<?php
/**
 * PDF FÁJL MEGNYITÁSA ÚJ LAPON
 */


if (isset($_POST['file'])) {

    $filename = $_POST['file'];

    if (isset($_POST['emp_ID'])) {
        $dir = $_POST['emp_ID'];
        $file = 'data/';
        $file .= $dir;
        $file .= '/';
        $file .= $filename;

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        @readfile($file);

    }


}

