<?php
session_start();
$_SESSION['time_start'] = time();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body >
<div class="body">
<form action="assets/vendor/signin.php" method="POST">
    <label>Логин</label>
    <input type="hidden" name="username">
    <input type="text" name="login" placeholder="Введите логин">
    <label>Пароль</label>
    <input type="password" name="password" placeholder="Введите пароль">
    <input type="hidden" name="capcha">
    <button type="submit">Войти</button>
    <p>
        У вас нет аккаунта? - <a href="register.php"> Зарегистрируйтесь.</a>
    </p>
    <?php

        if ($_SESSION['message']) {
            echo ' <p class="msg">'. $_SESSION['message'].'</p>';
            unset($_SESSION['message']);
        }
    ?>

</form>
</div>
</body>
</html>
