<?php
/**
 * ALKALMAZOTTAK LEJÁRT, HAMAROSAN LEJÁRT DOKUMENTUMAINAK MEGJELENÍTÉSE
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
            <a class="nav-link" href="notifications_email.php">E-mail-ek kezelése/küldése alkalmazottaknak</a>
        </li>
        <li class="nav-item <?php active_page('notifications_doc.php'); ?>">
            <a class="nav-link" href="notifications_doc.php">Alkalmazottak dokumentumai értesítései/kezelése</a>
        </li>
    </ul>
</div>
</div>
</nav>

<!-- ALKALMAZOTTAKHOZ ÉS DOKUMENTUMAIKHOZ TARTOZÓ ÉRTESÍTÉSEK   -->
<div class="">
    <h2>Alkalmazottak dokumentumai értesítései/kezelése: </h2>
</div>

<div class="form">

    <div class="form-group row">

        <label for="search" class="col-lg-2 col-form-label">Keresés</label>
        <div class="col-lg-10">
            <input type="text" name="search" id="search" placeholder="Keresett szöveg">
        </div>
    </div>
    <div class="input-group mb-3" style="max-width: 650px">
        <div class="input-group-prepend">
            <label class="input-group-text" for="inputGroupSelect01">Oldalanként: </label>
        </div>
        <select class="custom-select" id="rowNumbers" style="margin-right: 20px;">

            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="50">50</option>
        </select>


        <div class="input-group-prepend">
            <label class="input-group-text" for="inputGroupSelect02">Figyelmeztetés: </label>
        </div>
        <select class="custom-select" id="color">
            <option value="">Összes</option>
            <option value="FALSE" class="table-secondary">Hiányzó</option>
            <option value="WARN" class="table-warning">Hamarosan lejár</option>
            <option value="EXPIRED" class="table-danger">Lejárt</option>
        </select>
    </div>


    <div id="table">

    </div>

</div>


<?php
/**
 * FOOTER.PHP - DOKUMENTUM VÉGE
 */
include('footer.php')
?>

<!-- SCRIPTEK - JQUERY, FÜGGVÉNYEK, AJAX HÍVÁSOK, EZEN OLDAL MEGFELELŐ MEGJELENÍTÉSÉHEZ -->
<script>

    /**
     * HA KÉSZ AZ OLDAL ADATOK LEKÉRDEZÉSE
     */
    $(document).ready(function () {
        readRecords();
    });

    /**
     * ADATOK BEOLVASÁSA AJAX POST SEGÍTSÉGÉVEL A - NOTIFICATIONS_DOCTABLEACTION.PHP -BÓL
     */
    function readRecords() {
        var pageone = 1;
        var rows_perpage = $('#rowNumbers').val();
        var readrecords = "readrecords";
        var color = $('#color').val();
        $.ajax({
            url: "notifications_doctableaction.php",
            type: "POST",
            data: {readrecords: readrecords, rows_perpage: rows_perpage, color: color},
            success: function (data, status) {
                $('#table').html(data);
                $(".pagination_link[id='" + pageone + "']").css("background-color", "#575a5e");
            },

        });
    }


    /**
     * 'LIVE' KERESŐ FÜGGVÉNY AZ ADATOK KÖZÖTT (AJAX)
     */
    function SearchText(searchquery) {
        var pageone = 1;
        var rows_perpage = $('#rowNumbers').val();
        var color = $('#color').val();
        $.ajax({
            url: "notifications_doctableaction.php",
            method: "post",
            data: {searchquery: searchquery, rows_perpage: rows_perpage, color: color},
            success: function (data) {
                $('#table').html(data);
                $(".pagination_link[id='" + pageone + "']").css("background-color", "#575a5e");

            }
        });
    }

    /**
     * BILLENTYŰ FELENGEDÉSE ESEMÉNY A KERESŐHÖZ
     */
    $('#search').keyup(function () {
        var search = $(this).val();
        if (search != '') {
            SearchText(search);
        } else {
            readRecords();
        }
    });


    /**
     * AZ OLDALSZÁMOZÁSHOZ TARTOZÓ AJAX FÜGGVÉNY
     */
    function PaginationPage(page) {
        var rows_perpage = $('#rowNumbers').val();
        var searchquery = $('#search').val();
        var color = $('#color').val();


        $.ajax({
            url: "notifications_doctableaction.php",
            method: "post",
            data: {searchquery: searchquery, page: page, rows_perpage: rows_perpage, color: color},
            success: function (data) {
                $('#table').html(data);
                $(".pagination_link[id='" + page + "']").css("background-color", "#575a5e");

            }
        });
    }


    /**
     * KIVÁLASZTOTT SZÍN SZERINTI LEKÉRDEZÉS, MEGJELENÍTÉS
     */
    function ColorSelect(color) {
        var pageone = 1;
        var rows_perpage = $('#rowNumbers').val();
        var searchquery = $('#search').val();
        $.ajax({
            url: "notifications_doctableaction.php",
            method: "post",
            data: {searchquery: searchquery, rows_perpage: rows_perpage, color: color},
            success: function (data) {
                $('#table').html(data);
                $(".pagination_link[id='" + pageone + "']").css("background-color", "#575a5e");
            }
        });
    }


    /**
     * A KIVÁLASZTOTT OLDALSZÁM ESEMÉNY
     */
    $(document).on('click', '.pagination_link', function () {
        var page = $(this).attr("id");
        PaginationPage(page);
    });
    /**
     * OLDAL/ ADATOK SZÁMA MEGHATÁROZÁSA
     */
    $('#rowNumbers').change(function () {
        var rows_perpage = $('#rowNumbers').val();
        PaginationPage(1);
    });

    /**
     * KIVÁLASZTOTT SZÍN VÁLTOZÁSA ESEMÉNY
     */
    $('#color').change(function () {
        var color = $('#color').val();
        ColorSelect(color);
    });

</script>

