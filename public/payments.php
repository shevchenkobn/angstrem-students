<?php
    require_once "../includes/config.php";
    if ($_SERVER["REQUEST_METHOD"] === "GET")
        redirect("index.php?page=payments.php");
    elseif ($_SERVER["REQUEST_METHOD"] === "POST")
    {
        render("main.php", ["title" => "Результаты",
            "db_answer" => $db_worker->ProceedTableRequest($_POST, "payments"),
            "display_checkboxes" => $db_worker->GetHTMLSearchDisplayOptions($css_classes_display_columns, "payments"),
            "table" => "payments"
        ]);
    }
    else
        redirect("index.php");
?>