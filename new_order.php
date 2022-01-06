<?php

session_start();
if (!$_SESSION['user']) //Чтобы зарег-му пользователю небыла доступна страница регистрации
{
    header('Location: profile.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">

    <title>Новый заказ</title>
</head>
<body>
<h2>Информация по заказу:</h2>
<p>Имя: <?= $_SESSION['user']['full_name'] ?> </p>
<p>Фамилия: <?= $_SESSION['user']['second_name'] ?> </p>
<p>Отчество: <?= $_SESSION['user']['patronymic'] ?> </p>

<p>Вес посылки: <?= $_SESSION['order']['mass'] ?> </p>
<p>Длинна посылки: <?= $_SESSION['order']['length'] ?> </p>
<p>Ширина посылки: <?= $_SESSION['order']['height'] ?> </p>
<p>Высота: <?= $_SESSION['order']['height'] ?> </p>
<p>Пункт отправления: <?= $_SESSION['order']['item_from'] ?> </p>
<p>Пункт назначения: <?= $_SESSION['order']['destination'] ?> </p>

<h2>Стоимость: <?= $_SESSION['order']['cost_transport'] ?> руб. </h2>

<button id="new_order"> <a href="order_form.php">Назад</a> </button>
<button id="new_order"> <a href="assets/vendor/add_order.php">Оформить</a> </button>




</body>
</html>

