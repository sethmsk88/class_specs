<?php
	/*
		This page is intended to be accessed via an AJAX
		request.

		It responds with a JSON object containing queried
		information for a particular Job Code.
	*/

	// Include my database info
    include "../../shared/dbInfo.php";

    // Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	$param_str_JobCode = $conn->escape_string(trim($_POST['jobCode']));
	$select_classSpec_sql = "
		SELECT *
		FROM class_specs
		WHERE JobCode = ?
	";
	if (!$stmt = $conn->prepare($select_classSpec_sql)) {
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param("s", $param_str_JobCode)) {
		echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	$classSpec_result = $stmt->get_result();
	$classSpec_row = $classSpec_result->fetch_assoc();

	$stmt->close();
	$conn->close();

	/*
		If no results were returned, echo json_encode(null).
		Else, echo json_encode($classSpec_result).
	*/
	if ($classSpec_result->num_rows === 0) {
		echo json_encode(null);
	}
	else {
		echo json_encode($classSpec_row);
	}
?>
