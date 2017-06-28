<?php
require_once "../includes/config.php";
if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    $page = str_replace(RELATIVE_DOCUMENT_ROOT, "", $_GET["page"]);
    switch ($page)
    {
        case "login.php":
            require $page;
//            echo '<a href="login.php">text</a></br>';
//            var_dump($_POST);
            break;
        default:
            if ($page !== "")
                redirect("index.php");
    }
}
else
    redirect("index.php");
?>