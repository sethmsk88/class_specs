<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '\..\vendor\autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 1; // set debugging to errors mode
    $mail->isSMTP(); // enable SMTP
    $mail->Host = 'smtp.gmail.com'; // gmail
    $mail->Port = 465; // gmail
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled
    $mail->Username = 'famutraining@gmail.com';
    $mail->Password = 'tfolkoimlxvnjjpi'; // app password from famutraining@gmail.com account
    $mail->setFrom('hrodt-noreply@famu.edu', 'HRODT Apps');
    $mail->addReplyTo('hrodt-noreply@famu.edu', 'HRODT Apps');
    $mail->addAddress('michael.kerr@famu.edu', 'Seth Kerr');
    $mail->isHTML(true);

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
