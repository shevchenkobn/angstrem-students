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
function save_db_structure($filename = STUDENT_DB_STRUCTURE_JSON)
{
    $query_result = students_db_query("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?;",
        STUDENTS_DB_NAME);
    $table_names = shallow_array($query_result, "table_name");

    $database = [];
    $primary_keys = null;
    foreach ($table_names as $name)
    {
        $query_result = students_db_query("SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
            STUDENTS_DB_NAME, $name);
        $database[$name] = shallow_array($query_result, "column_name");
        if ($primary_keys === null)
            $primary_keys = $database[$name];
        else
            $primary_keys = array_intersect($primary_keys, $database[$name]);
    }
    $database_structure = ["database" => $database, "primary_keys" => $primary_keys];
    $json = json_encode($database_structure);
    file_put_contents($filename, $json);
    return $database;
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

    // do some stuff with retrieved database structure
}
?>