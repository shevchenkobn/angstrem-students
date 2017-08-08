<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- Bootstrapped select -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>

    <link rel="stylesheet" href="/<?= RELATIVE_DOCUMENT_ROOT?>css/styles.css">
    <script src="/<?= RELATIVE_DOCUMENT_ROOT?>js/script.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? htmlspecialchars($title)
            : "АНГСТРЕМ: ученики"?></title>
</head>
<body>
<header>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">
                    <img src="/<?= RELATIVE_DOCUMENT_ROOT?>images/logo2.png" class="logo" width="32" height="32"
                         alt="АНГСТРЕМ" style="display: inline-block;">
                    АНГСТРЕМ<small> ученики</small>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="main_navbar">
            <?php switch (!empty($_SESSION)): ?><?php case true: ?>
                <ul class="nav navbar-nav">
                    <li class="dropdown active">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Таблицы
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        <?php foreach (DBWorker::GetInstance()->GetDatabaseStructure()["database"] as $__table => $table_info): ?>
                            <li><a href="index.php?page=<?= $__table?>.php"><span class="glyphicon glyphicon-list-alt"></span> <?= $table_info["translation"]?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="index.php?page=add_new.php"><span class="glyphicon glyphicon-edit"></span> Добавить</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown active">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?= htmlspecialchars($_SESSION['user']["fullname"]) ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Профиль</a></li>
                            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Выйти</a></li>
                        </ul>
                    </li>
                </ul>
            <?php break; case false: ?>
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="login.php">
                            <span class="glyphicon glyphicon-log-in"></span> Войти</a>
                    </li>
                </ul>
            <?php endswitch; ?>
            </div>
        </div>
    </nav>
</header>
<!--Make breadcrumb in perspective-->
<!--<ol class="breadcrumb">-->
<!--    <li class="breadcrumb-item"><a href="#">Home</a></li>-->
<!--    <li class="breadcrumb-item"><a href="#">Library</a></li>-->
<!--    <li class="breadcrumb-item active">Data</li>-->
<!--</ol>-->
<div class="content container">