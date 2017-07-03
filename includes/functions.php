<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 26.06.17
 * Time: 22:32
 */
function dump()
{
    echo "<pre>";
    foreach (func_get_args() as $var)
        var_dump($var);
    echo "</pre>";
}

function redirect($destination)
{
    if (preg_match("/^https?:\/\//", $destination))
    {
        header("Location: " . $destination);
    }
    else if (preg_match("/^\//", $destination))
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        header("Location: $protocol://$host$destination");
    }
    else
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
        //echo "<a href='$protocol://$host$path/$destination'>$protocol://$host$path/$destination</a>";
        header("Location: $protocol://$host$path/$destination");
    }
    exit;
}

function render($template, $values = [])
{
    if (file_exists("../templates/$template"))
    {
        extract($values);
        require("../templates/header.php");
        $templatePath = "../templates/$template";
        require($templatePath);
        require("../templates/footer.php");
    }
    else
    {
        trigger_error("Invalid template: $template", E_USER_ERROR);
    }
    exit;
}

/**
 * Functions to work with databases.
 * May be incapsulated into the class in future together with constants.
 */
function get_html_search_display_options($css_classes = [], $table_name = null, $db_structure = null, $except_columns = null)
{
    // $fieldset, $legend, $checkbox, $label, $checkbox_wrap
    extract($css_classes);
    $output_html = "";
    if ($db_structure === null)
        $db_structure = get_db_structure();
    if ($table_name === null)
    {
        $except_columns = [];
        $common_columns_written = false;
        $database = $db_structure["database"];

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
function proceed_index_page_request($query, $css_table_classes = null)
{
    $db_structure = get_db_structure();
}
function save_db_structure($filename = STUDENT_DB_STRUCTURE_JSON)
{
    $query_result = students_db_query("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?;",
        STUDENTS_DB_NAME);
    $table_names = shallow_array($query_result, "table_name");
    $database = [];
    $common_columns = null;
//    $condition = function (&$column_info)
//    {
//        $column_info["translation"] = translate_name($column_info["column_name"]);
//        unset($column_info["column_name"]);
//        if (strpos($column_info["column_type"], "enum(") === 0)
//        {
//            $column_info["options"] = explode(',',
//                preg_replace("/(enum\(|'|\))/", '', $column_info["column_type"]));
//            unset($column_info["column_type"]);
//        }
//        return false;
//    };
    foreach ($table_names as $table_name)
    {
        $query_result = students_db_query("SELECT column_name, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
            STUDENTS_DB_NAME, $table_name);
        $table = [];
        foreach ($query_result as $column)
        {
            $table[$column["column_name"]] =
                ["translation" => translate_name($table_name, $column["column_name"])];
            if (strpos($column["column_type"], "enum(") === 0)
            {
                $table[$column["column_name"]]["options"] = explode(',',
                    preg_replace("/(enum\(|'|\))/", '', $column["column_type"]));
            }
        }
        $database[$table_name] = ["translation" => translate_name($table_name),
            "entity" => $table];
        $query_result = students_db_query("select DISTINCT COLUMN_NAME 
            from information_schema.STATISTICS 
            where table_schema = ? 
            and table_name = ? 
            and index_type = ?;",
            STUDENTS_DB_NAME, $table_name, 'FULLTEXT');
        $database[$table_name]["fulltext"] = shallow_array($query_result, "COLUMN_NAME");
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
function shallow_array($array, $single_key)
{
    $new_array = [];
    foreach ($array as $subarray)
    {
        array_push($new_array, $subarray[$single_key]);
    }
    return $new_array;
}
function get_db_structure()
{
    $database_structure = null;
    if (!is_readable(STUDENT_DB_STRUCTURE_JSON))
    {
        $database_structure = json_decode(file_get_contents(STUDENT_DB_STRUCTURE_JSON));
    }
    else
    {
        $database_structure = save_db_structure(STUDENT_DB_STRUCTURE_JSON);
    }

    return $database_structure;
}

$dictionary = require STUDENT_DB_DICTIONARY_PHP;
function set_dictionary($filename)
{
    global $dictionary;
    $dictionary = require $filename;
}

function translate_name($table, $column = null)
{
    global $dictionary;
    $translation = $column === null ? $table : $column;
    if (key_exists($table, $dictionary))
        if ($column === null)
        {
            $translation = $dictionary[$table][0];
        }
        else
            if (key_exists($column, $dictionary[$table]))
                $translation = $dictionary[$table][$column];
    return $translation;
}

function staff_db_query(/*$sql [, ... ] */)
{
    static $handle;
    if (!isset($handle))
        $handle = create_pdo(STAFF_DB_SERVER, STAFF_DB_NAME,
            STAFF_DB_USER, STAFF_DB_PASS);
    return proceed_query($handle, func_get_arg(0), array_slice(func_get_args(), 1));
}
function students_db_query(/*$sql [, ... ] */)
{
    static $handle;
    if (!isset($handle))
        $handle = create_pdo(STUDENTS_DB_SERVER, STUDENTS_DB_NAME,
            STUDENTS_DB_USER, STUDENTS_DB_PASS);
    return proceed_query($handle, func_get_arg(0), array_slice(func_get_args(), 1));
}
function create_pdo($server, $database, $user, $pass)
{
    $handle = null;
    try
    {
        $handle = new PDO("mysql:dbname=" . $database . ";host=" . $server, $user, $pass);

        // ensure that PDO::prepare returns false when passed invalid SQL
        $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (Exception $e)
    {
        trigger_error($e->getMessage(), E_USER_ERROR);
        exit;
    }
    return $handle;
}
function proceed_query($handle, $sql, $parameters)
{
    $statement = $handle->prepare($sql);
    if ($statement === false)
    {
        trigger_error($handle->errorInfo()[2], E_USER_ERROR);
        exit;
    }
    $results = $statement->execute($parameters);

    if ($results !== false)
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        return false;
    }
}
?>