<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 09.07.17
 * Time: 13:25
 */

/**
 * Do not think about creating obfuscation for table names
 */
class DBWorker implements IDBController
{
    private static function InitializeConstants()
    {
    	if (empty(self::$STUDENT_DB_STRUCTURES_JSON["RU"]))
        	self::$STUDENT_DB_STRUCTURES_JSON["RU"] = realpath(__DIR__."/../../")."/database_info/db_structure_ru.json";
	
		if (empty(self::$STUDENT_DB_DICTIONARIES_ARRAYS["RU"]))
        	self::$STUDENT_DB_DICTIONARIES_ARRAYS["RU"] = realpath(__DIR__."/../../")."/database_info/dictionary_ru.php";
	
		if (empty(self::$STAFF_COLUMNS_ARRAY_FILE))
        	self::$STAFF_COLUMNS_ARRAY_FILE = realpath(__DIR__ . "/../../")."/database_info/staff_tables.php";
		if (empty(self::$COLUMN_OBFUSCATOR_FILE))
        	self::$COLUMN_OBFUSCATOR_FILE = realpath(__DIR__."/../../")."/database_info/column_obfuscator.json";
		if (empty(self::$TABLE_ORDER_FILE))
        	self::$TABLE_ORDER_FILE = realpath(__DIR__."/../../")."/database_info/table_order.php";
		if (empty(self::$ADD_NEW_STUDENT_COLUMNS))
        	self::$ADD_NEW_STUDENT_COLUMNS = realpath(__DIR__."/../../")."/database_info/add_new_tables.php";
			
		if (empty(self::$MULTI_ROW_SPECIAL_ADDING_TABLES))
        	self::$MULTI_ROW_SPECIAL_ADDING_TABLES = realpath(__DIR__."/../../")."/database_info/special_adding_tables.php";
    }
    public static function GetInstance($language = "RU")
    {
        if (!self::$instance)
        {
            self::InitializeConstants();
            self::$instance = new DBWorker($language);
        }
        return self::$instance;
    }
    private static $instance;

    public static $STUDENT_DB_STRUCTURES_JSON = [];
    public static $STUDENT_DB_DICTIONARIES_ARRAYS = [];
    public static $STAFF_COLUMNS_ARRAY_FILE = "";
    public static $COLUMN_OBFUSCATOR_FILE = "";
    public static $TABLE_ORDER_FILE = "";
    public static $ADD_NEW_STUDENT_COLUMNS = "";
    public static $MULTI_ROW_SPECIAL_ADDING_TABLES = "";
    
    const STUDENTS_MONTHLY_FEE = 650;
    
    const DISPLAY_OPTIONS_NAME_DELIM = "/";
    const GENERAL_QUERY_KEYWORDS_DELIM = "/[\s,]+/";
    
    const QUERY_HTML_NAME = "q";
    const ACTION_HTML_NAME = "a";
    const TABLE_HTML_NAME = "t";
    const SUM_INPUT_HTML_NAME = "s";
    
    const MATCH_AGAINST_PARAMETER = ":query";
    
    const GENERAL_REQUEST_ACTION = "full";
    const DUMP_ALL_ACTION = "all";
    const ADD_NEW_ACTION = "n";
    const GET_UPDATE_FORM_ACTION = "uf";
    const UPDATE_ROW_ACTION = "u";
    const GET_ADD_ROW_FORM_ACTION = "gar";
    const ADD_ROW_ACTION = "ar";
    
    const OBFUSCATOR_KEY_PREFIX = "_";
    
    const REQUEST_TYPE_GENERAL = "g";
    const REQUEST_TYPE_TABLE = "t";
    

    private $DBConnection;
    private $studentDBStructure;
    private $language;
    private $dictionary;
    private $obfuscationDictionary;
    private $tableOrderArray;
    private $joinAllTables;
    private $multiRowWhereQuery;
    private $addNewStudentTables;
    private $requestColumns;
    private $lastRequest;
    private $specialAddingTables;

    private $whereMatchAgainst = null;

    private function __construct($language)
    {
        $language = strtoupper($language);
        if (!array_key_exists($language, self::$STUDENT_DB_DICTIONARIES_ARRAYS))
            throw new ErrorException("No filename for database structure.");
        if (!array_key_exists($language, self::$STUDENT_DB_STRUCTURES_JSON))
            throw new ErrorException("No dictionary for database structure.");
        $this->language = $language;


        $this->DBConnection = PDOMySQLConection::GetInstance();

        $this->SetDictionary();

        if (is_readable(self::$TABLE_ORDER_FILE))
        	$this->tableOrderArray = require self::$TABLE_ORDER_FILE;
        else
        	$this->tableOrderArray = [];

        $this->studentDBStructure = $this->GetDBStructure();

        $this->obfuscationDictionary = $this->GetColumnObfuscator();
	
		if (is_readable(self::$ADD_NEW_STUDENT_COLUMNS))
			$this->addNewStudentTables = require self::$ADD_NEW_STUDENT_COLUMNS;
		else
			$this->tableOrderArray = [];
		
		$this->specialAddingTables = require self::$MULTI_ROW_SPECIAL_ADDING_TABLES;
    }

    public function GetDBStructure()
    {
        /**
         * Database structure is cached JSON entity of PHP associated array.
         * It's multidimensional. On first level it has two keys: "database" and "column_columns"
         * "column_columns" subarray contains names of columns met in every table.
         * "dictionary" subarray contains database structure itself.
         *
         */
        $database_structure = null;
        $db_structure_filename = self::$STUDENT_DB_STRUCTURES_JSON[$this->language];
        if (is_readable($db_structure_filename))
        {
            $database_structure = json_decode(file_get_contents($db_structure_filename), true);
        }
        else
        {
            $database_structure = $this->CacheDBStructure($db_structure_filename);
        }

        return $database_structure;
    }
    private function CacheDBStructure($filename)
    {
        $sql_pieces = ["SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?",
            " AND ", "table_name <> ?", ";"];
        $parameters = [PDOMySQLConection::DATABASE];
        $sql = $sql_pieces[0];
        if (is_readable(self::$STAFF_COLUMNS_ARRAY_FILE) && ($staff_columns = require self::$STAFF_COLUMNS_ARRAY_FILE))
        {
            $sql .= $sql_pieces[1];
            for ($i = 0, $count = count($staff_columns); $i < $count; $i++)
            {
                $sql .= $sql_pieces[2];
                array_push($parameters, $staff_columns[$i]);
                if ($i < $count - 1)
                    $sql .= $sql_pieces[1];
            }
        }
        $sql .= $sql_pieces[3];
        $this->DBConnection->SetPDOFetchMode(PDO::FETCH_COLUMN);
        $table_names = $this->DBConnection->Query($sql, $parameters);
        $this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);

        $database = [];
        $common_columns = null;
        foreach ($table_names as $table_name)
        {
            $query_result = $this->DBConnection->Query("SELECT column_name, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
                PDOMySQLConection::DATABASE, $table_name);
            $table = [];
            foreach ($query_result as $column)
            {
                $column_type = $column["column_type"];
                $array = ["translation" => $this->TranslateName($table_name, $column["column_name"])];
                if (strpos($column_type, "enum(") === 0)
                {
                    $array["options"] = explode(',',
                        preg_replace("/(enum\(|'|\))/", '', $column_type));
                }
                else
                {
                    $array["type"] = $column_type;
                }
                $table[$column["column_name"]] = $array;
            }
            $database[$table_name] = ["translation" => $this->TranslateName($table_name),
                "entity" => $table];
            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_COLUMN);
            $database[$table_name]["fulltext"] = $this->DBConnection->Query("select DISTINCT COLUMN_NAME 
            from information_schema.STATISTICS 
            where table_schema = ? 
            and table_name = ? 
            and index_type = ?;",
                PDOMySQLConection::DATABASE, $table_name, 'FULLTEXT');
            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
            if ($common_columns === null)
                $common_columns = array_keys($database[$table_name]["entity"]);
            else
            {
                $common_columns = array_intersect($common_columns, array_keys($database[$table_name]["entity"]));
            }
        }
        if (!count($common_columns))
            throw new DatabaseException("There is no relations between tables.");
        foreach ($database as $table_name => $table)
        {
            $sql_pieces = ["select DISTINCT COLUMN_NAME 
            from information_schema.STATISTICS 
            where table_schema = ? 
            and table_name = ?
            and (", "COLUMN_NAME = ?", " OR ", ") and NON_UNIQUE = ?;"];
            $sql = $sql_pieces[0];
            $parameters = [PDOMySQLConection::DATABASE, $table_name];
            $common_columns_number = count($common_columns);
            for ($i = 0; $i < $common_columns_number; $i++)
            {
                $sql .= $sql_pieces[1];
                array_push($parameters, $common_columns[$i]);
                if ($i < $common_columns_number - 1)
                    $sql .= $sql_pieces[2];
            }
            $sql .= $sql_pieces[3];
            array_push($parameters, "0");

            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_COLUMN);
            $unique_common_columns = $this->DBConnection->Query($sql, $parameters);
            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
            $database[$table_name]["unique"] = $unique_common_columns;
        }
        $this->OrderTableArray($database, true);
        $database_structure = ["database" => $database, "common_columns" => $common_columns];
        $json = json_encode($database_structure, JSON_OBJECT_AS_ARRAY);
        file_put_contents($filename, $json);
        return $database_structure;
    }

    private function SetDictionary()
    {
        $this->dictionary = require self::$STUDENT_DB_DICTIONARIES_ARRAYS[$this->language];
    }
    private function TranslateName($table, $column = null)
    {
        /**
         * Dictionary is two dimensional associated array.
         * First level keys are names of tables.
         * First level values are arrays where key is column name and [0] key is key for table_name translation.
         * Second level values are translations.
         */
        $table_dictionary = $this->dictionary["db_structure"];
        $translation = $column === null ? $table : $column;
        if (key_exists($table, $table_dictionary))
            if ($column === null)
            {
                $translation = $table_dictionary[$table][0];
            }
            else
                if (key_exists($column, $table_dictionary[$table]))
                    $translation = $table_dictionary[$table][$column];
        return $translation;
    }
    private function GetErrorMessage($error_key = 0)
    {
        $message = $error_key;
        if (key_exists($error_key, $this->dictionary["errors"]))
            $message = $this->dictionary["errors"][$error_key];
        return $message;
    }
    public function GetDatabaseStructure()
    {
        return $this->studentDBStructure;
    }
    public function GetHTMLSearchDisplayOptions($css_classes = [], $table_name = null)
    {
        extract($css_classes);
        // $fieldset, $legend, $checkbox, $label, $checkbox_wrap
        $output_html = "";
        if ($this->studentDBStructure)
            $this->studentDBStructure = $this->GetDBStructure();
        $database = $this->studentDBStructure["database"];
        if ($table_name === null)
        {
            foreach ($database as $table_name=>$table_structure)
            {
                $output_html .= "<div><fieldset". (isset($fieldset) ? " class='$fieldset'" : ""). ">" .
                    "<legend". (isset($legend) ? " class='$legend'" : "") .">".$database[$table_name]["translation"]."</legend>".
                    $this->GetHTMLSearchDisplayOptions($css_classes, $table_name).
                    "</fieldset></div>";
            }
        }
        else
        {
            if (array_key_exists($table_name, $database))
            {
                $table = $database[$table_name]["entity"];
                foreach ($table as $column_name => $column)
                {
                    if (!in_array($column_name, $this->studentDBStructure["common_columns"]))
                        $output_html .= "<div". (isset($checkbox_wrap) ? " class='$checkbox_wrap'" : "") .">" .
                            "<label".(isset($label) ? " class='$label'" : "") .">".
                            "<input type='checkbox' name='".$this->ObfuscateColumnName($table_name, $column_name)."' checked". (isset($checkbox) ? " '$checkbox'" : "") ."> ".
                            $column["translation"]."</label>" . "</div>";
                }
            }
            else
                $output_html .= $this->GetErrorMessage();
        }
        return $output_html;
    }
    public function ____($templates, $placeholders, $table_name = null)
    {
        $database = $this->studentDBStructure["database"];
        $html_options = "";
        if ($table_name !== null)
        {
            $wrapping = $templates["wrapping"];
            $table_wrap = $templates["table_wrap"];
            foreach ($database as $table_name=>$table_structure)
            {
                $html_options .= str_replace($placeholders["table"], $this->____($templates, $placeholders, $table_name), $table_wrap);
            }
            $html_options = str_replace($placeholders["tables"], $html_options, $wrapping);
        }
        else
        {
            $checkbox_html = "<input type='checkbox' name='{$placeholders["checkbox_name"]}' checked"
                .(isset($placeholders["checkbox_class"]) ? "class=".$placeholders["checkbox_class"] : "").">";
            $option_wrap = $templates["option_wrap"];
            if (array_key_exists($table_name, $database))
            {
                $table = $database[$table_name]["entity"];
                foreach ($table as $column_name => $column)
                {
                    if (!in_array($column_name, $this->studentDBStructure["common_columns"]))
                    {
                        $current_checkbox = str_replace($placeholders["checkbox_name"],
                            $this->ObfuscateColumnName($table_name, $column_name),
                            $checkbox_html);
                        if (isset($placeholders["checkbox_class"]))
                        $current_checkbox = str_replace($placeholders["checkbox_class"],
                            $placeholders[$placeholders["checkbox_class"]],
                            $current_checkbox);
                        $html_options .= str_replace($placeholders["checkbox"],
                            $current_checkbox,
                            $option_wrap);
                    }
                }
            }
            else
                $html_options .= isset($templates["error"]) ?
                        str_replace($placeholders["error"], $this->GetErrorMessage(), $templates["error"])
                        : $this->GetErrorMessage();
        }
        return $html_options;
    }
    public function ProceedGeneralRequest($form_data)
    {
        $request_columns = $this->GetRequestedColumnsArray($form_data);
		
        if (empty($request_columns["query"]))
        	return [];
        $query = $this->PrepareQuery($request_columns["query"]);

        $single_row_query = $this->GetGeneralSelectQuery($request_columns["single_row"], $query);
        

        // multi-row query preparations
        $multi_row_queries = [];
        foreach ($request_columns["multi_row"] as $table => $columns)
        {
            $multi_row_queries[$table] = $this->GetSeparateTableSelectQuery($columns, $table, $query);
        }

        $db_answer = null;
        try
        {
            $db_answer = [];
            $query_result = $this->DBConnection->QueryWithBinding($single_row_query["query"], $single_row_query["parameters"]);

            $db_answer["single_row"] = $query_result;
            $db_answer["multi_row"] = [];
            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
            foreach ($multi_row_queries as $table => $query_info)
            {
                $query_result = $this->DBConnection->QueryWithBinding($query_info["query"], $query_info["parameters"]);
                $translation = $this->studentDBStructure["database"][$table]["translation"];
                $db_answer["multi_row"][$translation] = $query_result;
            }
            $this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
        }
        catch (Exception $exception)
        {
            $db_answer = ["error" => $this->GetErrorMessage(), "exception" => $exception];
        }
        return $db_answer;
    }

    private function GetGeneralSelectQuery($request_columns, $query, $decorators = [])
    {
        $sql_pieces = ["SELECT ", ", ", ";"];
        $sql = $sql_pieces[0];
        $request_columns = $this->SingleRowRequestColumnsArraysToString($request_columns);
        $columns_string = implode($sql_pieces[1], $request_columns);
        $sql .= $columns_string;
        if ($query !== null)
        	$sql .= $this->GetSelectMatchAgainstPiece(self::MATCH_AGAINST_PARAMETER);
        else
        	$sql .= $this->GetJoinAllTables();
        if (!empty($decorators))
        {
            if (isset($decorators["general"]))
                foreach ($decorators["general"] as $decorator)
                {
                    $sql = $decorator($sql, $this->studentDBStructure);
                }
        }
        if ($query !== null)
			return [
				"query" => $sql . $sql_pieces[2],
				"parameters" => [self::MATCH_AGAINST_PARAMETER => $query]
			];
        else
        	return $sql . $sql_pieces[2];
    }

    private function GetSeparateTableSelectQuery($request_columns, $table_name, $query, $decorators = [])
    {
        $columns = $this->MultiRowColumnArrayToString($request_columns, $table_name);
        $sql_pieces = ["SELECT ", ", ", " FROM ", " WHERE ", " IN (", ")", ";"];
        $sql = $sql_pieces[0] . $columns["string"] . $sql_pieces[2] . $table_name .
            $sql_pieces[3] . $columns["first"] . $sql_pieces[4] . $this->GetMultiRowWhereQuery() . $sql_pieces[5];
        if (!empty($decorators))
        {
            if (isset($decorators["table"]))
                foreach ($decorators["table"] as $decorator)
                {
                    $sql = $decorator($sql, $this->studentDBStructure, $table_name);
                }
            if (isset($decorators["general"]))
                foreach ($decorators["general"] as $decorator)
                {
                    $sql = $decorator($sql, $this->studentDBStructure);
                }
        }
        $ret = [
            "query" => $sql . $sql_pieces[6],
            "parameters" => [self::MATCH_AGAINST_PARAMETER => $query]
        ];
        return $ret;
    }

    private function GetMultiRowWhereQuery()
    {
        if (!$this->multiRowWhereQuery)
            $this->multiRowWhereQuery = "SELECT " . $this->studentDBStructure["common_columns"][0] .
                $this->GetSelectMatchAgainstPiece();
        return $this->multiRowWhereQuery;
    }

    private function GetSelectMatchAgainstPiece($query = "")
    {
        return $this->GetJoinAllTables() . $this->GetWhereMatchAgainstPiece($query);
    }

    private function SetJoinAllTablesPiece()
    {
        $sql_pieces = [" FROM ", " JOIN ", " USING(", ")"];
        $common_columns = $this->GetColumnString($this->studentDBStructure["common_columns"]);

        $is_first = true;
        $this->joinAllTables = "";
        foreach ($this->studentDBStructure["database"] as $table => $table_info)
        {
            if (!empty($table_info["unique"]))
            {
                if ($is_first) {
                    $is_first = false;
                    $from_table = $sql_pieces[0] . $table;
                    $this->joinAllTables .= $from_table;
                    continue;
                }

                $appendant = $sql_pieces[1] . $this->EscapeTableIdentifier($table) .
                    $sql_pieces[2] . $common_columns . $sql_pieces[3];
                $this->joinAllTables .= $appendant;
            }
        }
    }

    private function SingleRowRequestColumnsArraysToString($request_columns, $delim = ", ")
    {
        $is_first = true;
        if (!count($request_columns))
        {
            reset($this->studentDBStructure["database"]);
            $first_table = key($this->studentDBStructure["database"]);
            $request_columns[$first_table] =
                $this->GetTranslatedColumnString($this->studentDBStructure["common_columns"], $first_table);
        }
        else
            foreach ($request_columns as $table => $columns)
            {
                if ($is_first)
                {
                    $request_columns[$table] = $this->GetTranslatedColumnString($this->studentDBStructure["common_columns"], $table) .
                        $delim;
                    $is_first = false;
                }
                else
                    $request_columns[$table] = "";
                $request_columns[$table] .= $this->GetTranslatedColumnString($columns, $table);
            }
        return $request_columns;
    }

    private function MultiRowColumnArrayToString($columns, $table)
    {
        return ["first" => $columns[0],
            "string" => $this->GetTranslatedColumnString($columns, $table)
        ];
    }

    private function GetTranslatedColumnString($columns, $table_name, $escape = true, $delimeters = [" AS ", ", "])
    {
        $column_string = "";
        for ($i = 0, $count = count($columns); $i < $count; $i++)
        {
            $translation = $this->studentDBStructure["database"][$table_name]["entity"][$columns[$i]]["translation"];
            $column_string .= ($escape ? $this->EscapeTableIdentifier($table_name) : $table_name) . "." .
                ($escape ? $this->EscapeTableIdentifier($columns[$i]) : $columns[$i]) .
                $delimeters[0] . ($escape ? $this->EscapeTableIdentifier($translation) : $translation);
            if ($i < $count - 1)
                $column_string .= $delimeters[1];
        }
        return $column_string;
    }
    private function GetColumnString($columns, $table_name = "", $escape = true, $delimeter = ", ")
    {
        $column_string = "";
        if (empty($table_name))
            for ($i = 0, $count = count($columns); $i < $count; $i++)
            {
                $column_string .= ($escape ? $this->EscapeTableIdentifier($columns[$i]) : $columns[$i]);
                if ($i < $count - 1)
                    $column_string .= $delimeter;
            }
        else
        {
            if ($escape)
                $table_name = $this->EscapeTableIdentifier($table_name);
            $table_name .= ".";
            for ($i = 0, $count = count($columns); $i < $count; $i++) {
                $column_string .= $table_name . ($escape ? $this->EscapeTableIdentifier($columns[$i]) : $columns[$i]);
                if ($i < $count - 1)
                    $column_string .= $delimeter;
            }
        }
        return $column_string;
    }

    private function EscapeTableIdentifier($name, $escape_char = "`")
    {
        return $escape_char . $name . $escape_char;
    }
    private function GetWhereMatchAgainstPiece($query_name = "")
    {
        if ($this->whereMatchAgainst)
        {
            if ($query_name !== "")
                return str_replace("?", $query_name, $this->whereMatchAgainst);
            return $this->whereMatchAgainst;
        }
        $sql_pieces = [" WHERE", " MATCH (", ") AGAINST (? IN BOOLEAN MODE)", " OR", ";"];
        $this->whereMatchAgainst = $sql_pieces[0];
        $db_structure = $this->studentDBStructure["database"];
        $first = true;
        foreach ($db_structure as $table_name => $table_info)
        {
            if (!empty($table_info["unique"]))
            {
                if (!$first)
                    $this->whereMatchAgainst .= $sql_pieces[3];
                else
                    $first = false;
                $fulltext_columns = $this->GetColumnString($db_structure[$table_name]["fulltext"], $table_name);

                $this->whereMatchAgainst .= $sql_pieces[1] . $fulltext_columns . $sql_pieces[2];
                //array_push($single_row_parameters, $query);
            }
        }
        if ($query_name !== "")
            return str_replace("?", $query_name, $this->whereMatchAgainst);
        return $this->whereMatchAgainst;
    }
    private function GetRequestedColumnsArray($form_data, $table = null)
    {
        $request_columns = [];
        if (!$table)
        {
        	$this->lastRequest = self::REQUEST_TYPE_GENERAL;
            $request_columns = ["single_row" => [], "multi_row" => []];
            foreach ($form_data as $key => $value) {
                if ($key == self::QUERY_HTML_NAME) {
                    $request_columns["query"] = $value;
                    continue;
                }
                if ($key == self::ACTION_HTML_NAME)
                    continue;
                $pieces = $this->DeobfuscateColumnName($key);
                $table = $pieces["table"];
                $column = $pieces["column"];

                if (!empty($this->studentDBStructure["database"][$table]["unique"])) {
                    if (!key_exists($table, $request_columns["single_row"]))
                        $request_columns["single_row"][$table] = [];
                    array_push($request_columns["single_row"][$table], $column);
                } else {
                    if (!key_exists($table, $request_columns["multi_row"])) {
                        $request_columns["multi_row"][$table] = $this->studentDBStructure["common_columns"];
                    }
                    array_push($request_columns["multi_row"][$table], $column);
                }
            }
            $this->OrderTableArray($request_columns["single_row"]);
            $this->OrderTableArray($request_columns["multi_row"]);
        }
        else
        {
        	$this->lastRequest = self::REQUEST_TYPE_TABLE;
            $table_type = $this->GetTableTypeByRowsNumber($table);

            $request_columns[$table_type] = $this->studentDBStructure["common_columns"];
            foreach ($form_data as $key => $value)
            {
                if ($key == self::QUERY_HTML_NAME)
                {
                    $request_columns["query"] = $value;
                    continue;
                }
                if ($key == self::ACTION_HTML_NAME)
                    continue;
                if ($key == self::TABLE_HTML_NAME)
                	continue;
                $continue = false;
                switch ($key)
                {
                    // TODO: some distinct features of particular table
                }
                if ($continue)
                    continue;
                array_push($request_columns[$table_type], $this->DeobfuscateColumnName($key)["column"]);
            }
        }
        $this->requestColumns = $request_columns;
        return $request_columns;
    }
    private function CreateColumnObfuscator()
    {
        $key = 'a';
        $obfuscation_array = [];
        $sql = "SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?;";
        $this->DBConnection->SetPDOFetchMode(PDO::FETCH_COLUMN);
        $table_names = $this->DBConnection->Query($sql, PDOMySQLConection::DATABASE);
        $sql = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;";
        foreach ($table_names as $table_name)
        {
            $column_names = $this->DBConnection->Query($sql, PDOMySQLConection::DATABASE, $table_name);
            foreach ($column_names as $column_name)
            {
                $obfuscation_array[self::OBFUSCATOR_KEY_PREFIX . $key] = $this->GetColumnFullName($table_name, $column_name);
                $key++;
            }
            $key++;
        }
        $this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
        $json = json_encode($obfuscation_array, JSON_OBJECT_AS_ARRAY);
        file_put_contents(self::$COLUMN_OBFUSCATOR_FILE, $json);
        return $obfuscation_array;
    }
    private function GetColumnObfuscator()
    {
        if (is_readable(self::$COLUMN_OBFUSCATOR_FILE))
        {
            return json_decode(file_get_contents(self::$COLUMN_OBFUSCATOR_FILE), true);
        }
        else
            return $this->CreateColumnObfuscator();
    }
    public function ObfuscateColumnName($table, $column)
    {
        $name = $this->GetColumnFullName($table, $column);
        if (($key = array_search($name, $this->obfuscationDictionary)) === false)
            return $name;
        return $key;
    }
    public function DeobfuscateColumnName($key)
    {
        if (!key_exists($key, $this->obfuscationDictionary))
            return $key;
        $temp_array = explode(self::DISPLAY_OPTIONS_NAME_DELIM, $this->obfuscationDictionary[$key]);
        return ["table" => $temp_array[0], "column" => $temp_array[1]];
    }
    private function GetColumnFullName($table_name, $column_name)
    {
        return $table_name . self::DISPLAY_OPTIONS_NAME_DELIM . $column_name;
    }
    private function PrepareQuery($string)
    {
        $keywords = preg_split(self::GENERAL_QUERY_KEYWORDS_DELIM, $string, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0, $count = count($keywords) - 1; $i < $count; $i++)
        {
            $keywords[$i] = '+' . $keywords[$i];
        }
        $keywords[$count] = '+*' . $keywords[$i] . '*';
        return implode(" ", $keywords);
    }
    private function OrderTableArray(&$array, $by_key = true)
    {
        $callback = function($a, $b)
        {
            if (($i = array_search($a, $this->tableOrderArray)) === false)
                $i = 1000000000;
            if (($j = array_search($b, $this->tableOrderArray)) === false)
                $j = 1000000000;
            return $i - $j;
        };
        if ($by_key)
            uksort($array, $callback);
        else
            usort($array, $callback);
    }
    public function ProceedTableRequest($form_data, $table_name)
    {
        $request_columns = $this->GetRequestedColumnsArray($form_data, $table_name);
        if (empty($request_columns["query"]))
        	return [];
        if (isset($request_columns["single_row"]))
            $table_type = "single_row";
        else
            $table_type = "multi_row";
        $query = $this->GetSeparateTableSelectQuery($request_columns[$table_type],
            $table_name,
            $this->PrepareQuery($request_columns["query"]),
            $this->CallbacksGenerator(["sort_by_common_asc"])
        );
        $db_answer = [];
        try
        {
            $db_answer = $this->DBConnection->QueryWithBinding($query["query"], $query["parameters"]);
        }
        catch (Exception $e)
        {
            $db_answer = ["error" => $this->GetErrorMessage(), "exception" => $e];
        }
        return $db_answer;
    }
    private function CallbacksGenerator($general = [], $table = [], $form_data = [])
    {
        $general_callbacks = [
            "sort_by_common_asc" => function($query, $db_structure)
            {
                $sql_pieces = [" ORDER BY ", ", "];
                $sql = $sql_pieces[0];
                $is_first = true;
                foreach ($db_structure["common_columns"] as $common_column)
                    if ($is_first)
                    {
                        $sql .= $common_column;
                        $is_first = false;
                    }
                    else
                        $sql .= $sql_pieces[1] . $common_column;
                return $query . $sql;
            }
        ];
        $table_callbacks = [

        ];
        $returned_callbacks = [];
        if (!empty($general))
            $returned_callbacks["general"] = [];
        foreach ($general as $key)
        {
            if (key_exists($key, $general_callbacks))
                array_push($returned_callbacks["general"], $general_callbacks[$key]);
        }
        if (!empty($table))
            $returned_callbacks["table"] = [];
        foreach ($table as $key)
        {
            if (key_exists($key, $table_callbacks))
                array_push($returned_callbacks["table"], $table_callbacks[$key]);
        }
        return $returned_callbacks;
    }
    public function GetHTMLAddNewForm()
    {
        $database = $this->studentDBStructure["database"];
        $html_output = "<div><form action='index.php' method='post'>";
        reset($database);
        $first_table_name = key($database);
        $first_table = $database[$first_table_name];
        foreach ($this->studentDBStructure["common_columns"] as $column)
        {
            $obfuscated_name = $this->ObfuscateColumnName($first_table_name, $column);
            $html_output .= "<div class=\"form-group\">
                    <label for='$obfuscated_name'>{$first_table["entity"][$column]["translation"]}</label>
                    <input type='text' class=\"form-control\" id='$obfuscated_name' name='$obfuscated_name'>
                </div>";
        }
        $html_output .= "<div class='form-inline'>";
        foreach ($database as $table_name => $table_info)
        {
            if (key_exists($table_name, $this->addNewStudentTables))
            {
                $html_output .= "<div><fieldset class='form-group'>".
                    "<legend>{$table_info["translation"]}</legend>";
                foreach ($table_info["entity"] as $column_name => $column_info)
                {
                    if (array_search($column_name, $this->studentDBStructure["common_columns"]) === false)
                    {
                        $obfuscated_name = $this->ObfuscateColumnName($table_name, $column_name);
                        if (!isset($column_info["options"]))
                        {
                            $type = "text";
                            if (strpos($column_info["type"], "int(") !== false)
                                $type = "number";
                            elseif (!empty(strpos_arr($column_info["type"], ["float(", "double(", "decimal("])))
                                $type = "number' step='0.01";
                            elseif (!empty($result = strpos_arr($column_info["type"], ["date", "time"])))
							{
								$type = "";
								if (isset($result["date"]))
									$type .= "date";
								if (isset($result["time"]))
									$type .= "time";
							}
                            $html_output .= "<div class=\"form-group\">
                                <label for='$obfuscated_name'>{$column_info["translation"]}</label>
                                <input type='$type' class=\"form-control\" id='$obfuscated_name' name='$obfuscated_name'>
                            </div>";
                        }
                        else
                        {
                            $html_output .= "<div class='form-group'>
                                <div class='select'><select class='selectpicker'  name='$obfuscated_name' id='$obfuscated_name' title='{$column_info["translation"]}'>";
                            foreach ($column_info["options"] as $option)
                                $html_output .= "<option value='$option'>$option</option>";
                            $html_output .= "</select></div></div>";
                        }
                    }
                }
                $html_output .= "</fieldset></div>";
            }
        }
        $html_output .= "</div><input type='hidden' name='".self::ACTION_HTML_NAME."' value='" . self::ADD_NEW_ACTION . "'>".
            "<button type='submit' class='btn btn-info'>Добавить</button>".
            "</form></div>";
        return $html_output;
    }

    public function AddNewStudent($form_data)
    {
        $insert_data = $this->GetChangeDatabaseArray($form_data);

        $queries = [];
        foreach ($insert_data["tables"] as $table_name => $columns)
        {
            $insert_values = array_merge($insert_data["common_columns"], $columns);
            array_push($queries, $this->GetInsertQuery($insert_values, $table_name));
        }
        return $this->ProceedTransactionQueries($queries);
    }
    private function GetChangeDatabaseArray($form_data, $insert_new = true)
    {
        $changing_tables = [];
        $common_columns = [];
        foreach($form_data as $key => $value)
        {
            if ($key == self::ACTION_HTML_NAME)
                continue;
			if ($key == self::TABLE_HTML_NAME)
				continue;
            $column_name = $this->DeobfuscateColumnName($key);
            if ($key == $this->DeobfuscateColumnName($key))
            	continue;
            if (!is_int(array_search($column_name["column"], $this->studentDBStructure["common_columns"])))
            {
                if (!key_exists($column_name["table"], $changing_tables))
                    $changing_tables[$column_name["table"]] = [];
                $changing_tables[$column_name["table"]][$column_name["column"]] = $value;
            }
            else
                $common_columns[$column_name["column"]] = $value;
        }
        if ($insert_new)
			foreach ($this->studentDBStructure["database"] as $table_name => $table_info)
			{
				if (!key_exists($table_name, $changing_tables) && $table_info["unique"])
				{
					$columns = array_diff($table_info["entity"], $this->studentDBStructure["common_columns"]);
					$changing_tables[$table_name] = array_fill_keys($columns, "");
				}
			}
		$changing_tables = ["tables" => $changing_tables, "common_columns" => $common_columns];
        $this->AddSpecialColumns($form_data, $changing_tables);
        return $changing_tables;
    }
    private function GetInsertQuery($insert_values, $table_name)
    {
        $sql_pieces = ["INSERT INTO ", " (",  ")", " VALUES", ";"];
        $query = $sql_pieces[0] . $table_name . $sql_pieces[1];
        $query .= $this->GetColumnString(array_keys($insert_values));
        $query .= $sql_pieces[2] . $sql_pieces[3] . $sql_pieces[1];
        $query .= str_repeat_delim("?", count($insert_values));
        $query .= $sql_pieces[2] . $sql_pieces[4];
        return ["query" => $query, "parameters" => array_values($insert_values)];
    }
    public function GetLoginFormArray()
    {
        return [
            "email" => $this->ObfuscateColumnName("staff_users", "email"),
            "password" => $this->ObfuscateColumnName("staff_users", "password")
        ];
    }
    private function GetJoinAllTables()
	{
		if (empty($this->joinAllTables))
			$this->SetJoinAllTablesPiece();
		return $this->joinAllTables;
	}
	public function DumpAllRows($form_data, $table = "")
	{
		$sql = "";
		$single_query = "";
		$multi_queries = [];
		$requested_columns = $this->GetRequestedColumnsArray($form_data, $table);
		if (empty($table))
		{
			$single_query = $this->GetGeneralSelectQuery($requested_columns["single_row"], null);
			foreach ($requested_columns["multi_row"] as $table_name => $columns)
				$multi_queries[$table_name] = $this->GetSimpleSelectAllQuery($columns, $table_name);
		}
		else
		{
			$table_type = $this->GetTableTypeByRowsNumber($table);
			$sql = $this->GetSimpleSelectAllQuery($requested_columns[$table_type], $table);
		}
//		dump($sql);
		$db_answer = null;
		try
		{
			if (empty($table))
			{
				$db_answer = [];
				$db_answer["single_row"] = $this->DBConnection->Query($single_query);
				$db_answer["multi_row"] = [];
				$this->DBConnection->SetPDOFetchMode(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
				foreach ($multi_queries as $table_name=> $query)
				{
					$translation = $this->studentDBStructure["database"][$table_name]["translation"];
					$db_answer["multi_row"][$translation] = $this->DBConnection->Query($query);
				}
				$this->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
				
			}
			else
				$db_answer = $this->DBConnection->Query($sql);
		}
		catch (Exception $exception)
		{
			$db_answer = ["error" => $this->GetErrorMessage(), "exception" => $exception];
		}
		return $db_answer;
	}
	private function GetSimpleSelectAllQuery($columns, $table)
	{
		$sql = "SELECT " . $this->GetTranslatedColumnString($columns, $table);
		$sql .= " FROM " . $this->EscapeTableIdentifier($table) . ";";
		return $sql;
	}
	public function GetUpdateInputNames($table = null)
	{
		$input_names = [];
		if (empty($this->requestColumns))
			$this->requestColumns = ['single_row' => [], 'multi_row' => []];
		if (empty($table))
		{
			foreach ($this->studentDBStructure["database"] as $table_name=>$table_info)
			{
				if (!empty($table_info["unique"]))
				{
					$input_names = array_merge($input_names, $this->GetUpdateInputNames($table_name));
				}
			}
		}
		else
		{
			$table_type = $this->GetTableTypeByRowsNumber($table);
			foreach ($this->studentDBStructure["database"][$table]["entity"] as $column_name => $column_info)
			{
				$obfuscation = $this->ObfuscateColumnName($table, $column_name);
				if ($this->lastRequest == self::REQUEST_TYPE_GENERAL && array_search($column_name, $this->requestColumns[$table_type][$table]) !== false ||
					$this->lastRequest == self::REQUEST_TYPE_TABLE && array_search($column_name, $this->requestColumns[$table_type]) !== false ||
					array_search($column_name, $this->studentDBStructure["common_columns"]) !== false)
				{
					$input_names[$column_info["translation"]] = $obfuscation;
				}
				else
				{
					array_push($input_names, $obfuscation);
				}
			}
		}
		return $input_names;
	}
	private function GetTableTypeByRowsNumber($table)
	{
		if (!empty($this->studentDBStructure["database"][$table]["unique"]))
			return "single_row";
		else
			return "multi_row";
	}
	public function UpdateRow($form_data)
	{
		$update_columns = $this->GetUpdateRowValuesArray($form_data);
		if ($update_columns === false)
			return ["error" => $this->GetErrorMessage("no_where_piece")];
		$queries = [];
		foreach ($update_columns["update_tables"] as $table => $columns)
			array_push($queries, $this->GetUpdateRowQuery($columns, $table, $update_columns["common_columns"]));
		$result = $this->ProceedTransactionQueries($queries);
		if ($result === false || $result !== true && key_exists("error", $result))
			return $result;
		$repair_query = $this->GetRepairTablesQuery(array_keys($update_columns["update_tables"]));
		return $this->ProceedTransactionQuery($repair_query);
	}
	private function GetUpdateRowValuesArray($form_data)
	{
		$update_columns = [];
		$common_columns = [];
		foreach ($form_data as $name => $value)
		{
			if ($name === self::ACTION_HTML_NAME)
				continue;
			$column_name = $this->DeobfuscateColumnName($name);
			if (array_search($column_name["column"], $this->studentDBStructure["common_columns"]) !== false)
				$common_columns[$column_name["column"]] = $value;
			else
			{
				if (!key_exists($column_name["table"], $update_columns))
					$update_columns[$column_name["table"]] = [];
				$update_columns[$column_name["table"]][$column_name["column"]] = $value;
			}
		}
		if (!empty($common_columns))
			return ["update_tables" => $update_columns, "common_columns" => $common_columns];
		else
			return false;
	}
	private function GetUpdateRowQuery($columns, $table_name, $common_columns)
	{
		$sql_pieces = ["UPDATE ", " SET ", ";"];
		$query = $sql_pieces[0] . $this->EscapeTableIdentifier($table_name) . $sql_pieces[1];
		$query .= $this->GetUpdateColumnsString($columns);
		$query .= $this->GetUpdateRowWherePiece($common_columns);
		$parameters = array_merge(array_values($columns), array_values($common_columns));
		return ["query" => $query . $sql_pieces[2], "parameters" => $parameters];
	}
	private function GetUpdateColumnsString($columns)
	{
		$sql_pieces = [" = ", "?", ", "];
		$string = "";
		$is_first = true;
		foreach ($columns as $name => $value)
		{
			if (!$is_first)
				$string .= $sql_pieces[2];
			else
				$is_first = false;
			$string .= $this->EscapeTableIdentifier($name) . $sql_pieces[0] . $sql_pieces[1];
		}
		return $string;
	}
	private function GetUpdateRowWherePiece($common_columns)
	{
		$sql_pieces = [" WHERE ", " = ", "?", " AND "];
		$string = $sql_pieces[0];
		$is_first = true;
		foreach ($common_columns as $name => $value)
		{
			if (!$is_first)
				$string .= $sql_pieces[3];
			else
				$is_first = false;
			$string .= $this->EscapeTableIdentifier($name) . $sql_pieces[1] . $sql_pieces[2];
		}
		return $string;
	}
	private function ProceedTransactionQueries($queries)
	{
		try
		{
			$this->DBConnection->BeginTransaction();
			$success = false;
			foreach ($queries as $query)
			{
				$success = $this->DBConnection->QueryNoResults($query["query"], $query["parameters"]);
				if (!$success)
				{
					break;
				}
			}
			$result = true;
			if (!$success)
			{
				$this->DBConnection->RollbackTransaction();
				$result = false;
			}
			else
				$this->DBConnection->CommitTransaction();
			return $result;
		}
		catch (Exception $exception)
		{
			return ["error" => $this->GetErrorMessage(), "exception" => $exception];
		}
	}
	private function ProceedTransactionQuery($query, $parameters = [])
	{
		try
		{
			$this->DBConnection->BeginTransaction();
			$success = $this->DBConnection->QueryNoResults($query, $parameters);
			$result = true;
			if (!$success)
			{
				$this->DBConnection->RollbackTransaction();
				$result = false;
			}
			else
				$this->DBConnection->CommitTransaction();
			return $result;
		}
		catch (Exception $exception)
		{
			return ["error" => $this->GetErrorMessage(), "exception" => $exception];
		}
	}
	private function GetRepairTablesQuery($tables, $quick = true)
	{
		$sql_pieces = ["REPAIR TABLE ", " QUICK", ";"];
		$query = $sql_pieces[0] . $this->GetColumnString($tables);
		if ($quick)
			$query .= $sql_pieces[1];
		return $query . $sql_pieces[2];
	}
	public function GetHTMLUpdateForm($row_data)
	{
		$common_columns = "";
		$inputs = "<div class='form-inline'>";
		foreach ($row_data as $name => $value)
		{
			if ($name === self::ACTION_HTML_NAME)
				continue;
			$column_name = $this->DeobfuscateColumnName($name);
			$column = $column_name["column"];
			$table = $column_name["table"];
			if (array_search($column, $this->studentDBStructure["common_columns"]) !== false)
			{
				$common_columns .= "<div class='form-group'>" .
					"<label for='$name'>{$this->studentDBStructure["database"][$table]["entity"][$column]["translation"]}</label>" .
					"<input type='text' class='form-control' name='$name' value='$value' readonly>".
					"</div>";
			}
			else
			{
				$column_info = $this->studentDBStructure["database"][$table]["entity"][$column];
				if (!isset($column_info["options"]))
				{
					$type = "text";
					if (strpos($column_info["type"], "int(") !== false)
						$type = "number";
					elseif (!empty(strpos_arr($column_info["type"], ["float(", "double(", "decimal("])))
						$type = "number' step='0.01";
					elseif (!empty($result = strpos_arr($column_info["type"], ["date", "time"])))
					{
						$type = "";
						if (isset($result["date"]))
							$type .= "date";
						if (isset($result["time"]))
							$type .= "time";
					}
					$inputs .= "<div class='form-group'>" .
						"<label for='$name'>{$this->studentDBStructure["database"][$table]["entity"][$column]["translation"]}</label>" .
						"<input type='$type' class='form-control' name='$name' value='$value'>".
						"</div>";
				}
				else
				{
					$inputs .= "<div class='form-group'>" .
						"<label for='$name'>{$this->studentDBStructure["database"][$table]["entity"][$column]["translation"]}</label>" .
						"<div class='select'><select class='selectpicker' name='$name' id='$name' title='{$column_info["translation"]}'>";
					foreach ($column_info["options"] as $option)
						$inputs .= "<option value='$option'". ($option === $value ? " selected" : "") .">$option</option>";
					$inputs .= "</select></div></div>";
				}
			}
		}
		if (empty($common_columns))
			return "<div class=\"alert alert-danger\">
				<strong>Ученик для изменения не определен</strong>
			</div>";
		else
		{
			$inputs .= "</div>";
			$submit = "<button class='btn btn-block btn-success' type='submit' name='" . self::ACTION_HTML_NAME . "' value='" . self::UPDATE_ROW_ACTION . "'>Изменить</button>";
			return "<form action='index.php' method='post'>" . $common_columns . $inputs . $submit . "</form>";
		}
	}
	public function GetHTMLAddRowForm($table)
	{
		if (!empty($this->studentDBStructure["database"][$table]["unique"]))
			return "";
		$html_form = "<form method='post' action='index.php'><div class='form-inline'>";
		$is_special_table = key_exists($table, $this->specialAddingTables);
		$has_excluded_columns = false;
		if ($is_special_table)
			$has_excluded_columns = key_exists("exclude", $this->specialAddingTables[$table]);
		foreach ($this->studentDBStructure["database"][$table]["entity"] as $column_name => $column_info)
		{
			if ($has_excluded_columns && array_search($column_name, $this->specialAddingTables[$table]["exclude"]) !== false)
				continue;
			$obfuscated_name = $this->ObfuscateColumnName($table, $column_name);
			if (!isset($column_info["options"]))
			{
				$type = "text";
				if (strpos($column_info["type"], "int(") !== false)
					$type = "number";
				elseif (!empty(strpos_arr($column_info["type"], ["float(", "double(", "decimal("])))
					$type = "number' step='0.01";
				elseif (!empty($result = strpos_arr($column_info["type"], ["date", "time"])))
				{
					if (isset($result["date"]) && isset($result["time"]) || $column_info["type"] === "timestamp")
						$type = "datetime-local";
					else
						$type = "date";
				}
				$html_form .= "<div class=\"form-group\">
                                <label for='$obfuscated_name'>{$column_info["translation"]}</label>
                                <input type='$type' class=\"form-control\" id='$obfuscated_name' name='$obfuscated_name'>
                            </div>";
			}
			else
			{
				$html_form .= "<div class='form-group'>
                                <div class='select'><select class='selectpicker'  name='$obfuscated_name' id='$obfuscated_name' title='{$column_info["translation"]}'>";
				foreach ($column_info["options"] as $option)
					$html_form .= "<option value='$option'>$option</option>";
				$html_form .= "</select></div></div>";
			}
		}
		$html_form .= $this->GetSpecialInputFields($table);
		$html_form .= "<input type='hidden' name='" . self::TABLE_HTML_NAME . "' value='$table'>" .
			"<input type='hidden' name='" . self::ACTION_HTML_NAME . "' value='" . self::ADD_ROW_ACTION . "'>".
			"<button type='submit' class='btn btn-block btn-success'>Добавить</buttontype>" .
			"</div></form>";
		return $html_form;
	}
	public function AddNewRow($form_data)
	{
		$change_columns = $this->GetChangeDatabaseArray($form_data, false);
		if (count($change_columns["tables"]) === 0)
			return false;
		$queries = $this->GetInsertNewRowQueries($change_columns);
		return $this->ProceedTransactionQueries($queries);
	}
	private function GetSpecialInputFields($table)
	{
		$functions = [
			"parents" => function(DBWorker $self) {
				return "<input type='number' step='0.01' name='" . $self::SUM_INPUT_HTML_NAME . "'/>";
			}
		];
		if (!key_exists($table, $functions))
			return "";
		return $functions[$table]($this);
	}
	private function AddSpecialColumns($form_data, &$change_columns)
	{
		$functions = [
			"parents" => function(DBWorker $self, $form_data, &$change_columns) {
				$key = $self::SUM_INPUT_HTML_NAME;
				$change_columns["parents"][$key] = $form_data[$key];
			}
		];
		foreach ($change_columns as $table=>$columns_array)
		{
			if (key_exists($table, $functions))
				$functions[$table]($this, $form_data, $change_columns);
		}
	}
	private function GetInsertNewRowQueries($change_columns)
	{
		$queries = [];
		
		foreach ($change_columns["tables"] as $table_name => $columns)
		{
			if (key_exists($table_name, $this->specialAddingTables))
			{
				$this->InsertRowPerformCalculations($queries, $change_columns, $table_name);
			}
			else
			{
				$insert_values = array_merge($change_columns["common_columns"], $columns);
				array_push($queries, $this->GetInsertQuery($insert_values, $table_name));
			}
		}
		return $queries;
	}
	private function InsertRowPerformCalculations(&$queries, $change_columns, $table_name)
	{
		$functions = [
			"parents" => function(DBWorker $self, &$queries, $change_columns) {
				
				$sum_key = $self::SUM_INPUT_HTML_NAME;
				$sum = $change_columns["parents"][$sum_key];
				
				$months_paid = floor($sum / $self::STUDENTS_MONTHLY_FEE);
				
				$where_piece = $self::GetUpdateRowWherePiece($change_columns["common_columns"]);
				$common_columns_values = array_values($change_columns["common_columns"]);
				
				if ($months_paid > 0)
				{
					$self->DBConnection->SetPDOFetchMode(PDO::FETCH_COLUMN);
					$last_paid_date = $self->DBConnection->Query("SELECT MAX(`end_period`) FROM `payments`" . $where_piece . ";",
						$common_columns_values);
					if (!empty($last_paid_date))
						$last_paid_date = $last_paid_date[0];
					else
					{
						$last_paid_date = $self->DBConnection->Query("SELECT `learning_start` FROM `contracts_info`" . $where_piece . ";",
							$common_columns_values);
						
						// for case if learning_start is not specified
						if ($last_paid_date[0] !== "0000-00-00")
							$last_paid_date = $last_paid_date[0];
						else
							$last_paid_date = 0;
						$change_columns["payments"]['start_period'] = $last_paid_date;
						$change_columns["payments"]['period'] = $months_paid;
						$payment_timestamp = $change_columns["payments"]['payment_timestamp'];
						$query = "INSERT INTO `payments` (`contract_number`,";
						if (!empty($payment_timestamp))
							$query .= " `payment_timestamp`,";
						$query .= " `start_period`, `end_period`, `payment_system`) VALUES (:contract_number,";
						if (!empty($payment_timestamp))
							$query .= " :payment_timestamp,";
						$query .= " :start_period, DATE_ADD(:start_period, INTERVAL :period MONTH), :payment_system);";
						
						array_push($queries, ["query" => $query, "parameters" => array_merge($change_columns["common_columns"], $change_columns["payments"])]);
						
					}
					$self->DBConnection->SetPDOFetchMode(PDO::FETCH_ASSOC);
				}
				$update_contract_info = ["query" => "UPDATE `payments` SET `paid_sum` = `paid_sum` + ?" . $where_piece . ";", "parameters" => array_merge([$sum], $common_columns_values)];
				array_push($queries, $update_contract_info);
			}
		];
		if (key_exists($table_name, $functions))
			$functions[$table_name]($this, $queries, $change_columns);
	}
}
class DatabaseStructureException extends Exception {}