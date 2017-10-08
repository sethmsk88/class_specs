<?php
	/***  CHECK IF PAGE WAS POSTED TO  ***/
	if (!isset($_SERVER["REQUEST_METHOD"]) ||
		$_SERVER["REQUEST_METHOD"] != "POST") {
		exit;
	}

	/*
		Delete Class Spec from class_specs table.
		If there is more than one entry for a single class spec, the oldest
		one will be deleted.
		Required Param: $_POST['jobCode']
	*/
	if (isset($_POST['jobCode'])) {

		// Include my database info
	    include "../../shared/dbInfo.php";

	    // Connect to DB
		$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
		if (mysqli_connect_error()){
			echo mysqli_connect_error();
			exit();
		}
		
		/*
			Select the class spec by JobCode.
			If there are duplicates, delete the
			oldest class spec ID.
		*/
		$param_str_jobCode = $_POST['jobCode'];

		$sel_classSpec_sql = "
			SELECT ID
			FROM class_specs
			WHERE JobCode = ?
		";
		// If class spec is specified by dept id
		if (strlen($_POST['deptID']) > 0) {
			$sel_classSpec_sql .= " AND DeptID = ?";
			$sel_classSpec_sql .= " ORDER BY ID ASC";
			$stmt = $conn->prepare($sel_classSpec_sql);
			$stmt->bind_param("si", $param_str_jobCode, $_POST['deptID']);
		} else {
			$sel_classSpec_sql .= " ORDER BY ID ASC";
			$stmt = $conn->prepare($sel_classSpec_sql);
			$stmt->bind_param("s", $param_str_jobCode);
		}
		$stmt->execute();
		$sel_classSpec_result = $stmt->get_result();
		$stmt->close();
		$sel_classSpec_row = $sel_classSpec_result->fetch_assoc();

		/*
			Delete the class spec with the lowest ID number.
			The lowest ID is the first row in the previous query result.
		*/
		$param_int_ID = $sel_classSpec_row['ID'];
		$del_classSpec_sql = "
			DELETE FROM class_specs
			WHERE ID = ?
		";
		if (!$stmt = $conn->prepare($del_classSpec_sql)){
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}
		if (!$stmt->bind_param("i", $param_int_ID)) {
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}

		$stmt->close();

		/*
			Mark Job Code as inactive in pay_levels table
		*/
		$del_payLevel_sql = "
			UPDATE pay_levels
			SET Active = 0
			WHERE JobCode = ?
		";
		if (!$stmt = $conn->prepare($del_payLevel_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}
		if (!$stmt->bind_param("s", $param_str_jobCode)) {
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		$stmt->close();
		$conn->close();

		// Response
		echo "&jc={$param_str_jobCode}";
		if (strlen($_POST['deptID']) > 0) {
			echo "&deptid={$_POST['deptID']}";
		}
	}
?>
