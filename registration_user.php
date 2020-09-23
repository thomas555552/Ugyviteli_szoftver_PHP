<?php
/**
 * ÚJ FELHASZNÁLÓ LEKEZELÉSE
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
if (empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["name"]) || empty($_POST["email"])) {
    session_start();
    $_SESSION['newUser'] = "ures";

    header("location:new_user.php");
} else {
    if (isset($_POST["username"])) {
        $querys = "SELECT * FROM user WHERE username='" . $_POST["username"] . "'";
        $result = mysqli_query($connect, $querys);
        if (mysqli_num_rows($result) != 0) {
            session_start();
            $_SESSION['newUser'] = "nev";
            header("location:new_user.php");
        } else {
            /**
             * FELHASZNÁLÓ SIKERES LÉTREHOZÁSA
             */
            $username = mysqli_real_escape_string($connect, $_POST["username"]);
            $password = mysqli_real_escape_string($connect, $_POST["password"]);
            $password = password_hash($password, PASSWORD_DEFAULT);
            $name = mysqli_real_escape_string($connect, $_POST["name"]);
            $email = mysqli_real_escape_string($connect, $_POST["email"]);
            $office = mysqli_real_escape_string($connect, $_POST["office"]);
            $query = "INSERT INTO user(username, name, email, password, office) VALUES ('$username', '$name', '$email', '$password', '$office')";

            if (mysqli_query($connect, $query)) {
                session_start();
                $_SESSION['newUser'] = "true";

                addhistory('Hozzáadás', 'Új felhasználó hozzáadása a következő névvel: ' . $username . '');

                header("location:new_user.php");
            } else {
                $_SESSION['newUser'] = "false";
            }

        }

    }
}

