<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 26.06.17
 * Time: 22:32
 */
function dump()
{
    echo "<pre>";
    foreach (func_get_args() as $var)
        var_dump($var);
    echo "</pre>";
}

function redirect($destination)
{
    if (preg_match("/^https?:\/\//", $destination))
    {
        header("Location: " . $destination);
    }
    else if (preg_match("/^\//", $destination))
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        header("Location: $protocol://$host$destination");
    }
    else
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
        //echo "<a href='$protocol://$host$path/$destination'>$protocol://$host$path/$destination</a>";
        header("Location: $protocol://$host$path/$destination");
    }
    exit;
}

function render($template, $values = [])
{
    if (file_exists("../templates/$template"))
    {
        extract($values);
        require("../templates/header.php");
        $templatePath = "../templates/$template";
        require($templatePath);
        require("../templates/footer.php");
    }
    else
    {
        trigger_error("Invalid template: $template", E_USER_ERROR);
    }
    exit;
}
?>