<?php
session_start();
unset($_GET);
//unset($_SESSION[$rand.'user'.$time]);
unset($_SESSION['user']);

header('Location: ../../index.php');