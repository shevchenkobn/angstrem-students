<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 29.06.17
 * Time: 0:07
 */
?>
<form method="post" action="<?= REAL_DOCUMENT_ROOT?>index.php">
    <div class="form-group">

    </div>
    <div class="form-inline">
    <?php

    ?>
    </div>
    <input type="hidden" name="action" value="get-full-info">
    <button type="submit" id="submitGetFullInfo">
        <span class="glyphicon glyphicon-user"></span> Найти
    </button>
</form>
<?php if (isset($db_answer)): ?>
<div id="relevantStudents">
    <?= $db_answer;?>
    <a href="index.php" id="clearTable" class="btn btn-success">Очистить</a>
</div>
<?php endif; ?>
