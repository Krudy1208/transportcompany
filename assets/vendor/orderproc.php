<?php
session_start();
require_once 'connect.php';
require_once 'armor.php';
/* ЗАЩИТА ОТ СПАМА*/
$time_finish = time();
$ipUser = $_SERVER['REMOTE_ADDR']; //ip пользователя

$check_ip = mysqli_query($connect, "SELECT * FROM `bots`WHERE ip = '$ipUser'");
if (mysqli_num_rows($check_ip) > 0) {
    exit('Ваш IP '."($ipUser)".' заблокирован в связи с подозрительной деятельностью.');
}

if ( $time_finish - $_SESSION['time_start'] < 3 || $_POST['email'] || $_POST['age']){
    mysqli_query($connect, "INSERT INTO `bots` (`date`, `ip`) VALUES (CURRENT_TIMESTAMP(),'$ipUser')");
    exit('Ваш IP заблокирован в связи с подозрительной деятельностью. Ваши данные отпраленны в МВД РФ Управление "К".');
}
unset($_SESSION['time_start']);
/* ЗАЩИТА ОТ СПАМА*/

if ($_SESSION['order']) {unset($_SESSION['order']);}

if($_POST['mass'] && $_POST['length'] && $_POST['width'] && $_POST['height'] && $_POST['item_from'] && $_POST['destination'] && $_POST['distance']){

    $mass = (int)$_POST['mass'];
    $length = (int)$_POST['length'];
    $width = (int)$_POST['width'];
    $height = (int)$_POST['height'];
    $item_from = $_POST['item_from'];
    $destination = $_POST['destination'];
    $distance = (int)$_POST['distance'];
    $id = $_SESSION['user']['id'];
    $full_name = $_SESSION['user']['full_name'];
    ekran($item_from);
    ekran($destination);

//Присваивание уникального номера заказа (id пользователя + порядковый номер заказа)
    $check_id = mysqli_query($connect, "SELECT * FROM `orders`WHERE `id` = '$id' AND `id_order`"); //возвращает количество заказов (id_order) пользователя
    if (mysqli_num_rows($check_id) >= 0) {
       $id_order = $id . mysqli_num_rows($check_id);
    }

    $volume = ($length * $width * $height) * 0.000001;

    if ($volume <= 1) {
        $cost_transport = $distance;
    }
    if ($volume > 1 && $mass <= 10) {
        $cost_transport = 2 * $distance;
    }
    if ($volume > 1 && $mass > 10) {
        $cost_transport = 3 * $distance;
    }

    $_SESSION['order']= [ //
        "mass" => $mass,
        "length" => $length,
        "width" => $width,
        "height" => $height,
        "item_from" => $item_from,
        "destination" => $destination,
        "distance" => $distance,
        "cost_transport" => $cost_transport,
        "id_order" => $id_order,
    ];

    header('Location: ../../new_order.php');

    //mysqli_query($connect, "INSERT INTO `orders` (`id`,`Id_order`, `full_name`, `mass`, `length`, `width`, `height`, `item_from`, `destination`, `distance`, `cost`) VALUES ('$id','$id_order','$full_name','$mass','$length','$width','$height','$item_from','$destination','$distance','$cost_transport')");

}
else {
    $_SESSION['message_error_form'] = 'Заполните все поля';
    header('Location: ../../order_form.php');
}