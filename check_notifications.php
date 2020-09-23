<?php
/**
 * ÉRTESÍTÉSEK HOZZÁADÁSA - HA 30 NAPON BELÜL LEJÁR (LEJÁRATI IDŐ ÉS JELENLEGI)
 * AZ ALKALMAZOTT DOKUMENTUMAI KÖZÜL VALAMELYIK HOZZÁAD EGY BEJEGYZÉST A NOTIFICATION (ÉRTESÍTÉSEK) TÁBLÁHOZ
 * -HA MÁR LEJÁRT A DOKUMENTUM A JELENLEGI NAPHOZ KÉPEST AKKOR FRISSÍTI A ALKALMAZOTT DOKUMENTUMÁT LEJÁRT ÁLLAPOTÚRA
 * ÉS A ÉRTESÍTÉS BELI BEJEGYZÉST STÁTUSZÁT IS
 */
include('db_connect.php');

/**
 * 30 NAPON BELÜL LEJÁR A DOKUMENTUM
 */
$selectsql = 'select empdoc_ID, exp_date, uploaded from employee_documents WHERE ( DATEDIFF(exp_date,CURRENT_TIMESTAMP) BETWEEN 0 AND 30 ) AND (employee_documents.empdoc_ID not in (select empdoc_ID from notifications) )';


$result = mysqli_query($connect, $selectsql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $empdoc_ID = $row['empdoc_ID'];
        $insertsql = "INSERT INTO `notifications`(`notification_ID`, `empdoc_ID`, `date`, `status`, `sent_times`) VALUES (default ,'$empdoc_ID',default ,'NEW',0)";
        mysqli_query($connect, $insertsql);
        $updatesql = "UPDATE employee_documents SET uploaded='WARN' WHERE empdoc_ID='" . $empdoc_ID . "'";
        mysqli_query($connect, $updatesql);
    }
}


/**
 * LEJÁRT A DOKUMENTUM
 */

$selectsql = ' SELECT empdoc_ID, exp_date, uploaded from employee_documents WHERE DATEDIFF(exp_date,CURRENT_TIMESTAMP) < 0';
$result = mysqli_query($connect, $selectsql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $empdoc_ID = $row['empdoc_ID'];
        $updatesql = "UPDATE employee_documents SET uploaded='EXPIRED' WHERE empdoc_ID='" . $empdoc_ID . "'";
        mysqli_query($connect, $updatesql);
        $updatesql = "UPDATE notifications SET date=default , status='EXPIRED' WHERE (empdoc_ID='" . $empdoc_ID . "') AND (status<>'EXPIRED')";
        mysqli_query($connect, $updatesql);

    }
}
