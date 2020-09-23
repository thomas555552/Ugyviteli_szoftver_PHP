<?php
/**
 * A DOCUMENTS.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ
 */
include('db_connect.php');

$data = '';


$query = "SELECT position_ID, name FROM positions WHERE position_ID NOT IN (SELECT pos_ID FROM documents)";

$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        $data .= ' <ul style=" width: 300px;">
                    <li >' . $row['name'] . '</li>
                </ul> ';
    }
} else {
    $data .= 'Nem található ilyen adat!';
}


echo $data;



