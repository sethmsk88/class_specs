<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/login_functions.php';;

sec_session_start();

// Unset all session values
$_SESSION = array();

// Get session params
$params = session_get_cookie_params();

// Delete the actual cookie
setcookie(session_name(),
	'', time() - 42000,
	$params["path"],
	$params["domain"],
	$params["secure"],
	$params["httponly"]);

// Destroy session
session_destroy();
header('Location: ../index.php');

?>
