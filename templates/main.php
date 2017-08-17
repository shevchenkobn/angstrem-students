<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 29.06.17
 * Time: 0:07
 */
?>
<form method="post" action="<?= REAL_DOCUMENT_ROOT . "index"?>.php">
    <div class="form-group">
        <div class="row">
            <div class="col-sm-7">
                <label for="query"><h2><?= isset($table) ? $title : "Быстрый поиск:"?></h2></label>
            </div>
            <div class="col-sm-5" style="height: 100%; vertical-align: middle;">
                <div class="row">
                    <?php
                        $non_unique = isset($table) && empty(DBWorker::GetInstance()->GetDBStructure()["database"][$table]["unique"]);
                    ?>
                    <div class="col-sm-<?= $non_unique ? 6 : 12?>">
                        <form method="post">
                            <input type="hidden" name="<?= DBWorker::ACTION_HTML_NAME?>" value="<?= DBWorker::DUMP_ALL_ACTION?>">
                            <?php if (isset($table)): ?>
                                <input type="hidden" name="<?= DBWorker::TABLE_HTML_NAME?>" value="<?= $table?>">
                            <?php endif;?>
                            <button type="submit" class="btn btn-block btn-info" style="margin: 25px 0">Показать всю таблицу</button>
                        </form>
                    </div>
                    <?php if ($non_unique): ?>
                    <div class="col-sm-6">
                        <form method="post">
                            <input type="hidden" name="<?= DBWorker::ACTION_HTML_NAME?>" value="<?= DBWorker::DUMP_ALL_ACTION?>">
                            <button type="submit" class="btn btn-block btn-success" style="margin: 25px 0">Добавить запись</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
    <input type="hidden" name="<?= DBWorker::ACTION_HTML_NAME?>" value="<?= DBWorker::GENERAL_REQUEST_ACTION?>">
</form>
<?php if (isset($db_answer)): dump($db_answer);
    $multi_row_empty = true;
    if (isset($db_answer["multi_row"]))
        foreach ($db_answer["multi_row"] as $table_name => $contents)
            if (!empty($contents))
            {
                $multi_row_empty = false;
                break;
            }
    if (isset($db_answer["error"])): ?>
        <div class="alert alert-danger text-center">
            <strong><?= $db_answer["error"];?></strong>
        </div>
    <?php elseif (isset($table) && empty($db_answer) || !isset($table) && empty($db_answer["single_row"]) && $multi_row_empty):?>
        <div class="alert alert-info text-center">
            <strong>Запрос не вернул результатов</strong>
        </div>
    <?php else: ?>
<div id="relevantStudents">
    <h2>Результаты:</h2>
    <?php if (!isset($table)):
        foreach ($db_answer["single_row"] as $i => $student): ?>
            <h3>Ученик #<?= $i + 1?>:</h3>
            <div class="table-responsive">
                <table class="table">
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
            </div>
            <?php foreach ($db_answer["multi_row"] as $table => $students):
                $contract_number = reset($student);
                if (key_exists($contract_number, $students)): ?>
                <h4>Таблица <?= $table?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <?php foreach ($students[$contract_number] as $i => $row):
                            if ($i === 0):?>
                            <tr class="info">
                                <?php foreach ($row as $column => $value)
                                    echo "<th>$column</th>"?>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <?php foreach ($row as $column => $value)
                                    echo "<td>$value</td>"?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif;
            endforeach;?>
            <div style="height: 40px"></div>
    <?php endforeach;
    elseif (true):?>
        <div class="table-responsive">
            <table class="table table-striped">
            <?php foreach ($db_answer as $i => $row):
                if ($i === 0):?>
                    <tr class="info">
                        <?php foreach ($row as $column => $value)
                            echo "<th>$column</th>"?>
                    </tr>
                <?php endif; ?>
                <tr>
                    <?php foreach ($row as $column => $value)
                        echo "<td>$value</td>"?>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
    <a href="index.php<?php if (isset($table)) echo "?page=" . REAL_DOCUMENT_ROOT .  $table . ".php"; ?>" id="clearTable" class="btn btn-success">Очистить</a>
</div>
<?php endif;
    endif;?>

