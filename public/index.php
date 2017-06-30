<?php
require_once "../includes/config.php";
if ($_SERVER["REQUEST_METHOD"] === "GET")
{
    if (isset($_GET["page"]))
    {
        $page = str_replace(RELATIVE_DOCUMENT_ROOT, "", $_GET["page"]);
        switch ($page) {
            case "login.php":
            case "logout.php":
                require $page;
                break;
            default:
                redirect("index.php");
        }
    }
    else
    {
        render("main.php", ["title" => "Главная"]);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST")
{
    if (isset($_POST["action"]))
        switch ($_POST["action"])
        {
            case 1:
                break;
            default:
                redirect("index.php");
        }
    else
        redirect("index.php");
}
else
    redirect("index.php");
?>