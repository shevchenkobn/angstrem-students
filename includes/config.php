<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 26.06.17
 * Time: 22:38
 */
ini_set("display_errors", true);
error_reporting(E_ALL);

require "constants.php";
require_once "functions.php";

session_start();
//exit;
if (!in_array($_SERVER["REQUEST_METHOD"], ["GET", "POST"]))
    redirect("index.php");
elseif ($_SERVER["REQUEST_METHOD"] === "GET")
{
    $__php_self_test = "%^".REAL_DOCUMENT_ROOT."([A-Za-z0-9-_]+/)*[A-Za-z0-9-_]+\.php$%";
    $__get_page_test = "%^(/?" . RELATIVE_DOCUMENT_ROOT . "|/)?([A-Za-z0-9-_]+/)*[A-Za-z0-9-_]+\.php$%";
    $__index_test = "_^/?" . RELATIVE_DOCUMENT_ROOT . 'index\.php$_';
    if(preg_match($__php_self_test, $_SERVER["PHP_SELF"]))
    {
        if (preg_match($__index_test, $_SERVER["PHP_SELF"]))
        {
            if(!(isset($_GET["page"]) && preg_match($__get_page_test, $_GET["page"])) &&
                !preg_match($__index_test, $_SERVER["REQUEST_URI"]))
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
        && isset($_GET["page"]) && $_GET["page"] === "login.php"))
    {
        redirect("login.php");
    }
}
?>