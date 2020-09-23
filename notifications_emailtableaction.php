<?php
/**
 * A NOTIFICATIONS_EMAIL.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
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
        $squery = " and (employee.name like '%" . $search . "%'  or
        documents.name like '%" . $search . "%' or 
        notifications.date like '%" . $search . "%') ";
    }
}

/**
 * SZÍN SZERINTI KIVÁLASZTÁS
 */
$colorquery = " ";
if (isset($_POST['color'])) {
    $color = mysqli_real_escape_string($connect, $_POST["color"]);


    if ($color != '') {
        $colorquery = " and (notifications.status like '%" . $color . "%' ) ";
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
							<th>Dokumentum neve</th>
                            <th>Utolsó értesítés dátuma</th> 
                            <th>Értesítések száma</th>
                            <th>E-mail küldése</th>                                
						</tr>';

$query = "SELECT employee.name as employee_name, documents.name as document_name, notifications.empdoc_ID, notifications.date, notifications.status, notifications.sent_times from notifications INNER JOIN employee_documents ON employee_documents.empdoc_ID=notifications.empdoc_ID INNER JOIN employee ON employee_documents.employee_ID=employee.employee_ID INNER JOIN documents ON employee_documents.document_ID=documents.doc_ID WHERE (employee_documents.uploaded<>'TRUE') " . $squery . " " . $colorquery . " order by notifications.sent_times  LIMIT $start_from, $rows_perpage";

$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        if ($row['status'] == 'NEW') {
            $data .= '<tr class="table-warning">';
        } elseif ($row['status'] == 'EXPIRED') {
            $data .= '<tr class="table-danger">';
        } elseif ($row['status'] == 'SENT') {
            $data .= '<tr class="table-secondary">';
        } else {
            $data .= '<tr>';
        }

        $data .= '  
                <td>' . $row['employee_name'] . '</td>
				<td>' . $row['document_name'] . '</td>
				<td>' . $row['date'] . '</td>
				<td>' . $row['sent_times'] . '</td>
				<td>
    
				    <form method="POST" action="send_email.php" style="padding-right: 5px;">  
				    <input type="hidden" id="empdoc_ID" name="empdoc_ID" value= "' . $row['empdoc_ID'] . '">                              
					<button type="submit"  class="btn btn-primary btn-block btnMinTable"  >E-mail küldése</button>
				    </form>    
				</td>
		   		</tr>';

    }
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "SELECT employee.name as employee_name, documents.name as document_name, notifications.empdoc_ID, notifications.date, notifications.status, notifications.sent_times from notifications INNER JOIN employee_documents ON employee_documents.empdoc_ID=notifications.empdoc_ID INNER JOIN employee ON employee_documents.employee_ID=employee.employee_ID INNER JOIN documents ON employee_documents.document_ID=documents.doc_ID WHERE (employee_documents.uploaded<>'TRUE') " . $squery . " " . $colorquery . " order by notifications.sent_times ";

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



