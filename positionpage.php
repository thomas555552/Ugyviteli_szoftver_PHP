<?php
/**
 * KIVÁLASZTOTT POZÍCIÓ MEGJELENÍTÉSE
 */
/**
 * HEADER.PHP - MENÜ, STÍLUSLAP, JAVASCRIPT KÖNYVTÁRAK
 */
include('header.php')
?>


<?php
/**
 * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
 */
include('db_connect.php');
include('history_function.php');

/**
 * POZÍCIÓ ADATAINAK FRISSÍTÉSE
 */
if (isset($_POST['submit'])) {

    $position_ID = mysqli_real_escape_string($connect, $_POST['position_ID']);
    $name = mysqli_real_escape_string($connect, $_POST['name']);

    $sql = "UPDATE positions SET name='$name' WHERE position_ID='" . $position_ID . "'";

    if (mysqli_query($connect, $sql)) {

        addhistory('Módosítás', 'Módosítva lett a következő nevű pozíció: ' . $name . '');

    }
}


?>
    <!-- ALMENÜ  -->
    <button class="btn btn-outline-light d-inline-block d-md-none ml-auto" id="sidebarCollapse" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-align-justify fa-1x"></i>
        <span>Menü</span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item ">
                <a class="nav-link" href="positions.php">Pozíciók</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="new_position.php">Új pozíciók</a>
            </li>
            <li class="nav-item <?php active_page('positionpage.php'); ?>">
                <a class="nav-link" href="positionpage.php">Pozíció szerkesztése</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>


<?php
/**
 * A KIVÁLASZTOTT POZÍCIÓ LEKÉRDEZÉSE AZ ADATBÁZISBÓL ÉS MEGJELENITÉSE
 */
if (isset($_POST['position_ID'])) {
    $querys = "SELECT * FROM positions WHERE position_ID='" . $_POST['position_ID'] . "'";
    $result = mysqli_query($connect, $querys);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    ?>
    <div class="">
        <h2><?php echo($row['name']); ?> adatai: </h2>
    </div>
    <form id="updateposition-form" class="form" method="POST"
          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

        <div class="form-group row">
            <label for="position_ID" class="col-lg-2 col-form-label">Pozició ID</label>
            <div class="col-lg-10">
                <input type="text" name="position_ID" id="position_ID" value="<?php echo($row['position_ID']); ?>"
                       disabled>
                <input type="hidden" name="position_ID" value="<?php echo($row['position_ID']); ?>"/>
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Név</label>
            <div class="col-lg-10">
                <input type="text" id="name" name="name" placeholder="Név" value="<?php echo($row['name']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <button type="submit" name="submit" class="btn btn-success">Módosítás</button>
        </div>
    </form>
    <?php

} else { ?>

    <!-- HA NINCS KIVÁLASZTVA POZÍCIÓ  -->
    <div class="alert alert-info alert-dismissible fade show">
        <h4 style="text-align: center;">Nincs kiválasztva pozíció a szerkesztéshez!</h4>
    </div>


<?php } ?>

<?php
/**
 * FOOTER.PHP - DOKUMENTUM VÉGE
 */
include('footer.php')
?>