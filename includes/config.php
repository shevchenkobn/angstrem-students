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
if ($_SERVER["REQUEST_METHOD"] === "GET" && empty($_SESSION) &&
    !($_SERVER['PHP_SELF'] === REAL_DOCUMENT_ROOT."index.php"
    && isset($_GET["page"]) && ($_GET["page"] === RELATIVE_DOCUMENT_ROOT."login.php" ||
    $_GET["page"] === "login.php")))
{
//    echo $_SERVER['PHP_SELF']." ".REAL_DOCUMENT_ROOT."index.php"."<br>".
//$_GET["page"]." ".RELATIVE_DOCUMENT_ROOT."login.php"."<br>";
//    var_dump($_SERVER);
//    var_dump(__FILE__);
//    echo '<a href="login.php">link</a>';
    redirect("login.php");
}
?>