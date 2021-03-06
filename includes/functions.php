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

function get_update_form($update_names, $values, $submit_name, $submit_value)
{
	$placeholder_wrap = "%";
	static $form_template = null;
	if ($form_template === null)
	{
		$form_template = "<form method='post'>";
		foreach ($update_names as $column_name=>$name)
		{
			$placeholder = $placeholder_wrap . $name . $placeholder_wrap;
			$form_template .= "<input type='hidden' name='$name' value='$placeholder'>";
		}
		$form_template .= "<button type='submit' name='$submit_name' value='$submit_value' class='btn btn-sm btn-success'>" .
			"<span class='glyphicon glyphicon-pencil'></span>" .
			"</button>" .
			"</form>";
	}
	$form = $form_template;
	foreach ($update_names as $column_name=>$name)
	{
		$placeholder = $placeholder_wrap . $name . $placeholder_wrap;
		if (key_exists($column_name, $values))
			$form = str_replace($placeholder, $values[$column_name], $form);
		else
			$form = str_replace($placeholder, "", $form);
	}
	return $form;
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

function strpos_arr($haystack, $needles = [], $offset = 0)
{
    $positions = [];
    foreach ($needles as $needle)
    {
        $result = strpos($haystack, $needle, $offset);
        if ($result !== false)
            $positions[$needle] = $result;
    }
    return $positions;
}
?>