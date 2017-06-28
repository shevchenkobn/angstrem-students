<?php
require_once "../includes/config.php";
switch ($_GET["code"])
{
    case "404":
        render("404.php", ["title" => "Страница не найдена", "page" => $_SERVER["HTTP_HOST"]."/".$_GET["page"]]);
        break;
    default:
        redirect("index.php");
}
?>