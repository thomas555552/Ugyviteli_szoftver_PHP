<?php
/**
 * FŐOLDAL MEGJELENÍTÉSE
 */

/**
 * HEADER.PHP - MENÜ, STÍLUSLAP, JAVASCRIPT KÖNYVTÁRAK
 */
include('header.php')
?>

<?php
/**
 * ÉRTESITÉSEKHEZ SZOLGÁLÓ ELLENŐRZÉS FUTTATÁSA
 */
require_once('check_notifications.php');
?>
    <!-- ALMENÜ -->
    <button class="btn btn-outline-light d-inline-block d-md-none ml-auto" id="sidebarCollapse" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-align-justify fa-1x"></i>
        <span>Menü</span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Főoldal</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>

<?php
/**
 * CSATLAKOZÁS ADATBÁZISHOZ, MAJD AZ EGYES ÉRTESÍTÉSEKHEZ TARTOZÓ LEKÉRDEZÉSEK, ILLETVE MEGJELENÍTÉS
 */
include('db_connect.php');

$count_warn = '';
$count_expired = '';
$count_not_uploaded = '';

$countquery = "SELECT count(*) as total_warn FROM employee_documents WHERE employee_documents.uploaded='WARN'";
$result = mysqli_query($connect, $countquery);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $count_warn = $row['total_warn'];
    }
}

$countquery = "SELECT count(*) as total_expired FROM employee_documents WHERE employee_documents.uploaded='EXPIRED'";
$result = mysqli_query($connect, $countquery);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $count_expired = $row['total_expired'];
    }
}

$countquery = "SELECT count(*) as total_not_uploaded FROM employee_documents WHERE employee_documents.uploaded='FALSE'";
$result = mysqli_query($connect, $countquery);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $count_not_uploaded = $row['total_not_uploaded'];
    }
}


if (($count_warn > 0) || ($count_expired > 0) || ($count_not_uploaded > 0)) {
    ?>
    <!--  INFORMÁCIÓK MEGJELENÍTÉSE   -->
    <div class="">
        <h2>Információk: </h2>
    </div>

    <hr class="custHR">
    <div class="row">
        <div class="col-12">
            <?php
            if (($count_warn > 0)) {
                ?>
                <div class="alert alert-warning">
                    <strong>Figyelem!</strong> <b style="color:red; font-size: 1.1em;"><?php echo $count_warn; ?></b>
                    dokumentum 30 napon belül lejár,
                    kérjük értesítse az alkalmazottakat a pótlásra!!!
                </div>
                <?php
            }
            if (($count_expired > 0)) {
                ?>
                <div class="alert alert-danger">
                    <strong>Lejárt dokumentum!</strong> <b
                            style="color:red; font-size: 1.1em;"><?php echo $count_expired; ?></b> dokumentum lejárt.
                    Kérjük pótolja a hiányzó
                    dokumentumot!!!
                </div>
                <?php
            }
            if (($count_not_uploaded > 0)) {
                ?>
                <div class="alert alert-danger">
                    <strong>Hiányzó dokumentum!</strong> <b
                            style="color:red; font-size: 1.1em;"><?php echo $count_not_uploaded; ?></b> dokumentum
                    nincs feltöltve. Kérjük pótolja a hiányzó dokumentumot!!!
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <?php
}
?>
    <br>
    <!--  UTOLSÓ ELŐZMÉNYEK MEGJELENÍTÉSE, PHP-BEN LEKÉRDEZÉSE   -->
    <div class="">
        <h2>Utolsó tevékenységek: </h2>
    </div>
<?php
$selectquery = "select * from history order by history.date DESC LIMIT 0, 5";

$data = '<div class="table-responsive table-md table-striped" id="history_table">
					<table class="table table-hover table-bordered" >
						<tr class="bg-dark text-white">
                            <th><a id="name"  href="#">Név</a></th>
                            <th><a id="description"  href="#">Leírás</a></th>                       
                            <th><a id="date"  href="#">Dátum</a></th>
                            <th><a id="user"  href="#">Felhasználó</a></th>
						</tr>';


$result = mysqli_query($connect, $selectquery);

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
				<td>' . $row['name'] . '</td>
				<td>' . $row['description'] . '</td>
				<td>' . $date . '</td>
				<td>' . $row['user'] . '</td>
    		</tr>';

    }
}


$data .= '</table> ';

?>


    <hr class="custHR">
    <div class="row">
        <div class="col-12">
            <?php
            echo $data;
            ?>

        </div>
    </div>


<?php
include('footer.php')
?>