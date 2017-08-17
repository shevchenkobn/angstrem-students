<?php
require_once "../includes/config.php";
$tables = array_keys($db_worker->GetDatabaseStructure()["database"]);
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
            case "add_new.php":
                render("add_new.php", ["title" => "Добавить нового ученика", "form" => $db_worker->GetHTMLAddNewForm()]);
                break;

            default:
                $table = str_replace(".php", "", $page);
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
            "display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns)]);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST")
{
    if (isset($_POST[DBWorker::ACTION_HTML_NAME]))
        switch ($_POST[DBWorker::ACTION_HTML_NAME])
        {
            case DBWorker::GENERAL_REQUEST_ACTION:
            	if (isset($_POST[DBWorker::TABLE_HTML_NAME]))
				{
					$db_structure = $db_worker->GetDatabaseStructure();
					$table_name = $db_structure["database"][$table]["translation"];
					render("main.php", ["title" => "Результаты: $table_name",
						"db_answer" => $db_worker->ProceedTableRequest($_POST, $table),
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, "payments"),
						"table" => $table
					]);
				}
				else
				{
					$db_answer = $db_worker->ProceedGeneralRequest($_POST);
					render("main.php", ["db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns)]);
				}
                break;
            case DBWorker::ADD_NEW_ACTION:
                $result = $db_worker->AddNewStudent($_POST);
                render("add_new.php", ["title" => "Добавить нового ученика",
					"form" => $db_worker->GetHTMLAddNewForm(),
					"result" => $result
				]);
                break;
			case DBWorker::DUMP_ALL_ACTION:
				if (isset($_POST[DBWorker::TABLE_HTML_NAME]))
				{
					$table = $_POST[DBWorker::TABLE_HTML_NAME];
					$db_answer = $db_worker->DumpAllRows($table);
					$table_name = $db_worker->GetDatabaseStructure()["database"][$table]["translation"];
					render("main.php", ["title" => "Результаты: $table_name",
						"db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns),
						"table" => $table]);
				}
				else
				{
					$db_answer = $db_worker->DumpAllRows();
					render("main.php", ["db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns)]);
				}
				break;
            default:
                $table = $_POST[DBWorker::ACTION_HTML_NAME];
                if (in_array($table, $tables))
                {
                    $db_structure = $db_worker->GetDatabaseStructure();
                    $table_name = $db_structure["database"][$table]["translation"];
                    render("main.php", ["title" => "Результаты: $table_name",
                        "db_answer" => $db_worker->ProceedTableRequest($_POST, $table),
                        "display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, "payments"),
                        "table" => $table
                    ]);
                }
                else
                    redirect("index.php");
        }
    else
        redirect("index.php");
}
else
    redirect("index.php");
?>