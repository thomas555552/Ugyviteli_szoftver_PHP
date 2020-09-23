<?php
/**
 * A USERS.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
 */
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');
session_start();

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
        $squery = " and (username like '%" . $search . "%' or 
        name like '%" . $search . "%' or 
        email like'%" . $search . "%' or
        office like'%" . $search . "%') ";
    }
}


$column_name = 'username';
$order = 'asc';

/**
 * TÖRLÉS
 */
if (isset($_POST['deleteid'])) {
    $username = $_POST['deleteid'];
    $deletequery = " DELETE FROM user WHERE username='$username' ";
    mysqli_query($connect, $deletequery);

    addhistory('Törlés', 'Törölve lett a következő nevű felhasználó: ' . $username . '');

}


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */

$query = "select * from user WHERE 1 " . $squery . " order by " . $column_name . " " . $order . " LIMIT $start_from, $rows_perpage";


$data = '<div class="table-responsive table-md table-striped" id="user_table">
					<table class="table table-hover table-bordered">
						<tr>
							<th>Felhasználónév</th>
                            <th>Név</th>
                            <th>E-mail</th>                       
                            <th>Iroda</th>
                            <th>Törlés</th>
						</tr>';


$result = mysqli_query($connect, $query);
$useradmin = $_SESSION['username'];
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        $data .= '<tr>  
				<td>' . $row['username'] . '</td>
				<td>' . $row['name'] . '</td>
				<td>' . $row['email'] . '</td>
				<td>' . $row['office'] . '</td>
				<td>';


        if ($useradmin == 'admin') {
            $data .= '<button onclick="setDeleteID(this.value)" value= "' . $row['username'] . '" class="btn btn-danger btn-block btnMinTable" data-toggle="modal" data-target="#deleteModal">Törlés</button>
				</td>
    		</tr>';
        } else {
            $data .= '<button value= "' . $row['username'] . '" class="btn btn-danger btn-block btnMinTable" disabled>Törlés</button>
				</td>
    		</tr>';
        }
    }
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
$query2 = "select * from user WHERE 1 " . $squery . " order by " . $column_name . " ";
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



