<?php
/**
 * ELŐZMÉNY HOZZÁADÁSA FÜGGVÉNY
 */

function addhistory($name, $description)
{

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $username = $_SESSION['username'];

    require('db_connect.php');

    $query = "INSERT INTO `history`(`id`, `name`, `description`, `date`, `user`) VALUES (default ,'$name', '$description', default ,'$username')";


    $result = mysqli_query($connect, $query);
    if ($result) {
    } else {
        echo('HIBA: Előzmény hozzáadása sikertelen !!!');
    }


}

