<?php
/**
 * ÚJ FELHASZNÁLÓ LÉTREHOZÁSA
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
                <a class="nav-link" href="users.php">Felhasználók</a>
            </li>
            <li class="nav-item <?php active_page('new_user.php'); ?>">
                <a class="nav-link" href="#">Új felhasználó</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="userpage.php">Saját adatok módosítása</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>

    <!--  ÚJ ALKALMAZOTT HOZZÁADÁSA FORM   -->
    <div class="">
        <h2>Új felhasználó</h2>
    </div>


    <form id="newUser-form" class="form" method="POST" action="registration_user.php">

        <?php
        /**
         * HIBA ESETÉN MEGJELENÍTETT HIBAÜZENETEK
         */
        if (isset($_SESSION['newUser'])) {
            if ($_SESSION['newUser'] === "true") {
                ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Sikeresen hozzáadva!</strong>
                </div>
                <?php
                unset ($_SESSION['newUser']);
            } else if ($_SESSION['newUser'] === "ures") {
                ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Kérek minden szükséges mezőt kitölteni!</strong>
                </div>
                <?php
            } else if ($_SESSION['newUser'] === "nev") {
                ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Már létezik ilyen felhasználónév!</strong>
                </div>
                <?php
            }
        };
        $_SESSION['newUser'] = '';
        ?>
        <div class="form-group row">
            <label for="username" class="col-lg-2 col-form-label">Felhasználónév</label>
            <div class="col-lg-10">
                <input type="text" name="username" id="username" placeholder="Felhasználónév">
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-lg-2 col-form-label">Jelszó</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="password" placeholder="Jelszó">
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Név</label>
            <div class="col-lg-10">
                <input type="text" id="name" name="name" placeholder="Név">
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-lg-2 col-form-label">E-mail</label>
            <div class="col-lg-10">
                <input type="email" id="email" name="email" placeholder="E-mail">
            </div>
        </div>
        <div class="form-group row">
            <label for="office" class="col-lg-2 col-form-label">Iroda</label>
            <div class="col-lg-10">
                <input type="text" id="office" name="office" placeholder="Iroda">
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