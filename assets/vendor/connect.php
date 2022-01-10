<?php
$connect = mysqli_connect("", "", "", "");
if ($connect->connect_error) {
    die("Ошибка: " . mysqli_connect_error());
}

//*** Объектно-ориентированный стиль для подготовленных запросов ***
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$connect_oop = new mysqli("", "", "", "");
if ($connect_oop->connect_error) {
    die("Ошибка: " . mysqli_connect_error());
}


