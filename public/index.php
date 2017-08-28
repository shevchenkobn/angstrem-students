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
//	dump($_POST);
    if (isset($_POST[DBWorker::ACTION_HTML_NAME]))
        switch ($_POST[DBWorker::ACTION_HTML_NAME])
        {
            case DBWorker::GENERAL_REQUEST_ACTION:
            	$render_array = [];
            	if (isset($_POST[DBWorker::TABLE_HTML_NAME]))
				{
					$table = $_POST[DBWorker::TABLE_HTML_NAME];
					$db_structure = $db_worker->GetDatabaseStructure();
					$table_name = $db_structure["database"][$table]["translation"];
					render("main.php", ["title" => "Результаты: $table_name",
						"db_answer" => $db_worker->ProceedTableRequest($_POST, $table),
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, $table),
						"table" => $table,
						"update_form_names" => $db_worker->GetUpdateInputNames($table)
					]);
				}
				else
				{
					$db_answer = $db_worker->ProceedGeneralRequest($_POST);
					render("main.php", ["db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns),
						"update_form_names" => $db_worker->GetUpdateInputNames()]);
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
					$db_answer = $db_worker->DumpAllRows($_POST, $table);
					$table_name = $db_worker->GetDatabaseStructure()["database"][$table]["translation"];
					render("main.php", ["title" => "Результаты: $table_name",
						"db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, $table),
						"table" => $table,
						"update_form_names" => $db_worker->GetUpdateInputNames($table)
					]);
				}
				else
				{
					$db_answer = $db_worker->DumpAllRows($_POST);
					$update_form_names = $db_worker->GetUpdateInputNames();
					render("main.php", ["db_answer" => $db_answer,
						"display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns),
						"update_form_names" => $update_form_names]);
				}
				break;
			case DBWorker::GET_UPDATE_FORM_ACTION:
				render("update_row.php", [
					'title' => "Изменить информацию об ученике",
					'form' => $db_worker->GetHTMLUpdateForm($_POST)
				]);
				break;
			case DBWorker::UPDATE_ROW_ACTION:
				$result = $db_worker->UpdateRow($_POST);
				if (is_array($result) && isset($result["error"]))
					$result = false;
				render("update_result.php", [
					"title" => "Изменение данных об ученике",
					"result" => $result
				]);
				break;
			case DBWorker::GET_ADD_ROW_FORM_ACTION:
				if (!isset($_POST[DBWorker::TABLE_HTML_NAME]))
					redirect("index.php");
				$table = $_POST[DBWorker::TABLE_HTML_NAME];
				$form = $db_worker->GetHTMLAddRowForm($table);
				$tranlation = $db_worker->GetDBStructure()["database"][$table]["translation"];
				render("add_new_row.php", ["form" => $form, "title" => "Добавить новую запись об ученике в таблицу «{$tranlation}»"]);
				break;
			case DBWorker::ADD_ROW_ACTION:
				dump($db_worker->AddNewRow($_POST));
				echo "<a href='index.php'>to main</a>";
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