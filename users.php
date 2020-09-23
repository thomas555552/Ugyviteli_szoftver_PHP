<?php
/**
 * FELHASZNÁLÓK MEGJELENÍTÉSE TÁBLÁZAT
 */

/**
 * HEADER.PHP - MENÜ, STÍLUSLAP, JAVASCRIPT KÖNYVTÁRAK
 */
include('header.php')
?>

<style>
    .btn {
        font-size: 1.1em;

    }

</style>
<!-- ALMENÜ  -->
<button class="btn btn-outline-light d-inline-block d-md-none ml-auto" id="sidebarCollapse" type="button"
        data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
    <i class="fas fa-align-justify fa-1x"></i>
    <span>Menü</span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item active">
            <a class="nav-link" href="#">Felhasználók</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="new_user.php">Új felhasználó</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="userpage.php">Saját adatok módosítása</a>
        </li>
    </ul>
</div>
</div>
</nav>


<!-- TÖRLÉSHEZ MODAL MEGJELENÍTÉS ÉS DEFINIÁLÁS -->
<style>
    .modal-content {
        color: white;
        padding: 0px;
    }

    .modal-body p {
        color: white;
        margin: 0 auto;
    }

    .close {
        padding: 5px !important;
        width: 40px !important;
    }


</style>

<!-- Modal -->
<div class="modal fade" id="deleteModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Törlés</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tényleg törölni szeretné?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="deleteUser(this.value)" id="deleteID"
                        class="btn btn-danger btn-block btnMinTable">Törlés megerősítése
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FELHASZNÁLÓK MEGJELENÍTÉSE   -->
<div class="">
    <h2>Felhasználók: </h2>
</div>


<div class="form">

    <div class="form-group row">

        <label for="search" class="col-lg-2 col-form-label">Keresés</label>
        <div class="col-lg-10">
            <input type="text" name="search" id="search" placeholder="Keresett szöveg">
        </div>
    </div>
    <div class="input-group mb-3" style="max-width: 300px">
        <div class="input-group-prepend">
            <label class="input-group-text" for="inputGroupSelect01">Oldalanként: </label>
        </div>
        <select class="custom-select" id="rowNumbers">

            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="50">50</option>
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
     * ADATOK BEOLVASÁSA AJAX POST SEGÍTSÉGÉVEL A - USERTABLEACTION.PHP -BÓL
     */
    function readRecords() {
        var pageone = 1;
        var rows_perpage = $('.custom-select').val();
        var readrecords = "readrecords";
        $.ajax({
            url: "usertableaction.php",
            type: "POST",
            data: {readrecords: readrecords, rows_perpage: rows_perpage},
            success: function (data, status) {
                $('#table').html(data);
                $(".pagination_link[id='" + pageone + "']").css("background-color", "#575a5e");
            },

        });
    }


    /**
     * TÖRLÉS ID BEÁLLÍTÁSA FÜGGVÉNY
     */
    function setDeleteID(deleteid) {
        $('#deleteID').val(deleteid);
        var a = $('#deleteID').val();

    }

    /**
     * TÖRLÉS FÜGGVÉNY VÉGREHAJTÁSA A KIVÁLASZTOTT AZONOSÍTÓ SZERINT ÉS MODAL MEGJELNÍTÉS, MEGERŐSÍTÉS (AJAX)
     */
    function deleteUser(deleteid) {
        $('#deleteModal').modal('toggle');
        if (deleteid != '') {

            $('#search').val('');
            $.ajax({
                url: "usertableaction.php",
                type: 'POST',
                data: {deleteid: deleteid},

                success: function (data, status) {
                    readRecords();
                }
            });
        }
    }

    /**
     * 'LIVE' KERESŐ FÜGGVÉNY AZ ADATOK KÖZÖTT (AJAX)
     */
    function SearchText(searchquery) {
        var pageone = 1;
        var rows_perpage = $('.custom-select').val();
        $.ajax({
            url: "usertableaction.php",
            method: "post",
            data: {searchquery: searchquery, rows_perpage: rows_perpage},
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
        var rows_perpage = $('.custom-select').val();
        var searchquery = $('#search').val();
        $.ajax({
            url: "usertableaction.php",
            method: "post",
            data: {searchquery: searchquery, page: page, rows_perpage: rows_perpage},
            success: function (data) {
                $('#table').html(data);
                $(".pagination_link[id='" + page + "']").css("background-color", "#575a5e");
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
    $('.custom-select').change(function () {
        var rows_perpage = $('.custom-select').val();
        PaginationPage(1);
    });


</script>
