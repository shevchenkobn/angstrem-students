<?php
require_once "../includes/config.php";
$css_classes_display_columns = [
    "checkbox_wrap" => "checkbox-inline btn",
    "fieldset" => "form-group",
    "checkbox" => "form-control"];
$db_worker = DBWorker::GetInstance();
if ($_SERVER["REQUEST_METHOD"] === "GET")
{
    if (isset($_GET["page"]))
    {
        $page = str_replace([RELATIVE_DOCUMENT_ROOT, "/"], "", $_GET["page"]);
        $tables = array_keys($db_worker->GetDatabaseStructure()["database"]);
        switch ($page) {
            case "login.php":
            case "logout.php":
                require $page;
                break;

            default:
                $table = str_replace(".php", "", $page);
                dump($tables, $table);
                if (in_array($table, $tables))
                {
                    $db_structure = $db_worker->GetDatabaseStructure();
                    $table_name = $db_structure["database"][$table]["translation"];
                    render("main.php", ["title" => "Таблица: $table_name",
                        "table" => $table,
                        "display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, $table)]);
                }
                else
                    redirect("index.php");
        }
    }
    else
    {
        render("main.php", ["title" => "Главная",
            "display_checkboxes" => DBWorker::GetInstance()->GetHTMLSearchDisplayOptions($css_classes_display_columns)]);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST")
{
    //dump($_POST);
    if (isset($_POST["action"]))
        switch ($_POST["action"])
        {
            case DBWorker::GENERAL_REQUEST_ACTION:
                $db_answer = $db_worker->ProceedGeneralRequest($_POST, true);
                render("main.php", ["db_answer" => $db_answer,
                    "display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns)]);
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