<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("location:index.php");
}
?>
<?php
/**
 * A DOCUMENTS.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');


/**
 * AZ EGYES BEÁLLÍTOTT (POST SEGÍTSÉGÉVEL) VÁLTOZÓK, LEKEZELÉSE ÉS EGYÉB TEVÉKENYSÉGEK
 */
/**
 * OLDALSZÁM ÉS OLDAL/ ADATOK SZÁMA
 */
$rows_perpage = '';
$page = '';
$output = '';
if (isset($_POST["page"])) {
    $page = $_POST["page"];
} else {
    $page = 1;
}

if (isset($_POST["rows_perpage"])) {
    $rows_perpage = $_POST["rows_perpage"];
} else {
    $rows_perpage = 10;
}


$start_from = ($page - 1) * $rows_perpage;


/**
 * KERESŐ
 */
$squery = " ";
if (isset($_POST['searchquery'])) {
    $search = mysqli_real_escape_string($connect, $_POST["searchquery"]);


    if ($search != '') {
        $squery = " and (positions.name like '%" . $search . "%' or 
        documents.name like '%" . $search . "%') ";
    }
}


/**
 * TÖRLÉS
 */
if (isset($_POST['deleteid'])) {

    $doc_ID = $_POST['deleteid'];

    $selectquery = "SELECT empdoc_ID FROM employee_documents WHERE document_ID='$doc_ID'";
    $result = mysqli_query($connect, $selectquery);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $empdoc_ID = $row['empdoc_ID'];
            $deletequery = "DELETE FROM notifications WHERE empdoc_ID='$empdoc_ID'";
            mysqli_query($connect, $deletequery);
        }
    }

    $historysql = "SELECT documents.name as doc_name,positions.name as pos_name FROM documents INNER JOIN positions ON positions.position_ID=documents.pos_ID WHERE doc_ID=' $doc_ID ' ";  // Select ONLY one, instead of all
    $resulth = $connect->query($historysql);
    $rowh = $resulth->fetch_assoc();
    $doc_name = $rowh['doc_name'];
    $pos_name = $rowh['pos_name'];


    $deletequery = " DELETE FROM employee_documents WHERE document_ID='$doc_ID' ";
    mysqli_query($connect, $deletequery);
    $deletequery = " DELETE FROM documents WHERE doc_ID='$doc_ID' ";
    mysqli_query($connect, $deletequery);

    addhistory('Törlés', 'Törölve lett a következő nevű dokumentum: ' . $doc_name . ' a ' . $pos_name . ' nevű pozícióból');

}


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */

$query = "select positions.position_ID, documents.doc_ID ,documents.name as document_name, documents.exp_day, positions.name as position_name from documents INNER JOIN positions ON documents.pos_ID=positions.position_ID WHERE 1 " . $squery . " order by positions.position_ID LIMIT $start_from, $rows_perpage";


$data = '<div class="table-responsive table-md table-striped" id="history_table">
					<table class="table table-hover table-bordered" >
						<tr>
							<th>Pozíció neve</th>
                            <th>Dokumentumok és lejárati idejük</th>                                              
						</tr>';


$result = mysqli_query($connect, $query);
$documents;
if (mysqli_num_rows($result) > 0) {

    for ($i = 0; $documents[$i] = mysqli_fetch_assoc($result); $i++) ;

// Delete last empty one
    array_pop($documents);


    $positionCHECK = $documents[0]['position_ID'];
    $data .= '<tr>
                    <td>' . $documents[0]['position_name'] . '</td><td><ul class="list-group">
                    ';

    for ($i = 0; $i < count($documents); $i++) {

        $boolCHECK = false;
        if ($positionCHECK != $documents[$i]['position_ID']) {
            $data .= '</ul></td></tr> <tr>
                    <td>' . $documents[$i]['position_name'] . '</td><td><ul class="list-group">';
            $boolCHECK = true;
        }
        if ($positionCHECK == $documents[$i]['position_ID'] || $boolCHECK) {
            $boolCHECK = false;
            $data .= '<li class="list-group-item list-group-item-dark"> ' . $documents[$i]['document_name'] . ' - ' . $documents[$i]['exp_day'] . ' nap  <button value= "' . $documents[$i]['doc_ID'] . '" onclick="setDeleteID(this.value)" class="btn btn-danger float-right" style="width: 20%;" data-toggle="modal" data-target="#deleteModal">X</button></li>';
        }


        $positionCHECK = $documents[$i]['position_ID'];
    }
    $data .= '</ul></td> </tr>';
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "select positions.position_ID, documents.name as document_name, documents.exp_day, positions.name as position_name from documents INNER JOIN positions ON documents.pos_ID=positions.position_ID WHERE 1 " . $squery . " order by positions.position_ID ";
$page_result = mysqli_query($connect, $query2);
$total_records = mysqli_num_rows($page_result);
$total_pages = ceil($total_records / $rows_perpage);

if ($total_pages > 12) {
    for ($i = 1; $i <= $total_pages; $i++) {
        if (($i == 1) OR ($i == $page - 1) OR ($i == $page) OR ($i == $page + 1) OR ($i == $total_pages)) {
            if (($i == $page - 1) AND ($page != 2) AND ($page != 3) AND ($page != $total_pages)) {
                $data .= "<span class='pagination_link_disabled'>...</span>";
            }
            $data .= "<span class='pagination_link' id='" . $i . "'>" . $i . "</span>";
            if (($i == $page + 1) AND ($page != 1) AND ($page != $total_pages - 1) AND ($page != $total_pages - 2)) {
                $data .= "<span class='pagination_link_disabled'>...</span>";
            }
        }
        if (($page == 1)) {
            if ($i == $page + 2) {
                $data .= "<span class='pagination_link_disabled'>...</span>";
            }
        }

        if (($page == $total_pages)) {
            if ($i == $page - 2) {
                $data .= "<span class='pagination_link_disabled'>...</span>";
            }
        }
    }
} else {
    for ($i = 1; $i <= $total_pages; $i++) {
        $data .= "<span class='pagination_link' id='" . $i . "'>" . $i . "</span>";
    }
}


echo $data;



