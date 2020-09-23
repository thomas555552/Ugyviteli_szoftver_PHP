<?php

/**
 * A HISTORY.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ
 */
include('db_connect.php');

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
        $squery = " and (name like '%" . $search . "%' or 
        description like '%" . $search . "%' or 
        date like'%" . $search . "%' or
        user like'%" . $search . "%') ";
    }
}
/**
 * SZÍN SZERINTI KIVÁLASZTÁS
 */
$colorquery = " ";
if (isset($_POST['color'])) {
    $color = mysqli_real_escape_string($connect, $_POST["color"]);


    if ($color != '') {
        $colorquery = " and (name like '%" . $color . "%' ) ";
    }
}


$column_name = 'date';
$order = 'desc';


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */

$query = "select * from history WHERE 1 " . $squery . " " . $colorquery . " order by " . $column_name . " " . $order . " LIMIT $start_from, $rows_perpage";


$data = '<div class="table-responsive table-md table-striped" id="history_table">
					<table class="table table-hover table-bordered" >
						<tr>
							<th>ID</th>
                            <th>Név</th>
                            <th>Leírás</th>                       
                            <th>Dátum</th>
                            <th>Felhasználó</th>
						</tr>';


$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        $date = date('Y-m-d - H:i:s', strtotime($row['date']));


        if ($row['name'] == 'Hozzáadás') {
            $data .= '<tr class="table-success">';
        } elseif ($row['name'] == 'Módosítás') {
            $data .= '<tr class="table-warning">';
        } elseif ($row['name'] == 'Törlés') {
            $data .= '<tr class="table-danger">';
        } elseif ($row['name'] == 'E-mail') {
            $data .= '<tr class="table-primary">';
        } elseif ($row['name'] == 'DokFeltöltés') {
            $data .= '<tr class="table-secondary">';
        } else {
            $data .= '<tr>';
        }

        $data .= '
				<td>' . $row['id'] . '</td>
				<td>' . $row['name'] . '</td>
				<td>' . $row['description'] . '</td>
				<td>' . $date . '</td>
				<td>' . $row['user'] . '</td>
    		</tr>';

    }
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "select * from history WHERE 1 " . $squery . " " . $colorquery . " order by " . $column_name . " ";
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



