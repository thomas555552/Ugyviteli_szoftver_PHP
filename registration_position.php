<?php
/**
 * ÚJ POZÍCIÓ LEKEZELÉSE
 */
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
}
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');

/**
 * BEVITELI ADATOK ELLENŐRZÉSE
 */
if (empty($_POST["name"])) {
    session_start();
    $_SESSION['newPosition'] = "ures";

    header("location:new_position.php");
}
if (isset($_POST["name"])) {
    $querys = "SELECT * FROM positions WHERE name='" . $_POST["name"] . "'";
    $result = mysqli_query($connect, $querys);
    if (mysqli_num_rows($result) != 0) {
        session_start();
        $_SESSION['newPosition'] = "nev";
        header("location:new_position.php");
    } else {
        /**
         * POZÍCIÓ SIKERES LÉTREHOZÁSA
         */
        $name = mysqli_real_escape_string($connect, $_POST["name"]);
        $query = "INSERT INTO positions(position_ID, name) VALUES (default , '$name')";

        if (mysqli_query($connect, $query)) {
            session_start();
            $_SESSION['newPosition'] = "true";

            addhistory('Hozzáadás', 'Új pozíció hozzáadása a következő névvel: ' . $name . '');

            header("location:new_position.php");
        } else {
            $_SESSION['newPosition'] = "false";
        }

    }
}

