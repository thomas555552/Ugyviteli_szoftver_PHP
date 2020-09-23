<?php

/**
 * A EMPLOYEES.PHP - AJAX, ÉS EGYÉB HÍVÁSOK DEFINIÁLÁSA, KEZELÉSE
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
        $squery = " and (employee.name like '%" . $search . "%' or 
        positions.name like '%" . $search . "%') ";
    }
}

/**
 * TÖRLÉS
 */
if (isset($_POST['deleteid'])) {
    $employee_ID = $_POST['deleteid'];

    $selectquery = "SELECT empdoc_ID FROM employee_documents WHERE employee_ID='$employee_ID'";
    $result = mysqli_query($connect, $selectquery);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $empdoc_ID = $row['empdoc_ID'];
            $deletequery = "DELETE FROM notifications WHERE empdoc_ID='$empdoc_ID'";
            mysqli_query($connect, $deletequery);
        }
    }


    $historysql = "SELECT name FROM employee WHERE employee_ID=' $employee_ID ' ";  // Select ONLY one, instead of all
    $resulth = $connect->query($historysql);
    $rowh = $resulth->fetch_assoc();
    $empName = $rowh['name'];


    $deletequery = " DELETE FROM employee_documents WHERE employee_ID='$employee_ID' ";
    mysqli_query($connect, $deletequery);

    $deletequery = " DELETE FROM employee WHERE employee_ID='$employee_ID' ";
    mysqli_query($connect, $deletequery);


    addhistory('Törlés', 'Törölve lett a következő nevű alkalmazott: ' . $empName . '');


    //Delete directory for user with name Employee_ID
    $current_dir = getcwd();
    rmdir($current_dir . "/data/$employee_ID");

}


/**
 * TÁBLÁZAT FELTÖLTÉSE, MEGJELENÉSE A MEGFELELŐ ADATOKKAL, NYOMÓGOMBOKKAL
 * SQL LEKÉRDEZÉSEK
 */
if (isset($_POST['checkbox'])) {
    $check = $_POST['checkbox'];
    if ($check == 'TRUE') {
        $query = "select employee_ID, name as employee_name, job_position as position_name from employee WHERE (job_position is null) order by employee_ID LIMIT $start_from, $rows_perpage";
        $query2 = "select employee_ID, name as employee_name, job_position as position_name from employee WHERE (job_position is null) order by employee_ID";

    } elseif ($check == 'FALSE') {
        $query = "select employee.employee_ID, employee.name as employee_name, positions.name as position_name from employee INNER JOIN  positions ON employee.job_position=positions.position_ID  WHERE 1 " . $squery . " order by employee.employee_ID LIMIT $start_from, $rows_perpage";
        $query2 = "select employee.employee_ID, employee.name as employee_name, positions.name as position_name from employee INNER JOIN  positions ON employee.job_position=positions.position_ID WHERE 1 " . $squery . " order by employee.employee_ID ";

    }
}


$data = '<div class="table-responsive table-md table-striped" id="history_table">
					<table class="table table-hover table-bordered" >
						<tr>
						    <th>Alkalmazott ID</th>
							<th>Alkalmazott neve</th>
                            <th>Alkalmazott pozíciója</th> 
                            <th>Alkalmazott adatainak megjelnítése/szerkesztése</th>
                            <th>Törlés</th>                                             
						</tr>';


$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_array($result)) {
        $data .= '<tr>  
                <td>' . $row['employee_ID'] . '</td>
				<td>' . $row['employee_name'] . '</td>
				<td>' . $row['position_name'] . '</td>
				<td>
    
				    <form method="POST" action="employeepage.php" style="padding-right: 5px;">  
				    <input type="hidden" id="employee_ID" name="employee_ID" value= "' . $row['employee_ID'] . '">                              
					<button type="submit"  class="btn btn-primary btn-block btnMinTable" style="width: 50%" >Kiválaszt</button>
				    </form>    
				</td>
				<td><button value= "' . $row['employee_ID'] . '" onclick="setDeleteID(this.value)" class="btn btn-danger btn-block btnMinTable"  data-toggle="modal" data-target="#deleteModal">Delete</button></td>
    		</tr>';

    }
}


$data .= '</table> ';

/**
 * OLDALSZÁMOK MEGJELENÍTÉSE ÉS A HOZZÁ TARTOZÓ SQL LEKÉRDEZÉS ENNEK MEGHATÁROZÁSÁHOZ
 */
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



