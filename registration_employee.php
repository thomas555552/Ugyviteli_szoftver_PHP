<?php
/**
 * ÚJ ALKALMAZOTT LEKEZELÉSE
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
if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["address"]) || empty($_POST["PositionSelect"])) {
    session_start();
    $_SESSION['newEmployee'] = "ures";

    header("location:new_employee.php");
}

/**
 * ALKALMAZOTT SIKERES LÉTREHOZÁSA
 * ALKALMAZOTTAKHOZ A KIVÁLASZTOTT POZÍCIÓ SZERINT DOKUMENTUMOK HOZZÁRENDELÉSE
 */
if (isset($_POST["name"]) AND isset($_POST["email"]) AND isset($_POST["address"]) AND isset($_POST["PositionSelect"])) {

    $name = mysqli_real_escape_string($connect, $_POST["name"]);
    $email = mysqli_real_escape_string($connect, $_POST["email"]);
    $address = mysqli_real_escape_string($connect, $_POST["address"]);
    $phone_number = mysqli_real_escape_string($connect, $_POST["phone_number"]);
    $job_position = mysqli_real_escape_string($connect, $_POST["PositionSelect"]);
    $query = "INSERT INTO employee(employee_ID, name, email, address, phone_number, job_position) VALUES (default, '$name', '$email', '$address', '$phone_number', '$job_position')";

    if (mysqli_query($connect, $query)) {
        //session_start();
        $_SESSION['newEmployee'] = "true";
        $employee_ID = mysqli_insert_id($connect);

        $historysql = "SELECT name FROM positions WHERE position_ID=' $job_position ' ";  // Select ONLY one, instead of all
        $resulth = $connect->query($historysql);
        $rowh = $resulth->fetch_assoc();
        $posName = $rowh['name'];

        addhistory('Hozzáadás', 'Új alkalmazott hozzáadása a következő névvel: ' . $name . ' a ' . $posName . ' pozícióhoz');


        echo($employee_ID);
        // Select doc id where position id is selected from form
        $documents_query = "SELECT doc_ID FROM documents WHERE pos_ID='$job_position'";
        $result2 = mysqli_query($connect, $documents_query);
        // add employee_documents if there are more than 0 documents added to posisitions
        if (mysqli_num_rows($result2) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result2)) {
                $document_ID = $row['doc_ID'];
                $insertEmp_docs = "INSERT INTO employee_documents(empdoc_ID, employee_ID, document_ID, file_name, exp_date, added_date, uploaded) VALUES (default , '$employee_ID', '$document_ID', null, null, null, 'FALSE')";
                mysqli_query($connect, $insertEmp_docs);
            }
        }

        //New directory for user with name Employee_ID
        $current_dir = getcwd();
        if (mkdir($current_dir . "/data/$employee_ID", 0755)) {
            header("location:new_employee.php");
        }

    } else {
        $_SESSION['newEmployee'] = "false";
    }

}


