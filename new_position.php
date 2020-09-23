<?php
/**
 * ÚJ POZÍCIÓ LÉTREHOZÁSA
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
                <a class="nav-link" href="positions.php">Pozíciók</a>
            </li>
            <li class="nav-item <?php active_page('new_position.php'); ?>">
                <a class="nav-link" href="new_position.php">Új pozíciók</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="positionpage.php">Pozíció szerkesztése</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>

    <!--  ÚJ POZÍCIÓ HOZZÁADÁSA FORM   -->
    <div class="">
        <h2>Új pozíció</h2>
    </div>


    <form id="newPosition-form" class="form" method="POST" action="registration_position.php">

        <?php
        /**
         * HIBA ESETÉN MEGJELENÍTETT HIBAÜZENETEK
         */
        if (isset($_SESSION['newPosition'])) {
            if ($_SESSION['newPosition'] === "true") {
                ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Sikeresen hozzáadva!</strong>
                </div>
                <?php
                unset ($_SESSION['newPosition']);
            } else if ($_SESSION['newPosition'] === "ures") {
                ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Kérek minden szükséges mezőt kitölteni!</strong>
                </div>
                <?php
            } else if ($_SESSION['newPosition'] === "nev") {
                ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Már létezik ilyen nevű pozíció!</strong>
                </div>
                <?php
            }
        };
        $_SESSION['newPosition'] = '';
        ?>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Pozíció neve</label>
            <div class="col-lg-10">
                <input type="text" name="name" id="name" placeholder="Pozíció neve">
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