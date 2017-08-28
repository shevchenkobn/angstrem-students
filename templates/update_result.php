<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 21.08.17
 * Time: 7:39
 */
?>
<div class="alert alert-<?= $result ? "info" : "danger"?> alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	<strong><?= $result ? "Данные об ученике изменены" : "Данные об ученике не изменены"?></strong>
</div>
<a class="btn btn-lg btn-block" href="index.php"><h1>На главную</h1></a>
