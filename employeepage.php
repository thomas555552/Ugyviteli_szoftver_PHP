<?php
/**
 * KIVÁLASZTOTT ALKALMAZOTT MEGJELENÍTÉSE
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
 * ALKALMAZOTT ADATAINAK FRISSÍTÉSE
 */
if (isset($_POST['submit'])) {

    $employee_ID = mysqli_real_escape_string($connect, $_POST['employee_ID']);
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $phone_number = mysqli_real_escape_string($connect, $_POST['phone_number']);


    /**
     * HA AZ ALKALMAZOTT POZÍCIÓJA VÁLTOZTATÁSRA KERÜL - KORÁBBI HOZZÁRENDELT DOKUMENTUMOK TÖRLÉSE
     * ÚJ SZÜKSÉGES DOKUMENTUMOK HOZZÁADÁSA
     */
    if (isset($_POST['PositionSel'])) {
        $position_select = mysqli_real_escape_string($connect, $_POST['PositionSel']);


        $selectquery = "SELECT empdoc_ID FROM employee_documents WHERE employee_ID='$employee_ID'";
        $result = mysqli_query($connect, $selectquery);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $empdoc_ID = $row['empdoc_ID'];
                $deletequery = "DELETE FROM notifications WHERE empdoc_ID='$empdoc_ID'";
                mysqli_query($connect, $deletequery);
            }
        }

        $deletequery = " DELETE FROM employee_documents WHERE employee_ID='$employee_ID' ";
        mysqli_query($connect, $deletequery);

        $documents_query = "SELECT doc_ID FROM documents WHERE pos_ID='$position_select'";
        $result2 = mysqli_query($connect, $documents_query);
        // add employee_documents if there are more than 0 documents added to posisitions
        if (mysqli_num_rows($result2) > 0) {
            // output data of each row
            while ($row = mysqli_fetch_assoc($result2)) {
                $document_ID = $row['doc_ID'];
                $insertEmp_docs = "INSERT INTO employee_documents(empdoc_ID, employee_ID, document_ID, file_name, exp_date, added_date, uploaded) VALUES (default , '$employee_ID', '$document_ID', null, null, null, null)";
                mysqli_query($connect, $insertEmp_docs);
            }
        }
        $sql = "UPDATE employee SET name='$name', email='$email', address='$address', phone_number='$phone_number', job_position='$position_select' WHERE employee_ID='" . $employee_ID . "'";
        mysqli_query($connect, $sql);
    } else {
        $sql = "UPDATE employee SET name='$name', email='$email', address='$address', phone_number='$phone_number' WHERE employee_ID='" . $employee_ID . "'";
        mysqli_query($connect, $sql);


    }

    addhistory('Módosítás', 'Módosítva lett a következő nevű alkalmazott: ' . $name . '');
}


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
        <li class="nav-item ">
            <a class="nav-link" href="employees.php">Alkalmazottak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="new_employee.php">Új alkalmazott létrehozása</a>
        </li>
        <li class="nav-item <?php active_page('employeepage.php'); ?>">
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
<div class="modal fade" id="uploadModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Feltöltés megerősítése</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Feltöltendő fájl lejárati ideje: </p>

                <div id="expdateinput">

                </div>
                <p><i style="color:tomato">*Csak akkor változtassa meg, ha nem felel meg az automatikusan kiszámított
                        dátum</i></p>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="UploadFile(this.value)" id="uempdoc_ID"
                        class="btn btn-primary btn-block btnMinTable">Feltöltés megerősítése
                </button>
            </div>
        </div>
    </div>
</div>


<?php
/**
 * A KIVÁLASZTOTT ALKALMAZOTT LEKÉRDEZÉSE AZ ADATBÁZISBÓL ÉS MEGJELENITÉSE
 */
if (isset($_POST['employee_ID'])) {
    $querys = "SELECT * FROM employee WHERE employee_ID='" . $_POST['employee_ID'] . "'";
    $result = mysqli_query($connect, $querys);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    ?>
    <div class="">
        <h2><?php echo($row['name']); ?> adatai: </h2>
    </div>
    <form id="updateemployee-form" class="form" method="POST"
          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

        <div class="form-group row">
            <label for="employee_ID" class="col-lg-2 col-form-label">Alkalmazott ID</label>
            <div class="col-lg-10">
                <input type="text" name="employee_ID" id="employee_ID" value="<?php echo($row['employee_ID']); ?>"
                       disabled>
                <input type="hidden" name="employee_ID" value="<?php echo($row['employee_ID']); ?>"/>
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
                <input type="text" id="email" name="email" placeholder="E-mail" value="<?php echo($row['email']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="address" class="col-lg-2 col-form-label">Lakcím</label>
            <div class="col-lg-10">
                <input type="text" id="address" name="address" placeholder="Lakcím"
                       value="<?php echo($row['address']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone_number" class="col-lg-2 col-form-label">Telefonszám</label>
            <div class="col-lg-10">
                <input type="text" id="phone_number" name="phone_number" placeholder="Telefonszám"
                       value="<?php echo($row['phone_number']); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="PositionSel" class="col-lg-2 col-form-label">Alkalmazott pozíciója</label>
            <div class="col-lg-10">
                <?php
                include('db_connect.php');
                $result = $connect->query("SELECT position_ID, name FROM positions");
                ?>
                <select id="PositionSel" name="PositionSel" class="browser-default custom-select custom-select-lg">
                    <?php
                    $emp_job = $row['job_position'];
                    if ($emp_job == NULL) {
                        echo "<option value='' disabled selected> Pozíció választás... </option>";
                    }
                    while ($rows = $result->fetch_assoc()) {
                        $pos_ID = $rows['position_ID'];
                        $pos_name = $rows['name'];
                        if ($emp_job == $pos_ID) {
                            echo "<option value='' disabled selected> $pos_name </option>";
                        } else {
                            echo "<option value='$pos_ID'> $pos_name </option>";
                        }
                    }
                    ?>
                </select>

            </div>
        </div>
        <div class="form-group row">
            <button type="submit" name="submit" class="btn btn-success">Mentés</button>
        </div>
    </form>

    <br/>
    <!-- ALKALMAZOTT DOKUMENTUMAINAK MEGJELENITÉSE -->
    <div class="">
        <h2><?php echo($row['name']); ?> dokumentumai: </h2>
    </div>

    <div class="form">

        <div id="empdoclist">


        </div>

    </div>


    <?php

} else { ?>

    <!-- HA NINCS KIVÁLASZTVA ALKALMAZOTT  -->
    <div class="alert alert-info alert-dismissible fade show">
        <h4 style="text-align: center;">Nincs kiválasztva alkalmazott a szerkesztéshez!</h4>
    </div>


<?php } ?>

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
     * ADATOK BEOLVASÁSA AJAX POST SEGÍTSÉGÉVEL A - DOCUMENTTABLEACTION.PHP -BÓL
     */
    function readRecords() {
        var readrecords = "readrecords";
        var employee_ID = $('#employee_ID').val();
        $.ajax({
            url: "emp_docs_action.php",
            type: "POST",
            data: {employee_ID: employee_ID, readrecords: readrecords},
            success: function (data, status) {
                $('#empdoclist').html(data);
            },

        });
    };

    /**
     * FELTÖLTÉSHEZ TARTOZÓ ADATOK MEGHATÁROZÁSA ÉS AJAX POST HIVÁS
     */
    function UploadFile(empdoc_ID) {
        $('#uploadModal').modal('toggle');
        var added_day = $('#' + empdoc_ID).val();
        var docname = $('#docname' + empdoc_ID).val();
        var file_data = $('#file' + empdoc_ID).prop('files')[0];
        var form_data = new FormData();
        var employee_ID = $('#employee_ID').val();
        var upload = 'upload';
        var exp_dateselect = $('#expdateselect').val();


        form_data.append("file", file_data);
        form_data.append("added_day", added_day);
        form_data.append("docname", docname);
        form_data.append("exp_dateselect", exp_dateselect);
        form_data.append("employee_ID", employee_ID);
        form_data.append("empdoc_ID", empdoc_ID);
        form_data.append("upload", upload);
        //Ajax to send file to upload
        $.ajax({
            url: "emp_docs_action.php",
            type: "POST",
            dataType: 'script',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function (data) {
                readRecords();
            }
        });

    }

    /**
     * EMPDOC ID BEÁLLÍTÁSA FÜGGVÉNY
     */
    function setUploadID(uempdoc_ID) {
        var added_day = $('#' + uempdoc_ID).val();
        var file_data = $('#file' + uempdoc_ID).prop('files')[0];

        if ((added_day == '') || (file_data == undefined)) {
            $('#valid' + uempdoc_ID).text('Kérem kiválasztani a dátumot ÉS a feltöltendő fájlt (*.pdf) !').css("color", "tomato");

        } else {
            if (file_data.size > 26214400) {
                $('#valid' + uempdoc_ID).text('Maximális fájl méret: 25 MB!').css("color", "tomato");
            } else {
                var readexpdate = "readexpdate";
                $('#valid' + uempdoc_ID).text('').css("color", "tomato");
                $.ajax({
                    url: "emp_docs_action.php",
                    type: "POST",
                    data: {readexpdate: readexpdate, added_day: added_day, uempdoc_ID: uempdoc_ID},
                    success: function (datamodal) {
                        $('#expdateinput').html(datamodal);
                        $('#uploadModal').modal('show');
                        $('#uempdoc_ID').val(uempdoc_ID);
                    },

                });
            }
        }
    }


</script>



