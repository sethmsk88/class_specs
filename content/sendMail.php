<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '\..\vendor\autoload.php';

$pathToSSLCert = $_SERVER["DOCUMENT_ROOT"] . '\\ssl_cert\\';
// echo realpath(); exit;

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 1; // set debugging to errors mode
    $mail->isSMTP(); // enable SMTP
    $mail->Host = 'smtp.gmail.com'; // gmail
    $mail->Port = 465; // gmail
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled
    $mail->Username = 'sethmsk88@gmail.com';
    $mail->Password = 'mtbcopfqqqaahrgd';
    $mail->setFrom('hrodt-noreply@famu.edu', 'HRODT Apps');
    $mail->addAddress('michael.kerr@famu.edu', 'Seth Kerr');
    $mail->isHTML(true);

    // $mail->Host = 'smtp.office365.com'; // famu
    // $mail->Port = 587; // famu
    // $mail->Username = 'michael.kerr@famu.edu';
    // $mail->Password = '$ethFC0des';
    // $mail->setFrom('hrodt@donotreply.edu', 'HRODT Apps');
    // $mail->setFrom('sethmsk88@gmail.com', 'HRODT Apps');
    // $mail->addReplyTo('hrodt@donotreply.edu', 'HRODT Apps');

    // Content
    $mail->Subject = "Email Subject";
    $mail->Body = "Email content.";

    // Required to allow a self-signed SSL certificate
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message failed to send<br>';
    echo 'Mailer error: ' . $e->getMessage();
}

?>
