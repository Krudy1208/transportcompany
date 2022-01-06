<?php
session_start();

if ($_SESSION['user']) //Чтобы зареганномо пользователю небыла доступна страница регистрации
{
    header('Location: profile.php');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="body">

<form action="assets/vendor/signup.php" method="POST" enctype="multipart/form-data"> <!-- enctype позволит передавать через форму файлы -->
    <label>Имя</label>
    <input type="text" name="full_name" placeholder="Введите свое Имя ">
    <label>Фамилия</label>
    <input type="text" name="second_name" placeholder="Введите свою Фамилию">
    <label>Отчество</label>
    <input type="text" name="patronymic" placeholder="Введите свое Отчество">
    <label>Логин</label>
    <input type="text" name="login" placeholder="Введите свой лоигин">
    <label>Почта</label>
    <input type="email" name="email" placeholder="Введите свою почту">
    <label>Аватар</label>
    <input type="file" name="avatar">
    <label>Пароль</label>
    <input type="password" name="password" placeholder="Введите пароль">
    <label>Подтверждение пароля</label>
    <input type="password" name="password_confirm" placeholder="Подтвердите пароль">
    <button type="submit">Зарегестрироваться</button>
    <p>
        У вас есть аккаунт? - <a href="index.php"> Войти. </a>
    </p>

    <p>
        <?php

        if ($_SESSION['message_error_form']) {
            echo ' <p class="msg">'. $_SESSION['message_error_form'].'</p>';
            unset($_SESSION['message_error_form']);
        }
        if ($_SESSION['message_error_form_']) {
            echo ' <p class="msg">'. $_SESSION['message_error_form_pass'].'</p>';
            unset($_SESSION['message_error_form_pass']);
        }

        if ($_SESSION['mess_error_logpass']) {
            ++$_SESSION['count_err'];
            echo ' <p class="msg">' . $_SESSION['mess_error_logpass'] . '</p>';
            unset($_SESSION['mess_error_logpass']);

        }

        ?>
    </p>

</form>

</body>
</html>

