<?php

session_start();
require_once 'assets/vendor/connect.php';
require_once 'assets/vendor/armor.php';

if (!$_SESSION['user'] && $_SESSION['user']['login'] !== 'admin')
{
    header('Location: index.php');
}
$id= $_SESSION['user']['id'];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Админка</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="profile">

    <img src="<?= $_SESSION['user']['avatar'] ?>" width="200" alt="">
    <h2>Администратор:</h2>

    <h2><?= $_SESSION['user']['login']?> </h2>
    <p><?= $_SESSION['user']['email'] ?></p>
    <a href="assets/vendor/logout.php">Выход</a>

</div>

<div id="my_order">
    <h2>Доставки</h2>

    <?
    $sql_request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Status` = 'В обработке'");
    $nombre_new_orders = mysqli_num_rows($sql_request);
    if ($nombre_new_orders > 0){
        ?> <h3 style="color: red"> Количество новых заказов: <? echo "$nombre_new_orders"?> </h3> <?
    }

    /*ПАГИНАЦИЯ*/

    if($_GET['pageno']){
        $pageno = $_GET['pageno'];
        unset($_GET['pageno']);
    }
    else $pageno = 1;
    $size_page = 10; //количество строк на странице
    $offset = ($pageno-1) * $size_page; // Вычисляем с какого объекта начать выводить

    $sql_request = mysqli_query($connect, "SELECT * FROM `orders`");
    $num_rows = mysqli_num_rows($sql_request); //общее количество строк в таблице
    $total_page =  ceil($num_rows / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк
/*ПАГИНАЦИЯ*/
    /* ЗАПРОС ПО ФИЛЬТРАМ */
    if($_POST['filter']){
        /*по дате*/
        switch ($_POST['filter']){
            /* от последнего заказа */
            case 'filter_data_last': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `date` DESC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
                //unset($_POST['filter']);
                break;
                /* от первого заказа */
            case 'filter_data_first': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `date` ASC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
                //unset($_POST['filter']);
                break;
            /* Цена от большего */
            case 'cost_up': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `cost` DESC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
                //unset($_POST['filter']);
                break;
            /* Цена от меньшего */
            case 'cost_down': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `cost` ASC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
                //unset($_POST['filter']);
                break;
            /* Расстояние от меньшего */
            case 'distance_up': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `distance` DESC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
                //unset($_POST['filter']);
                break;
            /* Расстояние от большего */
            case 'distance_down': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `distance` ASC  LIMIT $offset, $size_page ");
                $lines = mysqli_num_rows($request);
               // unset($_POST['filter']);
                break;
        }
    }
    if ($lines > 0) {
    ?>
    <table>
        <thead>
        <tr>
            <th></th> <th>Дата</th> <th>ID пользователя</th> <th>Заказ №</th> <th>Имя</th> <th>Вес посылки</th> <th>Город отправления</th> <th>Город получения</th> <th>Расстояние</th> <th>Стоимость</th> <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <form action ="assets/vendor/update_table_order.php" method="POST">
            <?
            while ($user_order = mysqli_fetch_assoc($request)){
                ?>
                <tr>
                    <td><input type="checkbox" name="number_orders[]" value="<?=$user_order['Id_order']?>" /> </td> <td> <?= $user_order['date']?> </td> <td> <?= $user_order['id']?> </td> <td> <?= $user_order['Id_order']?> </td> </td> <td> <?= $user_order['full_name']?> </td> <td><?= $user_order['mass']?></td> <td><?= $user_order['item_from']?></td> <td><?= $user_order['destination']?></td> <td><?= $user_order['distance']?></td> <td><?= $user_order['cost']?></td> <td><?= $user_order['Status']?></td>
                </tr>
            <? } ?>
    </table>
    <? } ?>
    <? /*ВЫВОД ПАГИНАЦИИ */

    for($i=1; $total_page >= $i; $i++){

    ?> <a class="s" href="admin_filter.php?pageno=<?echo $i?>"> <?echo "$i"?> </a>
    <?  }
    ?>


    <button> <a href="admin_profile.php">Назад</a> </button>
</div>
</body>
</html>
