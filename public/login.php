<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 28.06.17
 * Time: 10:42
 */
require_once "../includes/config.php";
if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    if ($_POST["action"] != "login")
        render("login.php", ["title" => "Авторизация", "errors" => ["Неизвестная ошибка. Попробуйте снова."]]);
    $errors = [];
    if (!filter_var($_POST["login"], FILTER_VALIDATE_EMAIL))
        array_push($errors, "Ошибка в логине.");
    if (strlen($_POST["password"]) > 72 || $_POST["password"] == "")
        array_push($errors, "Недопустимая длина пароля.");
    $user = staff_db_query("SELECT * FROM `users` WHERE `email`=?;", $_POST["login"]);
    if ($user === false)
        array_push($errors, "Внутренняя ошибка сервера. Попробуйте еще раз.");
    if (empty($user))
        array_push($errors, "Неправильно введен логин.");
    elseif (!password_verify($_POST["password"], $user[0]["password"]))
        array_push($errors, "Неправильно введен пароль.");
    if (count($errors) > 0)
        render("login.php", ["title" => "Авторизация", "errors" => $errors]);
    else
    {
        $_SESSION["user"] = $user[0];
        redirect("index.php");
    }
}
elseif (!empty($_SESSION))
    redirect("index.php");
else
    render("login.php", ["title" => "Авторизация"]);
?>