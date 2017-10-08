<?php
	/***  CHECK IF PAGE WAS POSTED TO  ***/
	if (!isset($_SERVER["REQUEST_METHOD"]) ||
		$_SERVER["REQUEST_METHOD"] != "POST") {
		exit;
	}

	/*
		Deactivate or Re-activate Class Spec in class_specs table based
		on status parameter.
		If there is more than one entry for a single class spec, the oldest
		one will be acted upon.
		Required Param: $_POST['jobCode'], $_POST['status']
	*/
	if (isset($_POST['jobCode'], $_POST['status'])) {

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
			If there are duplicates, act upon the
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
			Update the class spec with the lowest ID number.
			The lowest ID is the first row in the previous query result.
		*/
		$param_int_ID = $sel_classSpec_row['ID'];

		// Flip the active flag based on the current status
		if ($_POST['status'] == 0)
			$newStatus = 1;
		else
			$newStatus = 0;

		$update_classSpec_sql = "
			UPDATE class_specs
			SET Active = ?
			WHERE ID = ?
		";
		$stmt = $conn->prepare($update_classSpec_sql);
		$stmt->bind_param("ii", $newStatus, $param_int_ID);
		$stmt->execute();
		$stmt->close();

		/*
			Mark Job Code as inactive in pay_levels table
		*/
		$update_payLevel_sql = "
			UPDATE pay_levels
			SET Active = ?
			WHERE JobCode = ?
		";
		$stmt = $conn->prepare($update_payLevel_sql);
		$stmt->bind_param("is", $newStatus, $param_str_jobCode);
		$stmt->execute();
		$stmt->close();
		$conn->close();

		// Response
		echo "&jc={$param_str_jobCode}&status={$newStatus}";
		if (strlen($_POST['deptID']) > 0) {
			echo "&deptid={$_POST['deptID']}";
		}
	}
?>
