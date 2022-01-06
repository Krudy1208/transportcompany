<?php
session_start();
require_once 'connect.php';
require_once 'armor.php';

/* ЗАЩИТА ОТ СПАМА*/
$time_finish = time();
$ipUser = $_SERVER['REMOTE_ADDR']; //ip пользователя

$check_ip = mysqli_query($connect, "SELECT * FROM `bots`WHERE ip = '$ipUser'");
if (mysqli_num_rows($check_ip) > 0) {
    exit('Ваш IP заблокирован в связи с подозрительной деятельностью.');
}

if ( $time_finish - $_SESSION['time_start'] < 2 & $_POST['username']){
    mysqli_query($connect, "INSERT INTO `bots` (`date`, `ip`) VALUES (CURRENT_TIMESTAMP(),'$ipUser')");
    exit('Ваш IP заблокирован в связи с подозрительной деятельностью.');
}
unset($_SESSION['time_start']);
/* ЗАЩИТА ОТ СПАМА*/

if ($_POST["full_name"] && $_POST["second_name"] && $_POST["patronymic"] && $_POST["login"] && $_POST["email"] && $_POST["password"] && $_POST["password_confirm"]) {
    $full_name = $_POST["full_name"];
    $second_name = $_POST["second_name"];
    $patronymic = $_POST["patronymic"];
    $login = $_POST["login"];
    $email = $_POST["email"];
    $pass = $_POST["password"];
    $pass_conf = $_POST["password_confirm"];
    $simvol = ['!', '@', '#', '$', '%', '^', '&', '*', '+', '-', '-', '=', ':', '?', '|', '_', '~', '№',];

    /*Проверка на количество символов в пароле ->*/
    $check_num =strlen($pass);
    if ($check_num >= 6){
        /*<-Проверка на количество символов в пароле*/

   /*Проверка на наличие символов в пароле ->*/
    $control_simvol = 0;
    foreach ($simvol as $i) {
        $control = strpos($pass, $i);
        if ($control == true){
            ++$control_simvol;
        }
    }
    if ($control_simvol > 0) {
        /*<-Проверка на наличие символов в пароле*/

    ekran($full_name);
    ekran($second_name);
    ekran($patronymic);
    ekran($login);
    ekran($email);

    if ($pass == $pass_conf) {
        $path = 'uploads/' . time() . $_FILES['avatar']['name']; //Путь для загрузки картинки. Ф-ция time() добавляет время в имя файла для его уникальности
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], '../../' . $path)) //загрузить файл в
        {
            $_SESSION['message'] = 'Ошибка при загрузке';
            header('Location: ../../register.php');
        }

        $salt1 = "*#4!mfpo";
        $salt2 = "dg@&3g@*";

        $pass = sha1($salt1 . $pass . $salt2);

        $query = ("SELECT * FROM `users` WHERE `login` = ? ");
        $stmt = $connect_oop->prepare($query);
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $check_login =mysqli_num_rows($result);

        if ($check_login > 0) {
            $_SESSION['mess_error_logpass'] = 'Пользователь с таким логином уже существует!';
            header('Location: ../../register.php');
        } else {
            //mysqli_query($connect, "INSERT INTO `users` (`id`, `full_name`, `second_name`, `patronymic`, `login`, `email`, `password`, `avatar`) VALUES (NULL,'$full_name', '$second_name', '$patronymic','$login', '$email', '$pass', '$path')");
            $query = ("INSERT INTO `users` (`id`, `full_name`, `second_name`, `patronymic`, `login`, `email`, `password`, `avatar`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");
            $stmt = $connect_oop->prepare($query);
            $stmt->bind_param('sssssss', $full_name, $second_name, $patronymic, $login, $email, $pass, $path);
            $stmt->execute();

            $_SESSION['message'] = 'Регистрация прошла успешно';
            header('Location: ../../index.php');
        }

    } else {
        $_SESSION['mess_error_logpass'] = 'Пароли не совпадают!';
        header('Location: ../../register.php'); // "../" означает выход из папки на один уровень
            }
    }
    else {
        unset($control_simvol);
        $_SESSION['message_error_form'] = 'В пароле должны быть сивлолы "!*?" итд' ;
        header('Location: ../../register.php');
    }
    }
    else{
    $_SESSION['message_error_form'] = 'Пароль менее 6 символов' ;
    header('Location: ../../register.php');
    }

    }

else {
    $_SESSION['message_error_form'] = 'Заполните все поля';
    header('Location: ../../register.php');
}
