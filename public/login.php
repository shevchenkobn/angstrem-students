<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 28.06.17
 * Time: 10:42
 */
require_once "../includes/config.php";
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    echo "got here";
}
else
    render("login.php", ["title" => "Авторизация"]);
?>