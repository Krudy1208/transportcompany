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

    <!-- ПОИСК ПО № ЗАКАЗА -->
    <form action="admin_profile.php" method="get">
        Номер заказа <input type="search" name="search" placeholder="введите номер заказа или имя заказчика">
        <button type="submit">Найти</button>
    </form>

   <!-- ФИЛЬТРЫ -->
    <form action="admin_profile.php" method="get">
        <select name="filter" size="1">
            <option value=""></option>
            <option value="filter_new_order">Новые заказы</option>
            <option value="filter_data_last">от последнего заказа</option>
            <option value="filter_data_first">от первого заказа</option>
            <option value="cost_up">Цена: по убыванию</option>
            <option value="cost_down">Цена: по возрастанию</option>
            <option value="distance_up">Расстояние: по убыванию</option>
            <option value="distance_down">Расстояние: по возрастанию</option>
        </select>
        <button type="submit">Отфильтровать</button>
    </form>

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
    $size_page = 5; //количество строк на странице
    $offset = ($pageno-1) * $size_page; // Вычисляем с какого объекта начать выводить

    $sql_request = mysqli_query($connect, "SELECT * FROM `orders`");
    $num_rows = mysqli_num_rows($sql_request); //общее количество строк в таблице
    $total_page =  ceil($num_rows / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк
    /*ПАГИНАЦИЯ*/

    /* Запрос на таблицу orders если не заданы фильтры и поиск ->*/
    if (!$_GET['search'] && !$_GET['filter']){
        $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `date` DESC LIMIT $offset, $size_page"); // Возвращает просто таблицу orders. $table_only (assets/vendor/search_order.php)
        $lines = mysqli_num_rows($request);
        //$total_page =  ceil( $lines  / $size_page );
    }
    /*<- Запрос на таблицу orders если не заданы фильтры и поиск */

    /* Запрос по строке поиска ->*/
    elseif($_GET['search']){
        $search = ekran($_GET['search']);
        $search = ekran($_GET['search']);
        //$request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Id_order` = '$search' "); // Поиск по номеру заказа через форму search. $form_search  ORDER BY `date` DESC DESC LIMIT $offset, $size_page
        //$lines = mysqli_num_rows($request);
        //$total_page =  ceil($lines / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк
        if(!$_GET['filter']){
        $query = "SELECT * FROM `orders` WHERE `Id_order` = ? OR `full_name` = ? ORDER BY `date` DESC LIMIT $offset, $size_page ";
        }

        $stmt = $connect_oop->prepare($query);
        $stmt -> bind_param('is', $search, $search);
        $stmt->execute();
        $request =$stmt->get_result();
        $lines = mysqli_num_rows($request);
        $request_pageno = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Id_order` = '$search' OR  `full_name` = '$search'"); // зпрос чтобы узнать сколько строк у конкредного запроса
        $lines_pageno = mysqli_num_rows($request_pageno); // количество строк в запросе

        $total_page =  ceil($lines_pageno / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк
        $get='&search='."$search";

    }

    /* ЗАПРОС ПО ФИЛЬТРАМ */
    elseif($_GET['filter']){

        /*по новым заказам*/
        switch ($_GET['filter']){
            /* Новые заказы */
            case 'filter_new_order': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Status` = 'В обработке' ORDER BY `date` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $request_pageno = mysqli_query($connect, "SELECT * FROM `orders` WHERE `Status` = 'В обработке'"); // запрос для определения кол-ва страниц при фильтре "новый заказ"
                $lines_pageno = mysqli_num_rows($request_pageno);//кол-во линий при фильре "новый заказ"
                $total_page =  ceil($lines_pageno / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк

                $get='&filter=filter_new_order';
                break;

            /* от последнего заказа */
            case 'filter_data_last': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `date` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=filter_data_last';
               break;

                /* от первого заказа */
            case 'filter_data_first': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `date` ASC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=filter_data_first';
               break;

            /* Цена от большего */
            case 'cost_up': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `cost` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=cost_up';
                break;

            /* Цена от меньшего */
            case 'cost_down': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `cost` ASC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=cost_down';
                break;

            /* Расстояние от меньшего */
            case 'distance_up': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `distance` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=distance_up';
                break;

            /* Расстояние от большего */
            case 'distance_down': $request = mysqli_query($connect, "SELECT * FROM `orders` ORDER BY `distance` ASC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=distance_down';
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
        </tbody>
        <p>Выбрать статус</p>
        <select name="status" size="1" >
            <option value=""></option>
            <option value="Ожидание оплаты">Ожидание оплаты</option>
            <option value="Ожидание поступления">Ожидание поступления</option>
            <option value="Принята в отделении">Принята в отделении</option>
            <option value="На пути в транзитный город">На пути в транзитный город</option>
            <option value="В транзитном городе">В транзитном городе</option>
            <option value="Ожидает в месте вручения">Ожидает в месте вручения</option>
            <option value="Вручено">Вручено</option>
            <option value="Отменен">Отменен</option>
        </select>
            <button type="submit">Изменить</button>

        <? /*ВЫВОД ПАГИНАЦИИ */

       for($i=1; $total_page >= $i; $i++){

            ?> <a class="s" href="admin_profile.php?<?echo $get?>&pageno=<?echo $i?>"> <?echo "$i"?> </a>
        <?  }


        ?>

     <? if($search){
         ?> <button id="new_order"> <a href="admin_profile.php">Назад</a> </button> <?
        }
      }
    else{
        ?> <p>Заказ не найден</p>
        <button id="new_order"> <a href="admin_profile.php">Назад</a> </button> <? } ?>
    <button> <a href="admin_profile_statistics.php">Статистика</a> </button>
</div>
</body>
</html>
