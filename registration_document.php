<?php
/**
 * ÚJ DOKUMENTUM LEKEZELÉSE
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
if (empty($_POST["name"]) || empty($_POST["exp_day"]) || empty($_POST["DocumentsSelect"])) {
    session_start();
    $_SESSION['newDocument'] = "ures";

    header("location:new_document.php");
}
if (isset($_POST["name"]) && isset($_POST['DocumentsSelect'])) {
    $querys = "SELECT * FROM documents WHERE name='" . $_POST["name"] . "' AND pos_ID='" . $_POST["DocumentsSelect"] . "' ";
    $result = mysqli_query($connect, $querys);
    if (mysqli_num_rows($result) != 0) {
        session_start();
        $_SESSION['newDocument'] = "nev";
        header("location:new_document.php");
    } else {
        /**
         * DOKUMENTUM SIKERES LÉTREHOZÁSA
         * ELLENŐRZÉS AZ EGYES ALKALMAZOTTAKNÁL, AHOL EZ A POZÍCIÓ VAN MEGADVA
         * ÉS ALKALMAZOTT DOKUMENTUMOK LÉTREHOZÁSA
         */
        $name = mysqli_real_escape_string($connect, $_POST["name"]);
        $exp_day = mysqli_real_escape_string($connect, $_POST["exp_day"]);
        $pos_ID = mysqli_real_escape_string($connect, $_POST["DocumentsSelect"]);
        $query = "INSERT INTO documents(doc_ID, pos_ID, name, exp_day) VALUES (default , '$pos_ID', '$name', '$exp_day')";

        if (mysqli_query($connect, $query)) {
            session_start();
            $_SESSION['newDocument'] = "true";

            $last_id = mysqli_insert_id($connect);

            $selectquery = "SELECT employee_ID FROM employee WHERE job_position='" . $pos_ID . "' ";
            $resultselect = mysqli_query($connect, $selectquery);

            if (mysqli_num_rows($resultselect) > 0) {

                while ($row = mysqli_fetch_array($resultselect)) {
                    $employee_ID = $row['employee_ID'];
                    $insert_empdoc = "INSERT INTO employee_documents(empdoc_ID, employee_ID, document_ID, file_name, exp_date, added_date, uploaded) VALUES (default , '$employee_ID', '$last_id', null, null, null, 'FALSE')";
                    mysqli_query($connect, $insert_empdoc);
                }
            }

            $historysql = "SELECT name FROM positions WHERE position_ID=' $pos_ID ' ";  // Select ONLY one, instead of all
            $resulth = $connect->query($historysql);
            $rowh = $resulth->fetch_assoc();
            $posName = $rowh['name'];

            addhistory('Hozzáadás', 'Új dokumentum hozzáadása a következő névvel: ' . $name . ' a ' . $posName . ' pozícióhoz');

            header("location:new_document.php");
        } else {
            $_SESSION['newDocument'] = "false";
        }

    }
}


