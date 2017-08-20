<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 26.06.17
 * Time: 22:38
 */
ini_set("display_errors", true);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

require_once "constants.php";
require_once "functions.php";

spl_autoload_register(function ($class)
{
    include CLASSES_PATH . $class . '.php';
});

interface IDBConnection
{
    function QueryWithBinding($sql, $parameters);
    function Query($sql);
    function QueryNoResults($sql);
    function SetPDOFetchMode($pdo_constant);
    function BeginTransaction();
    function CommitTransaction();
    function RollbackTransaction();
}

interface IDBController
{
    function GetDBStructure();
    function GetHTMLSearchDisplayOptions();
    function GetHTMLAddNewForm();
    function AddNewStudent($form_data);
    function ProceedGeneralRequest($form_data);
    function ProceedTableRequest($form_data, $table_name);
    function ObfuscateColumnName($table, $column);
    function DeobfuscateColumnName($key);
    function GetLoginFormArray();
    function DumpAllRows($form_data, $table = "");
    function GetUpdateInputNames($table = null);
    function UpdateRow($form_data);
}

//dump(exec("id"));

session_start();
if (!in_array($_SERVER["REQUEST_METHOD"], ["GET", "POST"]))
    redirect("index.php");
elseif ($_SERVER["REQUEST_METHOD"] === "POST" && empty($_SESSION)
    && !preg_match("%^(/?" . RELATIVE_DOCUMENT_ROOT . "|/)?login\.php$%", $_SERVER["PHP_SELF"]))
{
    redirect("login.php");
}
elseif ($_SERVER["REQUEST_METHOD"] === "GET")
{
    $__php_self_test = "%^".REAL_DOCUMENT_ROOT."([A-Za-z0-9-_]+/)*[A-Za-z0-9-_]+\.php$%";
    $__get_page_test = "%^(/?" . RELATIVE_DOCUMENT_ROOT . "|/)?([A-Za-z0-9-_]+/)*[A-Za-z0-9-_]+\.php$%";
    $__index_test = "_^/?" . RELATIVE_DOCUMENT_ROOT . 'index\.php$_';
    $__errors_test = "%^/?" . RELATIVE_DOCUMENT_ROOT . 'errors\.php\?code=[0-9]{3}(&[a-z0-9]+=.+)*$%';
    if(preg_match($__php_self_test, $_SERVER["PHP_SELF"]))
    {
        $error_page = preg_match($__errors_test, $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"]);
        if (preg_match($__index_test, $_SERVER["PHP_SELF"]) || $error_page)
        {
            if(!(isset($_GET["page"]) && preg_match($__get_page_test, $_GET["page"])) &&
                !preg_match($__index_test, $_SERVER["REQUEST_URI"]) && !$error_page)
            {
                redirect("index.php");
            }
        }
        else
        {
            redirect("index.php?page={$_SERVER["PHP_SELF"]}");
        }
    }
    else
    {
        redirect("index.php");
    }
    if (empty($_SESSION) && !(preg_match($__index_test, $_SERVER["PHP_SELF"])
        && isset($_GET["page"]) && preg_match("%(/?".RELATIVE_DOCUMENT_ROOT.")?login.php%", $_GET["page"])))
    {
        redirect("login.php");
    }
}
$db_worker = DBWorker::GetInstance();
$css_classes_display_columns = [
    "checkbox_wrap" => "checkbox-inline btn",
    "fieldset" => "form-group",
    "checkbox" => "form-control"];
//dump($db_worker->GetDBStructure());
?>