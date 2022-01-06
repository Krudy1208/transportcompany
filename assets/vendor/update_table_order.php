<?php
session_start();
require_once 'connect.php';

 if (!$_SESSION['user']) //Чтобы зарег-му пользователю не была доступна страница регистрации
{
    header('Location: profile.php');
}
$User_email = $_SESSION['user']['email'];


if ($_POST["number_orders"] && $_POST["status"]){
    $number_orders = $_POST["number_orders"];
    $new_status = $_POST["status"];

    foreach ($number_orders as $item){
        mysqli_query($connect,"UPDATE `orders` SET `Status` = '$new_status' WHERE `Id_order` = '$item'");
       }
    mail($User_email, 'Заказ № '."$number_orders ",'Статус заказа был изменен: '."$new_status" );
    header('Location: ../../admin_profile.php');
}