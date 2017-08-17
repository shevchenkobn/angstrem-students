<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 28.06.17
 * Time: 10:42
 */
require_once "../includes/config.php";
if (!empty($_SESSION))
    redirect("index.php");
else
{
    $render_arr_param = $db_worker->GetLoginFormArray();
    $render_arr_param["title"] = "Авторизация";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($_POST["action"] != "login")
        {
            $render_arr_param["errors"] = ["Неизвестная ошибка. Попробуйте снова."];
            render("login.php", $render_arr_param);
        }
        foreach ($_POST as $key => $value)
        {
            if (strpos($key, DBWorker::OBFUSCATOR_KEY_PREFIX) === 0)
            {
                $_POST[$db_worker->DeobfuscateColumnName($key)["column"]] = $value;
                unset($_POST[$key]);
            }
        }
        $errors = [];
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            array_push($errors, "Ошибка в логине.");
        if (strlen($_POST["password"]) > 72 || $_POST["password"] == "")
            array_push($errors, "Недопустимая длина пароля.");
        $user = PDOMySQLConection::GetInstance()->Query("SELECT * FROM `staff_users` WHERE `email`=?;", $email);
        if ($user === false)
            array_push($errors, "Внутренняя ошибка сервера. Попробуйте еще раз.");
        if (empty($user))
            array_push($errors, "Неправильно введен логин.");
        elseif (!password_verify($_POST["password"], $user[0]["password"]))
            array_push($errors, "Неправильно введен пароль.");
        if (count($errors) > 0)
        {
            $render_arr_param["errors"] = $errors;
            render("login.php", $render_arr_param);
        }
        else {
            $_SESSION["user"] = $user[0];
            redirect("index.php");
        }
    }
    else
    {
        render("login.php", $render_arr_param);
    }
}
?>