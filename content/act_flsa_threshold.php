<?php
	define("APP_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/class_specs/");
	
	/***  CHECK IF PAGE WAS POSTED TO  ***/
	if (!isset($_SERVER["REQUEST_METHOD"]) ||
		$_SERVER["REQUEST_METHOD"] != "POST") {
		exit;
	}

	include_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
	include_once '../includes/functions.php';

	sec_session_start(); // make session variables available

	$insert_threshold_sql = "
		INSERT INTO hrodt.flsa_threshold (threshold, user_id, dateUpdated)
		VALUES (?,?,NOW())
	";

	$param_double_threshold = parseMoney(trim($_POST['threshold']));
	$param_int_user_id = $_SESSION['user_id'];

	if (!$stmt = $conn->prepare($insert_threshold_sql)) {
		header('Location: ' . APP_PATH . '?page=flsa_threshold&failure=1');
	} else if (!$stmt->bind_param('di', $param_double_threshold, $param_int_user_id)) {
		header('Location: ' . APP_PATH . '?page=flsa_threshold&failure=1');
	} else if (!$stmt->execute()) {
		header('Location: ' . APP_PATH . '?page=flsa_threshold&failure=1');
	} else {
		header('Location: ' . APP_PATH . '?page=flsa_threshold&success=1');
	}
?>
