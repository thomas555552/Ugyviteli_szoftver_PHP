<?php

/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');

/**
 * FELHASZNÁLÓNÉV, JELSZÓ ELLENŐRZÉSE
 */
if (empty($_POST["username"]) || empty($_POST["password"])) {
    echo '<script>alert("HIBA MIND A KETTO KELL ")</script>';
} else {
    $username = mysqli_real_escape_string($connect, $_POST["username"]);
    $password = mysqli_real_escape_string($connect, $_POST["password"]);
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($connect, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            if (password_verify($password, $row["password"])) {
                session_start();
                $_SESSION["username"] = $username;


                addhistory('Bejelentkezés', 'Bejelentkezett a ' . $username . ' nevű felhasználó');

                header("location:main.php");
            } else {
                session_start();
                $_SESSION['loginUser'] = "false";

                header("location:index.php");
            }
        }
    } else {
        session_start();
        $_SESSION['loginUser'] = "false";

        header("location:index.php");
    }


}

