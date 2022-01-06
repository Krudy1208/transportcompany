<?php
session_start();
require_once 'connect.php';

/* ЗАЩИТА ОТ СПАМА*/
$time_finish = time();
$ipUser = $_SERVER['REMOTE_ADDR']; //ip пользователя

$check_ip = mysqli_query($connect, "SELECT * FROM `bots`WHERE ip = '$ipUser'");
if (mysqli_num_rows($check_ip) > 0) {
    exit('Ваш IP '."($ipUser)".' заблокирован в связи с подозрительной деятельностью.');
}

if ( $time_finish - $_SESSION['time_start'] < 3 || $_POST['username']|| $_POST['capcha']){
    mysqli_query($connect, "INSERT INTO `bots` (`date`, `ip`) VALUES (CURRENT_TIMESTAMP(),'$ipUser')");
    exit('Ваш IP заблокирован в связи с подозрительной деятельностью.');
}
unset($_SESSION['time_start']);
/* ЗАЩИТА ОТ СПАМА*/

if ($_POST["login"] && $_POST["password"])
    {
        $salt1 = "*#4!mfpo";
        $salt2 = "dg@&3g@*";
        $login = strip_tags($_POST["login"]);
        $login = addslashes($login);

        $pass = sha1( $salt1.$_POST["password"].$salt2);

        //$check_user = mysqli_query($connect, "SELECT * FROM `users`WHERE `login` = '$login' AND `password` = '$pass'"); //проверяем наличие пользователя
        $query = 'SELECT * FROM `users` WHERE `login` = ? AND `password` = ?';

        $stmt = $connect_oop->prepare($query);
        $stmt -> bind_param('ss', $login, $pass);
        $stmt->execute();
        $result =$stmt->get_result();
        $check_user =$result->num_rows;

        //if (mysqli_num_rows($check_user) > 0)
        if ($check_user > 0)
        {
            //$user = mysqli_fetch_assoc($check_user); //преобразуем sql запрос в массив
            $user = $result->fetch_assoc();

                $_SESSION['user'] = [
                  "id"=> $user['id'],
                    "login"=> $user['login'],
                  "full_name"=> $user['full_name'],
                  "second_name"=> $user['second_name'],
                  "patronymic"=> $user['patronymic'],
                  "email"=> $user['email'],
                  "avatar"=> $user['avatar'],
                ];

                //unset($_POST["login"], $_POST["password"]);

                if($user['login'] == 'admin'){
                    header('Location: ../../admin_profile.php');
                }
                else
                header('Location: ../../profile.php');
            }

        else {
            $_SESSION['message'] = 'Неверный логин или пароль';

            ++$_SESSION['count_err'];
            if ($_SESSION['count_err']>=2)
            {
                $_SESSION['message'] = 'Опять неверный логин или пароль';

                    if ($_SESSION['count_err']>=3)
                    {
                        $_SESSION['message'] = 'Последняя попытка';
                        $_SESSION['message_all'];
                        header('Location: ../../index.php');

                    }
                    if ($_SESSION['count_err']>=4){
                        mysqli_query($connect, "INSERT INTO `bots` (`date`, `ip`, `id`) VALUES (CURRENT_TIMESTAMP(),'null', '$id')");
                        $_SESSION['message'] = 'Ваш аккаунт заблокирован в связи с превышением неверных запросов';
                    }
                        unset($_SESSION['count_err']);
                header('Location: ../../index.php');

            }
            header('Location: ../../index.php');


        }
    }
else header('Location: ../../index.php');
