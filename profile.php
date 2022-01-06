<?php
session_start();
require_once 'assets/vendor/connect.php';
//$rand = $_SESSION['key']['rand'];
//$time = $_SESSION['key']['time'];

if (!$_SESSION['user'])
    {
        header('Location: index.php');
    }
$id= $_SESSION['user']['id'];

/* проверка на бан*/
$time_finish = time();
$ipUser = $_SERVER['REMOTE_ADDR']; //ip пользователя

$check_ip = mysqli_query($connect, "SELECT * FROM `bots`WHERE id = '$id'");
if (mysqli_num_rows($check_ip) > 0) {
    exit('Ваш аккаунт заблокирован в связи с подозрительной деятельностью.');
}

if ( $time_finish - $_SESSION['time_start'] < 3 || $_POST['email'] || $_POST['age']){
    mysqli_query($connect, "INSERT INTO `bots` (`date`, `ip`) VALUES (CURRENT_TIMESTAMP(),'$ipUser')");
    exit('Ваш IP заблокирован в связи с подозрительной деятельностью.');
}
unset($_SESSION['time_start']);
/*проверка на бан*/


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div id="profile">
        <img src="<?= $_SESSION['user']['avatar'] ?>" width="200" alt="">
        <h2><?= $_SESSION['user']['second_name']?> </h2>
        <h2><?= $_SESSION['user']['full_name']?> <?=$_SESSION['user']['patronymic']?> </h2>
        <p><?= $_SESSION['user']['email'] ?></p>
        <p> <?= $_SESSION['time_start']?> </p>
        <a href="assets/vendor/logout.php">Выход</a>
    </div>
    <div id="my_order">
       Ваши доставки
        <button id="new_order"> <a href="order_form.php">Новая доставка</a> </button> <br>
<!-- ФИЛЬТРЫ -->
        <form action="profile.php" method="get">
            <select name="filter" size="1">
                <option value=""></option>
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

        /*ПАГИНАЦИЯ*/
        if($_GET['pageno']){
            $pageno = $_GET['pageno'];
        }
        else $pageno = 1;
        $size_page = 5; //количество строк на странице
        $offset = ($pageno-1) * $size_page; // Вычисляем с какого объекта начать выводить

        $sql_request = mysqli_query($connect, "SELECT * FROM `orders`WHERE `id` = '$id'");
        $num_rows = mysqli_num_rows($sql_request); //общее количество строк в таблице
        $total_page =  ceil($num_rows / $size_page ); //сколько будет всего страниц в зависимости от кол-ва строк
        /*ПАГИНАЦИЯ*/
        if (!$_GET['filter']){
        $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `date` DESC LIMIT $offset, $size_page"); //проверяем наличие доставок у пользователя
            $lines = mysqli_num_rows($request);
        }
         /* ЗАПРОС ПО ФИЛЬТРАМ */
    if($_GET['filter']){

        /*по дате*/
        switch ($_GET['filter']){
            /* от последнего заказа */
            case 'filter_data_last': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `date` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=filter_data_last';
               break;

                /* от первого заказа */
            case 'filter_data_first': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `date` ASC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=filter_data_first';
               break;

            /* Цена от большего */
            case 'cost_up': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `cost` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=cost_up';
                break;

            /* Цена от меньшего */
            case 'cost_down': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `cost` ASC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=cost_down';
                break;

            /* Расстояние от меньшего */
            case 'distance_up': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `distance` DESC  LIMIT $offset, $size_page ");

                $lines = mysqli_num_rows($request);
                $get='&filter=distance_up';
                break;

            /* Расстояние от большего */
            case 'distance_down': $request = mysqli_query($connect, "SELECT * FROM `orders` WHERE `id` = '$id' ORDER BY `distance` ASC  LIMIT $offset, $size_page ");

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
            <th>Дата</th><th>Заказ №</th> <th>Вес посылки</th> <th>Город отправления</th> <th>Город получения</th> <th>Расстояние</th> <th>Стоимость</th> <th>Статус</th>
        </tr>
        </thead>
        <tbody>
            <?
           // $user_order = mysqli_fetch_assoc($check_id);
            while ($user_order = mysqli_fetch_assoc($request)){
                ?>
            <tr>
                <td> <?= $user_order['date']?> </td><td> <?= $user_order['Id_order']?> </td> <td><?= $user_order['mass']?></td> <td><?= $user_order['item_from']?></td> <td><?= $user_order['destination']?></td> <td><?= $user_order['distance']?></td> <td><?= $user_order['cost']?></td> <td><?= $user_order['Status']?></td>
            </tr>
            <?
            }
        }
        else { ?> <p> У Вас пока нет доставок </p> <?  }
        ?>
        </tbody>
            </table>
        <? /*ВЫВОД ПАГИНАЦИИ */
        if($num_rows > $size_page){
            for($i=1; $total_page >= $i; $i++){
                ?> <a class="s" href="profile.php?<?echo $get?>&pageno=<?echo $i?>"> <?echo "$i"?> </a>
            <?  }
        }
        ?>
    </div>
</body>
</html>