<?php
/**
 * SQL ADATBÁZISHOZ CSATLAKOZÁS, VÁLTOZÓK SEGÍTSÉGÉVEL
 */
$host = "localhost";
$user = "root";
$password = "";
$database = "ugyviteli_szoftver";
$connect = mysqli_connect($host, $user, $password, $database);
if (!$connect) {
    die("Nem lehet kapcsolódni az adatbázishoz!" . mysql_error());
};

