<?php
/**
 * A KIVÁLASZTOTT ALKALMAZOTT DOKUMENTUMAINAK MEGJELENÍTÉSE, FELTÖLTÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');


if (isset($_POST["readexpdate"])) {
    $added_day = $_POST['added_day'];
    $empdoc_ID = $_POST['uempdoc_ID'];
    $datamodal = '';

    $sql = "SELECT document_ID FROM employee_documents WHERE empdoc_ID='" . $empdoc_ID . "' ";
    $result = $connect->query($sql);
    $row = $result->fetch_assoc();
    $document_ID = $row['document_ID'];

    $sql = "SELECT exp_day FROM documents WHERE doc_ID='" . $document_ID . "'  ";
    $result = $connect->query($sql);
    $row = $result->fetch_assoc();
    $exp_day = $row['exp_day'];


    $new_exp_date = date("Y-m-d", strtotime($added_day . '+ ' . $exp_day . ' days'));
    $datamodal .= '<input id="expdateselect" value="' . $new_exp_date . '" name="date" type="date" onkeydown="return false" required>';

    echo($datamodal);
}


/**
 * DOKUMENTUM FELTÖLTÉSE - TÁBLÁZAT FRISSÍTÉSE, FÁJL NEVÉNEK MEGVÁLTOZTATÁSA ÉS ÁTHELYEZÉSE AZ ALKALMAZOTT MAPPÁJÁHOZ
 */
if (isset($_POST["upload"])) {
    $employee_ID = $_POST['employee_ID'];
    $empdoc_ID = $_POST['empdoc_ID'];
    $added_day = $_POST['added_day'];
    $target_directory = "data/$employee_ID/";
    $docname = $_POST['docname'];
    $exp_dateselect = $_POST['exp_dateselect'];
    $target_file = $target_directory . basename($_FILES["file"]["name"]);
    $filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_name = $added_day . "-" . $docname . "." . $filetype;
    $newfilename = $target_directory . $added_day . "-" . $docname . "." . $filetype;
    move_uploaded_file($_FILES["file"]["tmp_name"], $newfilename);


    $new_exp_date = date("Y-m-d H:i:s", strtotime($exp_dateselect));

    $added_day_tosql = date("Y-m-d H:i:s", strtotime($added_day));

    $updatequery = "UPDATE `employee_documents` SET `file_name`='$file_name', `exp_date`='$new_exp_date', `added_date`='$added_day_tosql', `uploaded`='TRUE' WHERE empdoc_ID='" . $empdoc_ID . "'";
    mysqli_query($connect, $updatequery);

    $deletequery = "DELETE FROM notifications WHERE empdoc_ID='" . $empdoc_ID . "' ";
    mysqli_query($connect, $deletequery);


    $historysql = "SELECT name FROM employee WHERE employee_ID=' $employee_ID ' ";
    $resulth = $connect->query($historysql);
    $rowh = $resulth->fetch_assoc();
    $empName = $rowh['name'];

    addhistory('DokFeltöltés', '' . $file_name . ' dokumentum feltöltése a következő nevű alkalmazotthoz: ' . $empName . '');

}

/**
 * ÉRTESÍTÉSEKET MEGHATÁROZÓ FRISSÍTŐ FUTTATÁSA
 */
require_once('check_notifications.php');

/**
 * A KIVÁLASZTOTT ALKALMAZOTT ADATAINAK MEGJELENÍTÉSE
 */
if (isset($_POST['employee_ID'])) {
    $emp_ID = $_POST['employee_ID'];
    $data = '';
    $file_array = array();

    $result = $connect->query("SELECT employee_documents.empdoc_ID, employee_documents.file_name, employee_documents.exp_date, employee_documents.added_date, employee_documents.uploaded, documents.name FROM employee_documents INNER JOIN documents ON employee_documents.document_ID=documents.doc_ID WHERE employee_ID= '" . $emp_ID . "'");
    while ($rows = $result->fetch_assoc()) {

        $file_array[] = $rows['file_name'];

        $data .= '<div class="card text-white bg-secondary">';

        if ($rows['uploaded'] == 'TRUE') {
            $data .= '<div class="card-header bg-success"></div>';
        } elseif (($rows['uploaded'] == 'FALSE') || ($rows['uploaded'] == 'EXPIRED')) {
            $data .= '<div class="card-header bg-danger"></div>';
        } elseif ($rows['uploaded'] == 'WARN') {
            $data .= '<div class="card-header bg-warning"></div>';
        } else {
            $data .= '<div class="card-header bg-light"></div>';
        }

        $data .= '           <div class="card-body">
                   <h4 class="card-title" >Dokumentum neve: ' . $rows['name'] . ' </h4> 
                   <p class="card-text" style="color:black; font-weight: bold;">Fájl neve: ';
        if (empty($rows['file_name'])) {
            $data .= 'N/A </p>';
        } else {
            $data .= ' ' . $rows['file_name'] . '  <form action="openpdf.php" method="POST" target="_blank"><input type="hidden" name="file" id="file" value="' . $rows['file_name'] . '"><input type="hidden" name="emp_ID" id="emp_ID" value="' . $emp_ID . '"><button class="btn btn-dark btnMinTable" style="width: 25%;" type="submit">Megnyitás</button></form></p> ';
        };
        if (!empty($rows['added_date'])) {
            $added_date = date('Y. m. d.', strtotime($rows['added_date']));
        } else {
            $added_date = '';
        }
        if (!empty($rows['exp_date'])) {
            $exp_date = date('Y. m. d.', strtotime($rows['exp_date']));
        } else {
            $exp_date = '';
        }
        $data .= ' <p class="card-text text-white" >Érvényesség kezdete: ' . $added_date . '</p>
                       <p class="card-text  text-white" >Lejárati ideje: ' . $exp_date . '</p>
                    </div>
                    <div class="card-footer bg-dark"><h4>Új fájl feltöltése</h4>
                    <input type="hidden" id="docname' . $rows['empdoc_ID'] . '" value="' . $rows['name'] . '">  
                    <label for="date" >Dokumentum érvényességének kezdete (Dátum kiválasztása):
                    <input id="' . $rows['empdoc_ID'] . '" name="date" type="date" onkeydown="return false" required> </label>
                     
                    <input type="file" id="file' . $rows['empdoc_ID'] . '" name="file" accept="application/pdf">  
                    <p id="valid' . $rows['empdoc_ID'] . '"></p>
                    <button  value="' . $rows['empdoc_ID'] . '" onclick="setUploadID(this.value)" class="btn btn-primary btnMin"  data-target="#uploadModal">Feltöltés</button>
                    
                    </div>
                   
                
            </div> <br> ';


    }
    /**
     * KORÁBBAN FELTÖLTÖTT, VAGY LECSERÉLT DOKUMENTUMAINAK MEGHATÁROZÁSA AZ ALKALMAZOTT MAPPÁBÓL, MEGJELENÍTÉSE
     */
    $data .= '<div class="">
        <h4>Korábban feltöltött (archivált) dokumentumok: </h4>
                 </div>
                 <div class="d-sm-flex flex-nowrap justify-content-center">';

    $fileList = glob('data/' . $emp_ID . '/*');
    $strlength = strlen($emp_ID);
    $strlength = $strlength + 6;
    $otherfile = 0;
    $maxperrow = 0;
    foreach ($fileList as $filename) {
        $subfilename = substr($filename, $strlength);


        if (!(in_array($subfilename, $file_array))) {
            $maxperrow = $maxperrow + 1;
            $data .= ' <form action="openpdf.php" method="POST" target="_blank"> <input type="hidden" name="file" id="file" value="' . $subfilename . '"><input type="hidden" name="emp_ID" id="emp_ID" value="' . $emp_ID . '"> <button class="btn btn-dark btnMinTable" style="width: 100%" type="submit">' . $subfilename . '</button> </form>
                &nbsp;  ';
            if ((($maxperrow % 4) == 0) AND ($maxperrow != 0)) {
                $data .= '</div><br>  <div class="d-sm-flex flex-nowrap justify-content-center">';
            }
            $otherfile = 0;
        }
    }
    $data .= '  </div>';

    echo $data;
}