<?php
session_start();
require_once 'connect.php';

if (!$_SESSION['user']) //Чтобы зарег-му пользователю не была доступна страница регистрации
{
    header('Location: profile.php');
}

$id = $_SESSION['user']['id'];         // Информация о пользователе
$login = $_SESSION['user']['login'];
$full_name = $_SESSION['user']['full_name'];
$User_email = $_SESSION['user']['email'];

/* ЗАЩИТА ОТ СПАМА*/
$time_finish = time();
$ipUser = $_SERVER['REMOTE_ADDR']; //ip пользователя

$check_ip = mysqli_query($connect, "SELECT * FROM `bots`WHERE ip = '$ipUser' OR id = '$id'");
if (mysqli_num_rows($check_ip) > 0) {
    exit('Ваш аккаунт заблокирован в связи с подозрительной деятельностью.');
}

if ( $time_finish - $_SESSION['time_start'] < 3 || $_POST['email'] || $_POST['age']){
    mysqli_query($connect, "INSERT INTO `bots` (`date`, `id`) VALUES (CURRENT_TIMESTAMP(),'$id')");
    exit('Ваш аккаунт заблокирован в связи с подозрительной деятельностью.');
}
unset($_SESSION['time_start']);
/* ЗАЩИТА ОТ СПАМА*/

$mass = $_SESSION['order']['mass'];     // Информация по заказу
$length = $_SESSION['order']['length'];
$width = $_SESSION['order']['width'];
$height = $_SESSION['order']['height'];
$item_from = $_SESSION['order']['item_from'];
$destination = $_SESSION['order']['destination'];
$distance = $_SESSION['order']['distance'];
$cost_transport = $_SESSION['order']['cost_transport'];
$id_order = $_SESSION['order']['id_order'];
$status = 'В обработке';

$id = $_SESSION['user']['id'];         // Информация о пользователе
$full_name = $_SESSION['user']['full_name'];

//mysqli_query($connect, "INSERT INTO `orders` (`date`,`id`,`Id_order`, `full_name`, `mass`, `length`, `width`, `height`, `item_from`, `destination`, `distance`, `cost`, `Status`) VALUES (CURDATE(),'$id', '$id_order', '$full_name', '$mass', '$length', '$width', '$height', '$item_from', '$destination', '$distance', '$cost_transport', '$status')");
$query = 'INSERT INTO `orders` (`date`,`id`,`Id_order`, `full_name`, `mass`, `length`, `width`, `height`, `item_from`, `destination`, `distance`, `cost`, `Status`) VALUES (CURDATE(),? ,? ,? ,?, ? ,?,? ,? ,? ,? ,? ,?)';
$stmt = $connect_oop->prepare($query);
$stmt -> bind_param('iisiiiissiis', $id, $id_order, $full_name, $mass, $length, $width, $height, $item_from, $destination, $distance, $cost_transport, $status);
$stmt->execute();

$message_admin ='Новый заказ № '."$id_order".' от пользователя '."$login ".'на сумму '."$cost_transport".' .р'.'. Статус: '."$status";
$message_user ='Новый заказ № '."$id_order". 'на сумму '."$cost_transport".' .р'.'. Статус: '."$status";
mail('gdenix@mail.ru','Новый заказ №'."$id_order", $message_admin);
mail($User_email,'Новый заказ №'."$id_order", $message_user);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Новый заказ</title>
</head>
<body>

<h2>Заказ №  <?= $_SESSION['order']['id_order'] ?> Успешно сформирован! </h2>
<button id="new_order"> <a href="../../profile.php">Личный кабинет</a> </button>

</body>
</html>


