<?php
/**
 * ALKALMAZOTTAK MEGJELENÍTÉSE TÁBLÁZAT
 */

/**
 * HEADER.PHP - MENÜ, STÍLUSLAP, JAVASCRIPT KÖNYVTÁRAK
 */
include('header.php')
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
        <li class="nav-item <?php active_page('employees.php'); ?>">
            <a class="nav-link" href="employees.php">Alkalmazottak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="new_employee.php">Új alkalmazott létrehozása</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="employeepage.php">Alkalmazott szerkesztése</a>
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
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Törlés</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tényleg törölni szeretné?</p>
                <p>A következők kerülnek törlésre: </p>
                <ul>
                    <li>A kiválasztott alkalmazott</li>
                    <li>Alkalmazott dokumentumai</li>
                    <li>Esetlegesen ezen dokumentumokhoz tartozó értesítések</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="deleteEmployee(this.value)" id="deleteID"
                        class="btn btn-danger btn-block btnMinTable">Törlés megerősítése
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ALKALMAZOTTAK MEGJELENÍTÉSE   -->
<div class="">
    <h2>Alkalmazottak: </h2>
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

    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="checkbox" name="checkbox" value="FALSE">
        <label class="custom-control-label" for="checkbox">Alkalmazottak melyeknek nincs pozíciójuk</label>
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
     * ADATOK BEOLVASÁSA AJAX POST SEGÍTSÉGÉVEL A - EMPLOYEETABLEACTION.PHP -BÓL
     */
    function readRecords() {
        var pageone = 1;
        var rows_perpage = $('.custom-select').val();
        var readrecords = "readrecords";

        var checkbox = $('#checkbox').val();
        $.ajax({
            url: "employeetableaction.php",
            type: "POST",
            data: {readrecords: readrecords, rows_perpage: rows_perpage, checkbox: checkbox},
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
        var rows_perpage = $('.custom-select').val();

        var checkbox = $('#checkbox').val();
        $.ajax({
            url: "employeetableaction.php",
            method: "post",
            data: {searchquery: searchquery, rows_perpage: rows_perpage, checkbox: checkbox},
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


        var checkbox = $('#checkbox').val();
        $.ajax({
            url: "employeetableaction.php",
            method: "post",
            data: {searchquery: searchquery, page: page, rows_perpage: rows_perpage, checkbox: checkbox},
            success: function (data) {
                $('#table').html(data);
                $(".pagination_link[id='" + page + "']").css("background-color", "#575a5e");

            }
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
     * CHECKBOX-HOZ ESEMÉNY, MIT JELENITSÜNK MEG
     */
    $('#checkbox').change(function () {
        if ($(this).prop("checked")) {
            $(this).val('TRUE');
            readRecords();
            $('#search').prop('disabled', true);
        } else {
            $(this).val('FALSE');
            readRecords();
            $('#search').prop('disabled', false);
        }
    });


    /**
     * TÖRLÉS FÜGGVÉNY VÉGREHAJTÁSA A KIVÁLASZTOTT AZONOSÍTÓ SZERINT ÉS MODAL MEGJELNÍTÉS, MEGERŐSÍTÉS (AJAX)
     */
    function deleteEmployee(deleteid) {
        $('#deleteModal').modal('toggle');
        if (deleteid != '') {

            $('#search').val('');
            $.ajax({
                url: "employeetableaction.php",
                type: 'POST',
                data: {deleteid: deleteid},

                success: function (data, status) {
                    readRecords();
                }
            });
        }
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
