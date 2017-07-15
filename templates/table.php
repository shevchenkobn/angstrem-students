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
        <label for="query"><h2>Таблица <?= $title?></h2></label>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" data-toggle="collapse" data-target="#parameters" class="btn btn-success btn-md">
                        <span class="glyphicon glyphicon-cog"></span>
                    </button>
                </div>
                <input type="text" class="form-control" name="<?= DBWorker::QUERY_HTML_NAME?>" id="query" placeholder="Введите запрос">
                <div class="input-group-btn">
                    <button type="submit" id="submitGetFullInfo" class="btn btn-info">
                        <span class="glyphicon glyphicon-search"></span> Найти
                    </button>
                </div>
            </div>
        </div>
        <div id="parameters" class="form-inline collapse" aria-expanded="false">
            <!--            <h3>Параметры поиска:</h3>-->
            <!--            --><?//= $search_parameters?>
            <h3>Отображать колонки:</h3>
            <?= $display_checkboxes?>
        </div>
    </div>
    <input type="hidden" name="<?= DBWorker::ACTION_HTML_NAME?>" value="get_full_info">
</form>
<?php if (isset($db_answer)): ?>
    <div id="relevantStudents">
        <?= $db_answer;?>
        <a href="index.php" id="clearTable" class="btn btn-success">Очистить</a>
    </div>
<?php endif; ?>
