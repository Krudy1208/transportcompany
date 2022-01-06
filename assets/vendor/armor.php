<?php
/* Экранирование кавычек слэшей и прочее */
function ekran($a)
{
    $a = strip_tags($a);
    $a = addslashes($a);
    $a = urldecode($a);
    return $a;
}
