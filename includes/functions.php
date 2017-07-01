<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 26.06.17
 * Time: 22:32
 */
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
        echo "<a href='$protocol://$host$path/$destination'>$protocol://$host$path/$destination</a>";
        //header("Location: $protocol://$host$path/$destination");
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
function get_html_search_options($table_name = null)
{
    $output_html = "";
    if ($table_name === null)
    {
        $db_structure = translate_database(get_db_structure());
        foreach ($db_structure as $table_structure)
        {
            // generate html string to return encapsulating table in fieldset
        }
    }
    else
    {
        // generate html string to return
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
    $condition = function (&$column_info)
    {
        if (strpos($column_info["column_type"], "enum(") === 0)
        {
            $column_info["name"] = $column_info["column_name"];
            unset($column_info["column_name"]);
            $column_info["options"] = explode(',',
                preg_replace("/(enum\(|'|\))/", '', $column_info["column_type"]));
            unset($column_info["column_type"]);
            return false;
        }
        else
            return true;
    };
    $uintersect = function ($a, $b)
    {
        if (gettype($a) == "array")
            $a = json_encode($a);
        if (gettype($b) == "array")
            $b = json_encode($a);
        if (gettype($a) === "string" && gettype($b) === "string")
            return strcmp($a, $b);
        elseif ($a < $b)
            return -1;
        elseif ($a > $b)
            return 1;
        else
            return 0;
    };
    $uintersect(new stdClass(), "fucked up");
    foreach ($table_names as $name)
    {
        $query_result = students_db_query("SELECT column_name, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
            STUDENTS_DB_NAME, $name);
        $database[$name] = shallow_array($query_result, "column_name", $condition);
        if ($common_columns === null)
            $common_columns = $database[$name];
        else
        {
            $common_columns = array_uintersect($common_columns, $database[$name], $uintersect);
        }
    }
    $database_structure = ["database" => $database, "common_columns" => $common_columns];
    $json = json_encode($database_structure);
    file_put_contents($filename, $json);
    return $database_structure;
}
function shallow_array($array, $single_key, $comparator = null)
{
    $new_array = [];
    foreach ($array as $subarray)
    {
        if ($comparator !== null && is_callable($comparator) && !$comparator($subarray))
            array_push($new_array, $subarray);
        else
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
function translate_database($db_structure)
{
    foreach ($db_structure as $table_name=>$table)
    {
        $table_name = [$table_name => translate_name($table_name)];

    }
    return $db_structure;
}
function translate_table(&$table_structure, $table_name = null)
{
    if ($table_name === null)
    {
        foreach ($table_structure as $index => $column) {
            if (gettype($column) == "array") {
                $column["name"] = [$column["name"] => translate_name($column["name"])];
            }
            else
            {
                $column = [$column, translate_name($column)];
            }
        }
    }
    else
    {
        $table_struct_copy = $table_structure;
        translate_table($table_struct_copy);
        $table = [[$table_name => translate_name($table_name)] => $table_struct_copy];
        return $table;
    }
}
function translate_name($table, $column = null)
{
    $dictionary = require STUDENT_DB_DICTIONARY_PHP;
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