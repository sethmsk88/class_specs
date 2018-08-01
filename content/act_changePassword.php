<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/class_specs/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/class_specs/includes/globals.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

// This is the page we will return to after the password has been changed
$redirectUrl = "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/class_specs/index.php?page=homepage";

try {
	$hashedTempPw = hash("sha512", $_POST['tempPw']);

	if (!$stmt = $conn->prepare("
		select tempPasswordCreated
		from secure_login.users
		where id = ?
			and tempPassword = ?
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("is", $_POST['uid'], $hashedTempPw)) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}
	$stmt->store_result();
	$stmt->bind_result($tempPasswordCreated);
	$stmt->fetch();

	// Check to see if user exists
	if ($stmt->num_rows === 0) {
		throw new Exception("Error! No user found!");
	}

	// Make sure tempPasswordCreated date is less than 1 day old
	if (strtotime($tempPasswordCreated) < strtotime('-1 day', time())) {
		throw new Exception("Password reset link has expired!");
	}

	// Update user's password
	if (!$stmt = $conn->prepare("
		update secure_login.users
		set password = ?
		where id = ?
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("si", $_POST['hashedNewPassword'], $_POST['uid'])) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}

	$redirectUrl .= "&pwreset=true";

	// Clear the temporary password
	if (!$stmt = $conn->prepare("
		update secure_login.users
		set tempPassword = ''
		where id = ?
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("i", $_POST['uid'])) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}
?>

Redirecting...
<script>
window.location.href = "<?= $redirectUrl ?>";
</script>
