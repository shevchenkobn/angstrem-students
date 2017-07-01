<?php
require_once "../includes/config.php";
if ($_SERVER["REQUEST_METHOD"] === "GET")
{
    if (isset($_GET["page"]))
    {
        $page = str_replace([RELATIVE_DOCUMENT_ROOT, "/"], "", $_GET["page"]);
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
//        var_dump(students_db_query("SELECT COLUMN_NAME, column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?;",
//            STUDENTS_DB_NAME, "students"));
        render("main.php", ["title" => "Главная"]);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST")
{
    if (isset($_POST["action"]))
        switch ($_POST["action"])
        {
            case "get_full_info":
                $db_answer = proceed_index_page_request($_POST["query"]);
                render(["db_answer" => $db_answer]);
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