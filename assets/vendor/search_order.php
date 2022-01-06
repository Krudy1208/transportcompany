<?php
session_start();
require_once 'connect.php';
unset($_SESSION['search']);

if (!$_SESSION['user']) //Чтобы зарег-му пользователю не была доступна страница регистрации
{
    header('Location: profile.php');
}
/*
if ($_POST['search']){
    $search = $_POST['search'];

    $_SESSION['search'] = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Id_order` = '$search'");

    header('Location: ../../admin_profile.php');
}



