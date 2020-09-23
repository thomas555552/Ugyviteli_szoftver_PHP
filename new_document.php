<?php
/**
 * ÚJ DOKUMENTUM LÉTREHOZÁSA
 */
/**
 * HEADER.PHP - MENÜ, STÍLUSLAP, JAVASCRIPT KÖNYVTÁRAK
 */
include('header.php')
?>

    <!--  ALMENÜ   -->
    <button class="btn btn-outline-light d-inline-block d-md-none ml-auto" id="sidebarCollapse" type="button"
            data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-align-justify fa-1x"></i>
        <span>Menü</span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="documents.php">Dokumentumok</a>
            </li>
            <li class="nav-item <?php active_page('new_document.php'); ?>">
                <a class="nav-link" href="new_document.php">Új dokumentum hozzáadása</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>

    <!--  ÚJ DOKUMENTUM HOZZÁADÁSA FORM   -->
    <div class="">
        <h2>Új dokumentum hozzáadása</h2>
    </div>


    <form id="newDocument-form" class="form" method="POST" action="registration_document.php">

        <?php
        /**
         * HIBA ESETÉN MEGJELENÍTETT HIBAÜZENETEK
         */
        if (isset($_SESSION['newDocument'])) {
            if ($_SESSION['newDocument'] === "true") {
                ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Sikeresen hozzáadva!</strong>
                </div>
                <?php
                unset ($_SESSION['newDocument']);
            } else if ($_SESSION['newDocument'] === "ures") {
                ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Kérek minden szükséges mezőt kitölteni!</strong>
                </div>
                <?php
            } else if ($_SESSION['newDocument'] === "nev") {
                ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Már hozzá van adva ilyen nevű dokumentum a kiválasztott pozícióhoz!</strong>
                </div>
                <?php
            }
        };
        $_SESSION['newDocument'] = '';
        ?>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Dokumentum megnevezése</label>
            <div class="col-lg-10">
                <input type="text" name="name" id="name" placeholder="Dokumentum neve">
            </div>
        </div>
        <div class="form-group row">
            <label for="exp_day" class="col-lg-2 col-form-label">Lejárati idő</label>
            <div class="col-lg-10">
                <input type="number" name="exp_day" id="exp_day" placeholder="Lejárati idő napokban megadva (XXXX)">
            </div>
        </div>


        <div class="form-group row">
            <label for="DocumentsSelect" class="col-lg-2 col-form-label">Pozíció kiválasztása</label>
            <div class="col-lg-10">
                <?php
                /**
                 * CSATLAKOZÁS AZ ADATBÁZISHOZ, POZÍCIÓK LEKÉRDEZÉSE, MEGJELENÍTÉSE
                 */
                include('db_connect.php');
                $result = $connect->query("SELECT position_ID, name FROM positions");
                ?>
                <select id="DocumentsSelect" name="DocumentsSelect"
                        class="browser-default custom-select custom-select-lg">
                    <option value="" disabled selected>Pozíció választás...</option>
                    <?php
                    while ($rows = $result->fetch_assoc()) {
                        $pos_ID = $rows['position_ID'];
                        $pos_name = $rows['name'];
                        echo "<option value='$pos_ID'> $pos_name </option>";
                    }
                    ?>
                </select>

            </div>
        </div>
        <div class="form-group row">
            <button type="submit" class="btn btn-primary btnMin">Hozzáad</button>

        </div>
    </form>


<?php
/**
 * FOOTER.PHP - DOKUMENTUM VÉGE
 */
include('footer.php')
?>