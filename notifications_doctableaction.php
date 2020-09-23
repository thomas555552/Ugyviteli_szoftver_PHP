<?php
/**
 * A NOTIFICATIONS_DOC.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ
 */

include('db_connect.php');


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
        $squery = " and (employee.name like '%" . $search . "%' or 
        positions.name like '%" . $search . "%' or
        documents.name like '%" . $search . "%' or 
        employee_documents.exp_date like '%" . $search . "%') ";
    }
}

/**
 * SZÍN SZERINTI KIVÁLASZTÁS
 */
$colorquery = " ";
if (isset($_POST['color'])) {
    $color = mysqli_real_escape_string($connect, $_POST["color"]);


    if ($color != '') {
        $colorquery = " and (employee_documents.uploaded like '%" . $color . "%' ) ";
    }
}


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */
$data = '<div class="table-responsive table-md table-striped" id="history_table">
					<table class="table table-hover table-bordered" >
						<tr>
						    <th>Alkalmazott Neve</th>
							<th>Alkalmazott Pozíciója</th>
                            <th>Dokumentum neve</th> 
                            <th>Dokumentum lejárati ideje</th>
                            <th>Kiválasztás</th>                                             
						</tr>';


$query = " SELECT employee.employee_ID, employee.name as employee_name, documents.name as document_name, employee_documents.exp_date,employee_documents.uploaded, positions.name as position_name from employee_documents INNER JOIN documents ON employee_documents.document_ID=documents.doc_ID INNER JOIN employee ON employee_documents.employee_ID=employee.employee_ID INNER JOIN positions ON documents.pos_ID=positions.position_ID WHERE (employee_documents.uploaded<>'TRUE') " . $squery . " " . $colorquery . " order by employee_documents.exp_date LIMIT $start_from, $rows_perpage";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        if ($row['exp_date'] != null) {
            $format_expdate = date("Y-m-d", strtotime($row['exp_date']));
        } else {
            $format_expdate = 'Hiányzó dokumentum!';
        }
        if ($row['uploaded'] == 'WARN') {
            $data .= '<tr class="table-warning">';
        } elseif ($row['uploaded'] == 'EXPIRED') {
            $data .= '<tr class="table-danger">';
        } elseif ($row['uploaded'] == 'FALSE') {
            $data .= '<tr class="table-secondary">';
        } else {
            $data .= '<tr>';
        }

        $data .= '  
                <td>' . $row['employee_name'] . '</td>
				<td>' . $row['position_name'] . '</td>
				<td>' . $row['document_name'] . '</td>
				<td>' . $format_expdate . '</td>
				<td>
    
				    <form method="POST" action="employeepage.php" style="padding-right: 5px;">  
				    <input type="hidden" id="employee_ID" name="employee_ID" value= "' . $row['employee_ID'] . '">                              
					<button type="submit"  class="btn btn-primary btn-block btnMinTable"  >Ugrás az alkalmazotthoz</button>
				    </form>    
				</td>
		   		</tr>';

    }
}


$data .= '</table> ';


/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "SELECT employee.employee_ID, employee.name as employee_name, documents.name as document_name, employee_documents.exp_date,employee_documents.uploaded, positions.name as position_name from employee_documents INNER JOIN documents ON employee_documents.document_ID=documents.doc_ID INNER JOIN employee ON employee_documents.employee_ID=employee.employee_ID INNER JOIN positions ON documents.pos_ID=positions.position_ID WHERE (employee_documents.uploaded<>'TRUE') " . $squery . " " . $colorquery . " order by employee_documents.exp_date";

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



