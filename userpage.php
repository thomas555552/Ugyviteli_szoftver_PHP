<?php
/**
 * A BEJELENTKEZETT FELHASZNÁLÓ MEGJELENÍTÉSE
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
 * FELHASZNÁLÓ ADATAINAK FRISSÍTÉSE, VAGY LEKÉRDEZÉSE
 */
if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $office = mysqli_real_escape_string($connect, $_POST['office']);

    $sql = "UPDATE user SET name='$name', email='$email', office='$office' WHERE username='" . $_SESSION['username'] . "'";

    if (mysqli_query($connect, $sql)) {
        addhistory('Módosítás', '' . $name . ' megváltoztatta az adatait');
    }
}

$querys = "SELECT * FROM user WHERE username='" . $_SESSION["username"] . "'";
$result = mysqli_query($connect, $querys);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);


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
            <li class="nav-item">
                <a class="nav-link" href="users.php">Felhasználók</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="new_user.php">Új felhasználó</a>
            </li>
            <li class="nav-item <?php active_page('userpage.php'); ?>">
                <a class="nav-link" href="userpage.php">Saját adatok módosítása</a>
            </li>
        </ul>
    </div>
    </div>
    </nav>
    <!-- FELHASZNÁLÓ MEGJELENITÉSE  -->
    <div class="">
        <h2><?php echo($_SESSION['username']) ?> adatai: </h2>
    </div>


    <form id="updateuser-form" class="form" method="POST"
          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

        <div class="form-group row">
            <label for="username" class="col-lg-2 col-form-label">Felhasználónév</label>
            <div class="col-lg-10">
                <input type="text" name="username" id="username" placeholder="Felhasználónév"
                       value="<?php echo($row['username']); ?>" disabled>
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-lg-2 col-form-label">Jelszó</label>
            <div class="col-lg-10">
                <input type="password" id="password" name="password" placeholder="Jelszó"
                       value="<?php echo($row['password']); ?>" disabled>
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-lg-2 col-form-label">Név</label>
            <div class="col-lg-10">
                <input type="text" id="name" name="name" placeholder="Név" value="<?php echo($row['name']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="email" class="col-lg-2 col-form-label">E-mail</label>
            <div class="col-lg-10">
                <input type="email" id="email" name="email" placeholder="E-mail" value="<?php echo($row['email']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="office" class="col-lg-2 col-form-label">Iroda</label>
            <div class="col-lg-10">
                <input type="text" id="office" name="office" placeholder="Iroda" value="<?php echo($row['office']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <button type="submit" name="submit" class="btn btn-success btnMin">Módosítás</button>

        </div>
    </form>


<?php
/**
 * FOOTER.PHP - DOKUMENTUM VÉGE
 */
include('footer.php')
?>