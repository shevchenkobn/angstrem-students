<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 04.08.17
 * Time: 10:07
 */
?>
<?php if (isset($result)):?>
    <div class="alert alert-<?= $result ? "info" : "danger"?> alert-dismissable fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong><?= $result ? "Ученик успешно добавлен" : "Ученик не добавлен"?></strong>
    </div>
<?php endif;?>
<h1>Добавить нового ученика:</h1>
<?= $form?>
