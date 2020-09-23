<?php
/**
 * ÚJ ALKALMAZOTT LÉTREHOZÁSA
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
            <li class="nav-item ">
                <a class="nav-link" href="employees.php">Alkalmazottak</a>
            </li>
            <li class="nav-item <?php active_page('new_employee.php'); ?>">
                <a class="nav-link" href="new_employee.php">Új alkalmazott létrehozása</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="employeepage.php">Alkalmazott szerkesztése</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>

    <!--  ÚJ ALKALMAZOTT HOZZÁADÁSA FORM   -->
    <div class="">
        <h2>Új alkalmazott hozzáadása: </h2>
    </div>

    <form id="newEmployee-form" class="form" method="POST" action="registration_employee.php">

        <?php
        /**
         * HIBA ESETÉN MEGJELENÍTETT HIBAÜZENETEK
         */
        if (isset($_SESSION['newEmployee'])) {
            if ($_SESSION['newEmployee'] === "true") {
                ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Sikeresen hozzáadva!</strong>
                </div>
                <?php
                unset ($_SESSION['newEmployee']);
            } else if ($_SESSION['newEmployee'] === "ures") {
                ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">
                        &times;
                    </button>
                    <strong>Kérek minden szükséges mezőt kitölteni!</strong>
                </div>
                <?php
            }
        };
        $_SESSION['newEmployee'] = '';
        ?>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Alkalmazott teljes neve</label>
            <div class="col-lg-10">
                <input type="text" name="name" id="name" placeholder="Teljes név">
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-lg-2 col-form-label">E-mail</label>
            <div class="col-lg-10">
                <input type="email" id="email" name="email" placeholder="E-mail">
            </div>
        </div>
        <div class="form-group row">
            <label for="address" class="col-lg-2 col-form-label">Lakhely</label>
            <div class="col-lg-10">
                <input type="text" id="address" name="address"
                       placeholder="Lakhely (Település, Házszám, irányítószám. stb..)">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone_number" class="col-lg-2 col-form-label">Telefonszám</label>
            <div class="col-lg-10">
                <input type="number" name="phone_number" id="phone_number" placeholder="Telefonszám">
            </div>
        </div>
        <div class="form-group row">
            <label for="PositionSelect" class="col-lg-2 col-form-label">Alkalmazott pozíciójának kiválasztása</label>
            <div class="col-lg-10">
                <?php
                /**
                 * CSATLAKOZÁS AZ ADATBÁZISHOZ, POZÍCIÓK LEKÉRDEZÉSE, MEGJELENÍTÉSE
                 */
                include('db_connect.php');
                $result = $connect->query("SELECT position_ID, name FROM positions");
                ?>
                <select id="PositionSelect" name="PositionSelect"
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