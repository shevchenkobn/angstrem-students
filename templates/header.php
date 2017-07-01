<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/<?= RELATIVE_DOCUMENT_ROOT?>css/styles.css">
    <script src="/<?= RELATIVE_DOCUMENT_ROOT?>js/script.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                    АНГСТРЕМ
                </a>
            </div>
            <div class="collapse navbar-collapse" id="main_navbar">
<!--                <ul class="nav navbar-nav">-->
<!--                    <li class="active"><a href="index.php">-->
<!--                            <span class="glyphicon glyphicon-home"></span> На главную-->
<!--                        </a></li>-->
<!--                    <li><a href="shopping.php">-->
<!--                            <span class="glyphicon glyphicon-menu-hamburger"></span> Products-->
<!--                        </a></li>-->
<!--                    <li><a href="cart.php">-->
<!--                            <span class="glyphicon glyphicon-shopping-cart"></span> Cart-->
<!--                        </a></li>-->
<!--                </ul>-->
                <ul class="nav navbar-nav navbar-right">
                    <?php if (!empty($_SESSION)): ?>
                        <li><p class="navbar-text">
                                <?php if(isset($_SESSION["user"]))
                                    echo htmlspecialchars($_SESSION['user']["fullname"]);
                                ?>
                            </p></li>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="profile.php">
                                    <span class="glyphicon glyphicon-user"></span> Профиль
                                </a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="active"><a href="<?php echo empty($_SESSION) ? "login.php" : "logout.php"?>">
                            <span class="glyphicon glyphicon-log-<?php echo !empty($_SESSION) ? "out" : "in"?>"></span> <?php
                            echo empty($_SESSION) ? "Войти" : "Выйти"?></a>
                    </li>
                </ul>
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