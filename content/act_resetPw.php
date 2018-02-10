<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '\..\vendor\autoload.php';

// This is the page we will return to after the email has been sent
$redirectUrl = "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/class_specs/index.php?page=homepage";

function GenerateTempPassword($length = 12)
{
    $charSet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ`~#^&*-_';
    $tempPw = '';
    for ($i = 0; $i < $length; $i++) {
        $tempPw .= $charSet[rand(0, strlen($charSet)-1)];
    }
    return $tempPw;
}

// Connect to DB (Provides $conn variable)
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

// Get the user's record from the users table
try {
	if (!$stmt = $conn->prepare("
		select id
		from secure_login.users
		where email = ?
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("s", $_POST['email'])) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}
	$stmt->store_result();
	$stmt->bind_result($userId);
	$stmt->fetch();

	// Check to see if user exists
	if ($stmt->num_rows === 0) {
		throw new Exception("No user exists with email address <b>{$_POST['email']}</b><br>");
	}	
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

// Set temporary password for user
$tempPw = GenerateTempPassword();
$hashedTempPw = hash("sha512", $tempPw);
try {
	if (!$stmt = $conn->prepare("
		update secure_login.users
		set tempPassword = ?,
			tempPasswordCreated = NOW()
		where id = ?
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("si", $hashedTempPw, $userId)) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

// Send temporary password to user's email address
$mail = new PHPMailer(true);
try {
    // $mail->SMTPDebug = 2; // set debugging to errors mode
    $mail->isSMTP(); // enable SMTP
    $mail->Host = 'smtp.office365.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'TLS';
    $mail->Username = 'famutraining@famu.edu';
    $mail->Password = 'Tr@!n!ng';
    $mail->setFrom('hrodt-noreply@famu.edu', 'HR/ODT Apps');
    $mail->addReplyTo('hrodt-noreply@famu.edu', 'HR/ODT Apps');
    $mail->addAddress($_POST['email']);
    $mail->isHTML(true);

    // Content
    $mail->Subject = "HR/ODT Apps Password Reset";

    $resetPwLink = "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/class_specs/?page=changePassword&uid=" . $userId . "&tempPw=" . urlencode($tempPw);
    $mail->Body = "<h2>Hello!</h2>
    	You are receiving this email because we received a password reset request for your account.<br>
    	Please use the following link to reset your password. <b><a href=\"".$resetPwLink."\" target=\"_blank\">Reset your password</a></b><br>
    	<i>This link is valid for 24 hours.</i><br><br>
    	If you did not request this password reset, no further action is required.";

    // Required to allow a self-signed SSL certificate
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->send();

    $redirectUrl .= "&emailsent=true";
} catch (Exception $e) {
    $redirectUrl .= "&emailsent=false";
}
?>

Redirecting...
<script>
window.location.href = "<?= $redirectUrl ?>";
</script>
