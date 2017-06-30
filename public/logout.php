<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 28.06.17
 * Time: 10:42
 */
if (!empty($_SESSION))
{
    unset($_SESSION);
    session_destroy();
}
redirect("index.php");
?>