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
        <label for="query"><h2><?= isset($table) ? $title : "Получить информацию об ученике:"?></h2></label>
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
    <input type="hidden" name="<?= DBWorker::ACTION_HTML_NAME?>" value="<?= isset($table) ? $table : "get_full_info"?>">
</form>
<?php if (isset($db_answer)): ?>
<div id="relevantStudents">
    <h2>Результаты:</h2>
    <?php if (count($db_answer["single_row"])):
        foreach ($db_answer["single_row"] as $i => $student): ?>
            <h3>Ученик #<?= $i + 1?>:</h3>
            <table class="table table-responsive">
                <tr class="info">
                <?php
                foreach ($student as $column_name => $value)
                    echo "<th>$column_name</th>";
                ?>
                </tr>
                <tr>
                <?php
                foreach ($student as $column_name => $value)
                    echo "<td>$value</td>";
                ?>
                </tr>
            </table>
            <?php foreach ($db_answer["multi_row"] as $table => $students): ?>
                <h4>Таблица <?= $table?></h4>
                <table class="table table-responsive table-striped">
                    <tr class="info">

                    </tr>
                </table>
            <?php endforeach;?>
    <?php endforeach;
    elseif (true):
        foreach ($db_answer["multi_row"] as $table => $student): ?>

    <?php endforeach;
    endif; ?>
    <a href="index.php" id="clearTable" class="btn btn-success">Очистить</a>
</div>
<?php endif; ?>
