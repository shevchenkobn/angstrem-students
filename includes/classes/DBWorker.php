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
        self::$STUDENT_DB_STRUCTURES_JSON["RU"] = realpath(__DIR__."/../../database_info/db_structure.json");
        self::$STUDENT_DB_DICTIONARIES_ARRAYS["RU"] = realpath(__DIR__."/../../database_info/dictionary.php");
    }
    public static function GetInstance($language = "RU")
    {
        if (self::$instance)
        {
            self::InitializeConstants();
            self::$instance->instance = new DBWorker($language);
        }
        return self::$instance->instance;
    }
    private static $instance;

    private static $STUDENT_DB_STRUCTURES_JSON = [];
    private static $STUDENT_DB_DICTIONARIES_ARRAYS = [];
    const STUDENTS_MONTHLY_FEE = 650;

    private $studentDBConnection;
    private $staffDBConnection;
    private $studentDBStructure;
    private $language;
    private $dictionary;

    private function __construct($language)
    {
        if (!array_key_exists($language, self::$STUDENT_DB_DICTIONARIES_ARRAYS))
            throw new ErrorException("No filename for database structure.");
        if (!array_key_exists($language, self::$STUDENT_DB_STRUCTURES_JSON))
            throw new ErrorException("No dictionary for database structure.");
        $this->language = $language;


        $this->studentDBConnection = StudentsDBConnection::GetInstance();
        $this->staffDBConnection = StaffDBConnection::GetInstance();

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
        if (!is_readable($db_structure_filename))
        {
            $database_structure = json_decode(file_get_contents($db_structure_filename));
        }
        else
        {
            $database_structure = $this->CacheDBStructure($db_structure_filename);
        }

        return $database_structure;
    }
    private function CacheDBStructure($filename)
    {
        $query_result = $this->studentDBStructure->Query("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?;",
            STUDENTS_DB_NAME);
        $table_names = $this->ShallowArrayByKey($query_result, "table_name");
        $database = [];
        $common_columns = null;

        foreach ($table_names as $table_name)
        {
            $query_result = $this->studentDBStructure->Query("SELECT column_name, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
                StudentsDBConnection::DATABASE, $table_name);
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
            $query_result = $this->studentDBStructure->Query("select DISTINCT COLUMN_NAME 
            from information_schema.STATISTICS 
            where table_schema = ? 
            and table_name = ? 
            and index_type = ?;",
                StudentsDBConnection::DATABASE, $table_name, 'FULLTEXT');
            $database[$table_name]["fulltext"] = $this->ShallowArrayByKey($query_result, "COLUMN_NAME");
            if ($common_columns === null)
                $common_columns = array_keys($database[$table_name]["entity"]);
            else
            {
                $common_columns = array_intersect($common_columns, array_keys($database[$table_name]["entity"]));
            }
        }
        $database_structure = ["database" => $database, "common_columns" => $common_columns];
        $json = json_encode($database_structure);
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
        if ($this->dictionary)
            $this->SetDictionary();
        $translation = $column === null ? $table : $column;
        if (key_exists($table, $this->dictionary))
            if ($column === null)
            {
                $translation = $this->dictionary[$table][0];
            }
            else
                if (key_exists($column, $this->dictionary[$table]))
                    $translation = $this->dictionary[$table][$column];
        return $translation;
    }
    public function GetHTMLSearchDisplayOptions($css_classes = [], $table_name = null, $db_structure = null, $except_columns = null)
    {
        extract($css_classes);
        // $fieldset, $legend, $checkbox, $label, $checkbox_wrap
        $output_html = "";
        if ($this->studentDBStructure === null)
            $this->studentDBStructure = $this->GetDBStructure();
        if ($table_name === null)
        {
            $except_columns = [];
            $common_columns_written = false;
            $database = $this->studentDBStructure["database"];

            foreach ($database as $table_name=>$table_structure)
            {
                $output_html .= "<div><fieldset". (isset($fieldset) ? " class='$fieldset'" : ""). ">" .
                    "<legend". (isset($legend) ? " class='$legend'" : "") .">".$database[$table_name]["translation"]."</legend>".
                    get_html_search_display_options($css_classes, $table_name, $db_structure, $except_columns).
                    "</fieldset></div>";
                if ($common_columns_written === false)
                {
                    $except_columns = $db_structure["common_columns"];
                    $common_columns_written = true;
                }
            }
        }
        else
        {
            $database = $db_structure["database"];
            if (array_key_exists($table_name, $database))
            {
                $table = $database[$table_name]["entity"];
                foreach ($table as $column_name => $column)
                {
                    if (!in_array($column_name, $except_columns))
                        $output_html .= "<div". (isset($checkbox_wrap) ? " class='$checkbox_wrap'" : "") .">" .
                            "<label".(isset($label) ? " class='$label'" : "") .">".
                            "<input type='checkbox' name='$column_name' checked". (isset($checkbox) ? " class='$checkbox'" : "") ."> ".
                            $column["translation"]."</label>" . "</div>";
                }
            }
            else
                $output_html .= "Произошла ошибка";
        }
        return $output_html;
    }
    public function ProceedGeneralRequest($post)
    {
        // TODO: Implement ProceedGeneralRequest() method.
        return null;
    }
}