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
    && $_GET["page"] === RELATIVE_DOCUMENT_ROOT."login.php"))
{
    //echo '<a href="login.php">link</a>';
    redirect("login.php");
}
?>