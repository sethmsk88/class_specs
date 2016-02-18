<?php
require_once $_SERVER['DOCUMENT_ROOT'] . 'bootstrap/apps/shared/db_connect.php';
require_once '../includes/functions.php';

if (isset($_POST['username'], $_POST['p'])) {
	$username = $_POST['username'];
	$password = $_POST['p']; // hashed password

	if (login($username, $password, $conn) == true) {
		echo 1;
	}
	else {
		echo 0;
	}

/*	if (login($email, $password, $conn) == true) {
		// Login success
		//header('Location: ' . APP_PATH . '?page=protected_page');
		return 
	}
	else {
		// Login failed
		header('Location: ../index.php?error=1');
		exit();
	}*/
}
else {
	// The correct POST variables were not sent to this page
	header('Location: ../index.php?err=invalid_request');
}

?>