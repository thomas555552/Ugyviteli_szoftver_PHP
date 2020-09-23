<?php
/**
 * ELŐZMÉNY FÜGGVÉNY
 */
include('history_function.php');

/**
 * FELHASZNÁLÓ KIJELENTKEZÉS, MUNKAMENET TÖRLÉSE ÉS ÁTIRÁNYÍTÁS
 */
session_start();

$username = $_SESSION["username"];

addhistory('Kijelentkezés', 'Kijelentkezett a ' . $username . ' nevű felhasználó');

session_destroy();
header("location:index.php");