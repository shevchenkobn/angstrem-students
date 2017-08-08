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
function dump_to_string()
{
    ob_start();
    echo "<pre>";
    foreach (func_get_args() as $var)
        var_dump($var);
    echo "</pre>";
    return ob_get_clean();
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
//        echo "<a href='$protocol://$host$path/$destination'>$protocol://$host$path/$destination</a>";
//        dump($_SERVER);
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

function array_swap(&$array, $swap_a, $swap_b = 0){
    $temp = $array[$swap_a];
    $array[$swap_a] = $array[$swap_b];
    $array[$swap_b] = $temp;
}

function str_repeat_delim($string, $multiplier, $delim = ", ")
{
    $result = $string;
    for ($i = 1; $i < $multiplier; $i++)
    {
        $result .= $delim . $string;
    }
    return $result;
}
?>