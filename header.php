<?php
/**
 * MUNKAMENET INDÍTÁSA, ELLENŐRZÉSE
 */
session_start();

if (!isset($_SESSION["username"])) {
    header("location:index.php");
}
?>

<!DOCTYPE html>
<html>

<!-- HEADER - AZ EGYES STÍLUSLAPOK, JAVASCRIPT KÖNYVTÁRAK IMPORTÁLÁSA -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Ügyviteli szoftver</title>


    <!-- Bootstrap CSS and JS  -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js"
            integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ"
            crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js"
            integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY"
            crossorigin="anonymous"></script>

    <!-- JQuery Validate library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <!-- Custom JS -->
    <script src="js/javascript.js" type="text/javascript"></script>


</head>

<body>
<?php
/**
 * AKTUÁLIS OLDAL MÁS SZÍNNEL VALÓ MEGJELENÍTÉSÉHEZ FÜGGVÉNY
 */
function active_page($current_page)
{
    $url_array = explode('/', $_SERVER['REQUEST_URI']);
    $url = end($url_array);
    if ($current_page == $url) {
        echo 'active'; //class name in css
    }
}

?>

<!-- IRÁNYÍTÓPULT  -->
<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <b> Irányítópult</b>
            <a href="#"></a>
        </div>

        <ul class="list-unstyled components">
            <li class="<?php active_page('main.php'); ?> ">
                <a href="main.php">
                    Főoldal
                    <i class="fas fa-copy"></i>

                </a>

            </li>
            <li class="<?php active_page('users.php');
            active_page('new_user.php');
            active_page('userpage.php'); ?>">
                <a href="users.php">
                    Felhasználók
                    <i class="fas fa-id-card-alt"></i>

                </a>
            </li>
            <li class="<?php active_page('positions.php');
            active_page('positionpage.php');
            active_page('new_position.php'); ?>">
                <a href="positions.php">
                    Pozíciók
                    <i class="fas fa-street-view"></i>

                </a>

            </li>
            <li class="<?php active_page('documents.php');
            active_page('new_document.php'); ?>">
                <a href="documents.php">
                    Dokumentumok
                    <i class="fas fa-copy"></i>

                </a>
            </li>
            <li class="<?php active_page('employees.php');
            active_page('new_employee.php');
            active_page('employeepage.php'); ?>">
                <a href="employees.php">
                    Alkalmazottak
                    <i class="fas fa-user-friends"></i>

                </a>
            </li>
            <li class="<?php active_page('notifications_email.php');
            active_page('notifications_doc.php'); ?>">
                <a href="notifications_email.php">
                    Értesítések kezelése
                    <i class="fas fa-bell"></i>

                </a>
            </li>
            <li class="<?php active_page('history.php'); ?>">
                <a href="history.php">
                    Előzmények
                    <i class="fas fa-history"></i>

                </a>
            </li>
        </ul>


        <ul class="list-unstyled components">

            </li>
            <li class="<?php active_page('userpage.php'); ?>">
                <a href="userpage.php">
                    <?php echo($_SESSION['username']) ?>
                    <i class="fas fa-user"></i>

                </a>
            </li>
            <li class="">
                <a href="logout.php">
                    Kijelentkezés
                    <i class="fas fa-sign-out-alt"></i>

                </a>
        </ul>
    </nav>


    <!-- OLDAL TARTALMA -->
    <div id="content">

        <nav class="navbar navbar-expand-md navbar-custom">
            <div class="container-fluid">

                <button type="button" id="sidebarCollapse" class="btn btn-outline-info">
                    <i class="fas fa-align-left fa-1x"></i>
                    <span>Irányítópult</span>
                </button>
