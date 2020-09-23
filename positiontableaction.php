<?php
/**
 * A POSITIONS.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
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
        $squery = " and (position_ID like '%" . $search . "%' or 
        name like '%" . $search . "%') ";
    }
}


$column_name = 'name';
$order = 'asc';

/**
 * TÖRLÉS
 */
if (isset($_POST['deleteid'])) {

    $pos_ID = $_POST['deleteid'];

    $selectquery = "SELECT employee_ID FROM employee WHERE job_position='$pos_ID'";
    $result = mysqli_query($connect, $selectquery);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $employee_ID = $row['employee_ID'];
            $updatequery = "UPDATE employee SET job_position=null WHERE employee_ID='" . $employee_ID . "'";
            mysqli_query($connect, $updatequery);
        }
    }

    $selectquery = "SELECT doc_ID FROM documents WHERE pos_ID='$pos_ID'";
    $result = mysqli_query($connect, $selectquery);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $doc_ID = $row['doc_ID'];

            $selectquery2 = "SELECT empdoc_ID FROM employee_documents WHERE document_ID='$doc_ID'";
            $result2 = mysqli_query($connect, $selectquery2);
            if (mysqli_num_rows($result2) > 0) {
                while ($row2 = mysqli_fetch_array($result2)) {
                    $empdoc_ID = $row2['empdoc_ID'];
                    $deletequery2 = "DELETE FROM notifications WHERE empdoc_ID='$empdoc_ID'";
                    mysqli_query($connect, $deletequery2);
                }
            }

            $deletequery = "DELETE FROM employee_documents WHERE document_ID='$doc_ID'";
            mysqli_query($connect, $deletequery);
        }
    }

    $historysql = "SELECT name FROM positions WHERE position_ID=' $pos_ID ' ";  // Select ONLY one, instead of all
    $resulth = $connect->query($historysql);
    $rowh = $resulth->fetch_assoc();
    $posName = $rowh['name'];


    $deletequery = " DELETE FROM documents WHERE pos_ID='$pos_ID' ";
    mysqli_query($connect, $deletequery);
    $deletequery = " DELETE FROM positions WHERE position_ID='$pos_ID' ";

    mysqli_query($connect, $deletequery);


    addhistory('Törlés', 'Törölve lett a következő nevű pozíció: ' . $posName . ' + (hozzátartozó dokumentum és egyéb bejegyzések)');
}


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */
$query = "select * from positions WHERE 1 " . $squery . " order by " . $column_name . " " . $order . " LIMIT $start_from, $rows_perpage";


$data = '<div class="table-responsive table-md table-striped" id="position_table">
					<table class="table table-hover table-bordered">
						<tr>
							<th>Pozíció ID</th>
                            <th>Pozíció neve</th>
                            <th>Szerkesztés</th>
                            <th>Törlés</th>
						</tr>';


$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        $data .= '<tr>  
				<td>' . $row['position_ID'] . '</td>
				<td>' . $row['name'] . '</td>
				<td>
    
				    <form method="POST" action="positionpage.php" style="padding-right: 5px;">  
				    <input type="hidden" id="position_ID" name="position_ID" value= "' . $row['position_ID'] . '">                              
					<button type="submit"  class="btn btn-primary btn-block btnMinTable" >Szerkesztés</button>
				    </form>    
				</td>
				<td><button value= "' . $row['position_ID'] . '" onclick="setDeleteID(this.value)" class="btn btn-danger btn-block btnMinTable" data-toggle="modal" data-target="#deleteModal">Delete</button></td>
    		</tr>';

    }
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "select * from positions WHERE 1 " . $squery . " order by " . $column_name . " ";
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



