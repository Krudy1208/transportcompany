<?php
session_start();

if (!$_SESSION['user']) //Чтобы зареганномо пользователю небыла доступна страница регистрации
{
    header('Location: profile.php');
}
$_SESSION['time_start'] = time();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Новый заказ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="body">

<form action="assets/vendor/orderproc.php" method="POST">
    <label>Вес посылки</label>
    <input type="hidden" name="email">
    <input type="number" name="mass" placeholder="кг: ">
    <label>Длинна</label>
    <input type="number" name="length" placeholder="см: ">
    <label>Ширина</label>
    <input type="number" name="width" placeholder="см: ">
    <label>Высота</label>
    <input type="hidden" name="age">
    <input type="number" name="height" placeholder="см: ">
    <label>Пункт отправления</label>
    <input type="text" name="item_from" placeholder="Укажите город отправлния.">
    <label>Пункт назначения</label>
    <input type="text" name="destination" placeholder="Укажите город прибытия.">
    <label>Расстояние между населенными пунктами</label>
    <input type="number" name="distance" placeholder="км: ">
    <button type="submit">Оформить заказ</button>
</form>
<p>
<?
if ($_SESSION['message_error_form']) {
echo ' <p class="msg">'. $_SESSION['message_error_form'].'</p>';
unset($_SESSION['message_error_form']);
}
?>
</p>

</body>
</html>