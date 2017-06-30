<div class="row" style="height: 120px;">
    <?php  if (isset($errors)): ?>
        <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger alert-dismissable fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Ошибка!</strong> <?php echo $error ?>
        </div>
        <?php endforeach;?>
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-info">
            <div class="panel-heading">Авторизация</div>
            <div class="panel-body"><form method="POST" action="<?= REAL_DOCUMENT_ROOT?>login.php">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="email" class="form-control" id="login" name="login">
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-block btn-primary" name="action" value="login">Авторизоваться</button>
            </form></div>
        </div>
    </div>
</div>
<div class="row" style="padding-top: 120px">

</div>