<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 09.07.17
 * Time: 13:25
 */
class DBWorker implements IDBController
{
    private static function InitializeConstants()
    {
        self::$STUDENT_DB_STRUCTURES_JSON["RU"] = realpath(__DIR__."/../../")."/database_info/db_structure_ru.json";
        self::$STUDENT_DB_DICTIONARIES_ARRAYS["RU"] = realpath(__DIR__."/../../")."/database_info/dictionary_ru.php";
        self::$STAFF_COLUMNS_ARRAY_FILE = realpath(__DIR__."/../../")."/database_info/staff_columns.php";
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

    private static $STUDENT_DB_STRUCTURES_JSON = [];
    private static $STUDENT_DB_DICTIONARIES_ARRAYS = [];
    private static $STAFF_COLUMNS_ARRAY_FILE = "";
    const STUDENTS_MONTHLY_FEE = 650;
    const DISPLAY_OPTIONS_NAME_DELIM = "/";
    const GENERAL_QUERY_KEYWORDS_DELIM = "/[\s,]+/";
    const QUERY_HTML_NAME = "query";
    const ACTION_HTML_NAME = "action";
    const GENERAL_REQUEST_ACTION = "!!!full_info";

    private $DBConnection;
    private $studentDBStructure;
    private $language;
    private $dictionary;

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

        $this->studentDBStructure = $this->GetDBStructure();
    }

    private function GetDBStructure()
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
        $query_result = $this->DBConnection->Query($sql, $parameters);


        $table_names = $this->ShallowArrayByKey($query_result, "table_name");
        $database = [];
        $common_columns = null;

        foreach ($table_names as $table_name)
        {
            $query_result = $this->DBConnection->Query("SELECT column_name, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
                PDOMySQLConection::DATABASE, $table_name);
            $table = [];
            foreach ($query_result as $column)
            {
                $table[$column["column_name"]] =
                    ["translation" => $this->TranslateName($table_name, $column["column_name"])];
                if (strpos($column["column_type"], "enum(") === 0)
                {
                    $table[$column["column_name"]]["options"] = explode(',',
                        preg_replace("/(enum\(|'|\))/", '', $column["column_type"]));
                }
            }
            $database[$table_name] = ["translation" => $this->TranslateName($table_name),
                "entity" => $table];
            $query_result = $this->DBConnection->Query("select DISTINCT COLUMN_NAME 
            from information_schema.STATISTICS 
            where table_schema = ? 
            and table_name = ? 
            and index_type = ?;",
                PDOMySQLConection::DATABASE, $table_name, 'FULLTEXT');
            $database[$table_name]["fulltext"] = $this->ShallowArrayByKey($query_result, "COLUMN_NAME");
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
            $query_result = $this->DBConnection->Query($sql, $parameters);
            $unique_common_columns = $this->ShallowArrayByKey($query_result, "COLUMN_NAME");
            $database[$table_name]["unique"] = $unique_common_columns;
        }
        $database_structure = ["database" => $database, "common_columns" => $common_columns];
        $json = json_encode($database_structure, JSON_OBJECT_AS_ARRAY);
        file_put_contents($filename, $json);
        return $database_structure;
    }
    private function ShallowArrayByKey($array, $single_key)
    {
        $new_array = [];
        foreach ($array as $subarray)
        {
            array_push($new_array, $subarray[$single_key]);
        }
        return $new_array;
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
                            "<input type='checkbox' name='$table_name".self::DISPLAY_OPTIONS_NAME_DELIM."$column_name' checked". (isset($checkbox) ? " class='$checkbox'" : "") ."> ".
                            $column["translation"]."</label>" . "</div>";
                }
            }
            else
                $output_html .= $this->GetErrorMessage();
        }
        return $output_html;
    }
    public function ProceedGeneralRequest($post, $translate = false)
    {
        $query = "";
        $request_columns = ["single_row" => [], "multi_row" => []];
        foreach ($post as $key => $value)
        {
            if ($key == self::QUERY_HTML_NAME)
            {
                $query = $value;
                continue;
            }
            if ($key == self::ACTION_HTML_NAME)
                continue;
            $pieces = explode(self::DISPLAY_OPTIONS_NAME_DELIM, $key);
            $table = $pieces[0];
            $column = $pieces[1];

            if (!empty($this->studentDBStructure["database"][$table]["unique"]))
            {
                if (!key_exists($table, $request_columns["single_row"]))
                    $request_columns["single_row"][$table] = [];
                array_push($request_columns["single_row"][$table], $column);
            }
            else
            {
                if (!key_exists($table, $request_columns["multi_row"]))
                {
                    $request_columns["multi_row"][$table] = $this->studentDBStructure["common_columns"];
                }
                array_push($request_columns["multi_row"][$table], $column);
            }
        }
        if (count($request_columns["multi_row"]) === 0 && count($request_columns["single_row"]) === 0)
            return ["error" => $this->GetErrorMessage("no_rows")];

        $keywords = preg_split(self::GENERAL_QUERY_KEYWORDS_DELIM, $query, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0, $count = count($keywords) - 1; $i < $count; $i++)
        {
            $keywords[$i] = "+" . $keywords[$i];
        }
        $keywords[$count] = "+*" . $keywords[$i] . "*";
        $query = implode(" ", $keywords);

        // TODO: Test code below and add payments display
        // single_row query preparations
        $sql_pieces = ["SELECT ", " AS ", ", ", " FROM ", " JOIN ", " USING(", ")" ,
            " WHERE", " MATCH (", ") AGAINST (:query IN BOOLEAN MODE)", " OR", ";"];
        $is_first = true;

        foreach ($request_columns["single_row"] as $table => $columns)
        {
            if ($is_first)
            {
                $request_columns["single_row"][$table] = $this->GetTranslatedColumnString($this->studentDBStructure["common_columns"], $table) .
                    $sql_pieces[2];
                $is_first = false;
            }
            else
                $request_columns["single_row"][$table] = "";
            $request_columns["single_row"][$table] .= $this->GetTranslatedColumnString($columns, $table);
        }
        $multi_row_where_query = $single_row_query = $sql_pieces[0];
        $single_row_query .= implode($sql_pieces[2], $request_columns["single_row"]);
        $multi_row_where_query .= $this->studentDBStructure["common_columns"][0];

        $is_first = true;

        $common_columns = $this->GetColumnString($this->studentDBStructure["common_columns"]);

        foreach ($request_columns["single_row"] as $table => $columns_string)
        {
            if ($is_first)
            {
                $is_first = false;
                $from_table = $sql_pieces[3] . $table;
                $single_row_query .= $from_table;
                $multi_row_where_query .= $from_table;
                continue;
            }
            $appendant = $sql_pieces[4] . $this->EscapeTableIdentifier($table) .
                $sql_pieces[5] . $common_columns . $sql_pieces[6];
            $single_row_query .= $appendant;
            $multi_row_where_query .= $appendant;
        }

        $match_against_piece = $sql_pieces[7];
        $count = count($request_columns["single_row"]);
        $counter = 0;
        foreach ($request_columns["single_row"] as $table => $columns_string)
        {
            $fulltext_columns = $this->GetColumnString($this->studentDBStructure["database"][$table]["fulltext"], $table);
            $match_against_piece .= $sql_pieces[8] . $fulltext_columns . $sql_pieces[9];
            //array_push($single_row_parameters, $query);

            if ($counter < $count - 1)
                $match_against_piece .= $sql_pieces[10];
            $counter++;
        }
        $single_row_parameters = [":query" => $query];
        $single_row_query .= $match_against_piece . $sql_pieces[11];
        $multi_row_where_query .= $match_against_piece;

        // multi-row query preparations
        $sql_pieces = ["SELECT ", ", ", " FROM ", " WHERE ", " IN (", ") GROUP BY (", ");"];

        foreach ($request_columns["multi_row"] as $table => $columns)
        {
            $array = ["first" => $columns[0], "string" => $this -> GetTranslatedColumnString($columns, $table)];
            $request_columns["multi_row"][$table] = $array;
        }

        $multi_row_queries = [];
        foreach ($request_columns["multi_row"] as $table => $columns)
        {
            $temp_query = $sql_pieces[0] . $columns["string"] . $sql_pieces[2] . $table .
                $sql_pieces[3] . $columns["first"] . $sql_pieces[4] . $multi_row_where_query .
                $sql_pieces[5] . $common_columns . $sql_pieces[6];
            $temp_parameters = $single_row_parameters;
            $multi_row_queries[$table] = ["query" => $temp_query, "parameters" => $temp_parameters];
        }

        $db_answer = null;
        try
        {
            $db_answer = [];
            $query_result = $this->DBConnection->QueryWithBinding($single_row_query, $single_row_parameters);

            $db_answer["single_row"] = $query_result;
            $db_answer["multi_row"] = [];
            $this->DBConnection->SetPDOFetchMode(PDOMySQLConection::GET_GROUP_BY_FIRST_COLUMN);
            foreach ($multi_row_queries as $table => $query_info)
            {
                $query_result = $this->DBConnection->QueryWithBinding($query_info["query"], $query_info["parameters"]);
//                // TODO: Implement proper translation (below)
//
//                foreach ($query_result as $student => $rows)
//                {
//                    foreach ($rows as $row)
//                    {
//                        foreach ($row as $column => $value)
//                        {
//                            $row[$this->TranslateName($table, $column)] = $row[$column];
//                            unset($row[$column]);
//                        }
//                    }
//                }
//                $query_result[$this->TranslateName($table)] = $query_result;
//                unset($query_result[$table]);
                $translation = $this->studentDBStructure["database"][$table]["translation"];
                $db_answer["multi_row"][$translation] = $query_result;
            }
            $this->DBConnection->SetPDOFetchMode(PDOMySQLConection::GET_ASSOC);
        }
        catch (Exception $exception)
        {
            $db_answer = ["error" => $exception->getMessage(),
                "debug" => [
                    "exception" => $exception,
                    "single_row_query" => $single_row_query,
                    "single_row_parameters" => $single_row_parameters,
                    "multi_row_queries" => $multi_row_queries
                ]];
        }
        return $db_answer;
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
}
class DatabaseException extends Exception {}