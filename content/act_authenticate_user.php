<?php
// Authenticate user

require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/login_functions.php';;

if (isset($_POST['email'], $_POST['p'])) {
	$email = $_POST['email'];
	$hashedPassword = $_POST['p'];

	if (login($email, $hashedPassword, $conn) == true) {
		echo 1;
	}
	else {
		echo 0;
	}
}
else {
	// The correct POST variables were not sent to this page
	header('Location: ../index.php?err=invalid_request');
}



?>