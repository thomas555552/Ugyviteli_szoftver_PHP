<?php
/**
 * E-MAIL KÜLDÉS MEGVALÓSÍTÁSA
 */
$username = '';
session_start();
if (!isset($_SESSION["username"])) {
    header("location:index.php");
} else {
    $username = $_SESSION["username"];


    /**
     * CSATLAKOZÁS AZ ADATBÁZISHOZ, ELŐZMÉNY FÜGGVÉNY
     */
    include('db_connect.php');
    include('history_function.php');


    /**
     * EMPDOC_ID SZERINT AZ EGYES ADATOK MEGHATÁROZÁSA
     */
    if (isset($_POST['empdoc_ID'])) {


        $empdoc_ID = $_POST['empdoc_ID'];
        $employee_name = '';
        $employee_email = '';
        $doc_name = '';
        $exp_date = '';
        $status = '';
        $user_email = '';
        $user_office = '';

        $result = $connect->query("SELECT email,office FROM user WHERE username='$username'");
        while ($rows = $result->fetch_assoc()) {
            $user_email = $rows['email'];
            $user_office = $rows['office'];
        }


        $result = $connect->query("SELECT employee_documents.exp_date, employee_documents.uploaded, employee.name as employee_name, employee.email, documents.name as document_name from employee_documents INNER JOIN employee ON employee.employee_ID=employee_documents.employee_ID INNER JOIN documents ON documents.doc_ID=employee_documents.document_ID WHERE ((employee_documents.uploaded='WARN') OR (employee_documents.uploaded='EXPIRED')) AND (employee_documents.empdoc_ID='$empdoc_ID')");


        while ($rows = $result->fetch_assoc()) {
            $employee_name = $rows['employee_name'];
            $employee_email = $rows['email'];
            $doc_name = $rows['document_name'];
            $exp_date = $rows['exp_date'];
            $status = $rows['uploaded'];
        }


        if ($exp_date != null) {
            $format_expdate = date("Y-m-d", strtotime($exp_date));
        } else {
            $format_expdate = 'Még nem került feltöltésre!';
        }

        $message = '<html><body>';
        $message .= '<h2>Tisztelt ' . $employee_name . '!</h2>';

        /**
         * AZ EGYES ÁLLAPOTOK SZERINTI ÜZENET HOZZÁADÁSA AZ E-MAILHEZ
         */
        if ($status == 'WARN') {
            $message .= '<p><b> Hamarosan lefog járni a következő nevű dokumentuma: </b> ' . $doc_name . ' </p>';

        } elseif ($status == 'EXPIRED') {
            $message .= '<p>Lejárt a következő nevű dokumentuma: ' . $doc_name . '</p>';
        } elseif ($status == 'FALSE') {
            $message .= '<p>Hiányzó dokumentuma: ' . $doc_name . ' </p>';
        }

        $message .= '<p><b> Lejárati ideje: </b>' . $format_expdate . '</p>';
        $message .= '<p>Kérem mihamarabbi pótlását személyesen (iroda: ' . $user_office . '), vagy e-mailen (.pdf formátumban) a 
                     következő címre : <b> ' . $user_email . ' </b></p>';
        $message .= '<p>Megértését köszönöm.</p>';
        $message .= '<p>Ez egy automatikusan generált e-mail, erre az e-mail cimre kérem ne válaszoljon.</p>';


        $message .= '</body></html>';

        /**
         * E-MAIL FEJLÉC BEÁLLÍTÁSA, MAJD KÜLDÉS, ILLETVE ADATBÁZIS FRISSÍTÉS
         */
        $to = $employee_email;
        $subject = 'Ügyviteli szoftver - Értesités dokumentum pótlására!';
        $headers = "From: Ügyviteli szoftver <ugyviteli.szoftver.szakd@gmail.com>\r\n";
        $headers .= "Reply-To: " . $user_email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        if (mail($to, $subject, $message, $headers)) {
            session_start();
            $_SESSION['email_sent'] = "TRUE";

            $update = "UPDATE notifications SET date=CURRENT_TIMESTAMP, status='SENT', sent_times=sent_times+1 WHERE empdoc_ID='" . $empdoc_ID . "'";
            mysqli_query($connect, $update);

            addhistory('E-mail', 'E-mail elküldve a következő nevű alkalmazottnak: ' . $employee_name . '');

            header("location:notifications_email.php");
        } else {
            $_SESSION['email_sent'] = "FALSE";

            header("location:notifications_email.php");
        }

    }
}