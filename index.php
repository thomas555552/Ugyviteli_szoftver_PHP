<?php
/**
 * LOGIN PAGE, BEJELENTKEZŐ OLDAL MEGJELENÍTÉSE
 */
/**
 * MUNKAMENET INDÍTÁSA
 */
session_start();

if (isset($_SESSION["username"])) {
    header("location:main.php");
}
?>
<!DOCTYPE html>
<html>
<!-- AZ EGYES JAVASCRIPT KÖNYVTÁRAK, STÍLUSLAPOK IMPORTÁLÁSA -->
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- JQuery Validate library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <!-- Custom JS -->
    <script src="js/javascript.js" type="text/javascript"></script>
    <title>Ügyviteli szoftver</title>
</head>
<body>


<?php
/**
 * SIKERTELEN BEJELENTKEZÉS ESETÉN MUNKAMENET ELLENŐRZÉS, ÉS HIBA MEGJELENÍTÉSE
 */
if (isset($_SESSION['loginUser'])) {
    if ($_SESSION['loginUser'] === "false") {
        ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" style="box-shadow: none; width: 100px;" data-dismiss="alert">&times;
            </button>
            <strong>Helytelen felhasználónév vagy jelszó!</strong>
        </div>
        <?php
        unset ($_SESSION['loginUser']);
    }
}
?>


<!-- BEJELENTKEZÉSHEZ SZÜKSÉGES MEZŐK DEFINIÁLÁSA, FORM -->
<div class="modal-dialog text-center">
    <div class="col-12 main-section">
        <div class="modal-content">
            <div class="col-sm-12 logo-img">
                <img src="img/logo.png">

                <form class="col-12" method="post" id="login-form" action="login.php">
                    <div class="form-group">
                        <div class="inputWithIcon">
                            <input type="text" placeholder="Felhasználónév" name="username" id="username">
                            <i class="material-icons">assignment_ind</i>

                        </div>
                        <div class="inputWithIcon">
                            <input type="password" placeholder="Jelszó" name="password" id="password">
                            <i class="material-icons">lock</i>
                        </div>
                    </div>

                    <button type="submit" class="btn bg-primary custombtn" name="login" id="login">Belépés</button>
                </form>

            </div>

        </div>
    </div>
</div>


</body>


</html>
