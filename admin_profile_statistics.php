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
    <?

    if (!$_POST['month']){
    $sql_request = mysqli_query($connect, "SELECT * FROM `orders`");
    $num_orders = mysqli_num_rows($sql_request); //общее количество заказов
        
    $request_total_amount = mysqli_query($connect, "SELECT SUM(cost) FROM `orders` "); //общая сумма
    $arr_total_amount = mysqli_fetch_assoc($request_total_amount);        
    $total_amount = $arr_total_amount['SUM(cost)'];
        
    $average_value = mysqli_query($connect, "SELECT AVG(cost) FROM `orders` ");// средняя сумма
    $arr_average_value = mysqli_fetch_assoc($average_value);
    $average_value = round($arr_average_value['AVG(cost)'], 2, PHP_ROUND_HALF_UP);

    $request_greatest_distance = mysqli_query($connect, "SELECT MAX(distance) FROM `orders` "); //самое дальнее расстояние
    $arr_greatest_distance = mysqli_fetch_assoc($request_greatest_distance);
    $greatest_distance = $arr_greatest_distance['MAX(distance)'];
    $request_distantion = mysqli_query($connect, "SELECT * FROM `orders` WHERE `distance` = '$greatest_distance'");
    $arr_max_distantion = mysqli_fetch_assoc($request_distantion);
    $greatest_distance_city1 = $arr_max_distantion['item_from'];// город отправление
    $greatest_distance_city2 = $arr_max_distantion['destination']; //город доставки

    $request_smallest_distance = mysqli_query($connect, "SELECT MIN(distance) FROM `orders` "); //самое ближнее расстояние
    $arr_smallest_distance = mysqli_fetch_assoc($request_smallest_distance);
    $smallest_distance = $arr_smallest_distance['MIN(distance)'];
    $request_min_distantion = mysqli_query($connect, "SELECT * FROM `orders` WHERE `distance` = '$smallest_distance'");
    $arr_min_distantion = mysqli_fetch_assoc($request_min_distantion);
    $smallest_distance_city1 = $arr_min_distantion['item_from'];// город отправление
    $smallest_distance_city2 = $arr_min_distantion['destination'];//город доставки
    }
    /*Запросы по месяцу для списка */
    if ($_POST['month'] && $_POST['year']) {
        
        //функция $request_month_year возвращает массив статистики (общее количество заказов, общая сумма, средняя сумма, самое дальнее расстояние + города, самое ближнее расстояние + города) в зависимоти от месяца и года
       
        $request_month_year = function ($month, $year) use ($connect) {
            $sql_request = mysqli_query($connect, "SELECT * FROM `orders` WHERE YEAR(date)= '$year' AND MONTHNAME(date)= '$month'");
            $show_lines = mysqli_num_rows($sql_request);
            if ($show_lines > 0) {
                $num_orders = $show_lines;
                $request_total_amount = mysqli_query($connect, "SELECT SUM(cost) FROM `orders` WHERE YEAR(date)= '$year' AND MONTHNAME(date)= '$month' "); //общая сумма
                $arr_total_amount = mysqli_fetch_assoc($request_total_amount);
                $total_amount = $arr_total_amount['SUM(cost)'];
                $average_value = mysqli_query($connect, "SELECT AVG(cost) FROM `orders` WHERE YEAR(date)= '$year' AND MONTHNAME(date)= '$month'");// средняя сумма
                $arr_average_value = mysqli_fetch_assoc($average_value);
                $average_value = round($arr_average_value['AVG(cost)'], 2, PHP_ROUND_HALF_UP);

                $request_greatest_distance = mysqli_query($connect, "SELECT MAX(distance) FROM `orders` WHERE YEAR(date)= '$year' AND MONTHNAME(date)= '$month'"); //самое дальнее расстояние
                $arr_greatest_distance = mysqli_fetch_assoc($request_greatest_distance);
                $greatest_distance = $arr_greatest_distance['MAX(distance)'];
                $request_distantion = mysqli_query($connect, "SELECT * FROM `orders` WHERE `distance` = '$greatest_distance'");
                $arr_max_distantion = mysqli_fetch_assoc($request_distantion);
                $greatest_distance_city1 = $arr_max_distantion['item_from'];// город отправление
                $greatest_distance_city2 = $arr_max_distantion['destination']; //город доставки

                $request_smallest_distance = mysqli_query($connect, "SELECT MIN(distance) FROM `orders` WHERE YEAR(date)= '$year' AND MONTHNAME(date)= '$month'"); //самое ближнее расстояние
                $arr_smallest_distance = mysqli_fetch_assoc($request_smallest_distance);
                $smallest_distance = $arr_smallest_distance['MIN(distance)'];
                $request_min_distantion = mysqli_query($connect, "SELECT * FROM `orders` WHERE `distance` = '$smallest_distance'");
                $arr_min_distantion = mysqli_fetch_assoc($request_min_distantion);
                $smallest_distance_city1 = $arr_min_distantion['item_from'];// город отправление
                $smallest_distance_city2 = $arr_min_distantion['destination'];//город доставки

                return

                    [
                        "num_orders" => $num_orders,  //кол-во заказов
                        "total_amount" => $total_amount, //общая сумма
                        "average_value" => $average_value, // средняя сумма
                        "greatest_distance" => $greatest_distance, // самое дальнее расстояние
                        "greatest_distance_city1" => $greatest_distance_city1,// город отправление
                        "greatest_distance_city2" => $greatest_distance_city2,//город доставки
                        "smallest_distance" => $smallest_distance,//самое ближнее расстояние
                        "smallest_distance_city1" => $smallest_distance_city1,// город отправление
                        "smallest_distance_city2" => $smallest_distance_city2,//город доставки
                    ];

            }

        };
        $arr_request_month = $request_month_year($_POST['month'],$_POST['year']); //Вызов функции

        /* Извлечение переменных из массива */
        $num_orders = $arr_request_month['num_orders'];
        $total_amount = $arr_request_month['total_amount'];
        $average_value = $arr_request_month['average_value'];
        $greatest_distance = $arr_request_month['greatest_distance'];
        $greatest_distance_city1 = $arr_request_month['greatest_distance_city1'];
        $greatest_distance_city2 = $arr_request_month['greatest_distance_city2'];
        $smallest_distance = $arr_request_month['smallest_distance'];
        $smallest_distance_city1 = $arr_request_month['smallest_distance_city1'];
        $smallest_distance_city2 = $arr_request_month['smallest_distance_city2'];
    }

    ?>
</div>
<div id="my_order">
    <?if ($arr_request_month || !$_POST['month']  || !$_POST['year']){  //  ?> //если запросов нет - возвращает статистику за все время
        
     <?if ($_POST['month'] == ''){ ?> // если первая строка в списке она же ''
         <h3>Статистика за все время</h3>
         <? }

         ?>
  <?if($_POST['month'] && $_POST['year'] !==""){ ?>
      <h3>Статистаки за <? print_r($_POST['month'].' '.$_POST['year']);""?></h3> // Заголовок.  
      <?}

      ?>

<p>Общее количество заказов: <?echo $num_orders?></p>
    <p>Общая сумма: <?echo $total_amount?> руб.</p>
    <p>Средняя сумма: <?echo $average_value?> руб.</p>
    <p>Самое дальнее расстояние: <?echo $greatest_distance?> км.  Маршрут: <?echo "$greatest_distance_city1 -"." $greatest_distance_city2"?>  </p>
    <p>Самое ближнее расстояние: <?echo $smallest_distance?> км. Маршрут: <?echo "$smallest_distance_city1 -"." $smallest_distance_city2"?> </p>
<? }


elseif (!$arr_request_month) { //?> //в случае отсутствия записей в БД на счет конкретного месяца и года 
       <h3>Статистака за <? print_r($_POST['month'].' '.$_POST['year']);?></h3>
    За данный промежуток времени данных нет.
    <? }

    ?>
  <p>Показать статистику за...</p>
    <form action ="admin_profile_statistics.php" method="POST">
    <select name="month" size="1">
        <option value="">За все время</option>
        <option value="January">Январь</option>
        <option value="february">Февраль</option>
        <option value="march">Март</option>
        <option value="april">Апрель</option>
        <option value="may">Май</option>
    </select>
        <select name="year" size="1">
            <option value=""></option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
        </select>
    <button type="submit">Выбрать</button>

    </form>
    <button id="new_order"> <a href="admin_profile.php">Админка</a> </button>
</div>

</body>
</html>
