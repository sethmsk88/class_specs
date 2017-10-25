<?php
	/***  CHECK IF PAGE WAS POSTED TO  ***/
	if (!isset($_SERVER["REQUEST_METHOD"]) ||
		$_SERVER["REQUEST_METHOD"] != "POST") {
		exit;
	}

	/*
		This page is intended to be accessed via an AJAX
		request.

		It responds with a JSON object containing queried
		information for a particular Job Code.
	*/

	// Include my database info
    require "../../shared/dbInfo.php";

    require_once '../includes/functions.php';

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	// Create sql params
	$param_str_JobCode = $conn->escape_string(trim($_POST['jobCode']));

	/*
		Prepare SQL statement
	*/
	$select_classSpec_sql = "
		SELECT c.*, jf.JobFamily_short JobFamily
		FROM hrodt.class_specs c
		JOIN hrodt.job_families jf
			ON c.JobFamilyID = jf.ID
		WHERE c.JobCode = ?;
	";
	if (!$stmt = $conn->prepare($select_classSpec_sql)) {
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}

	/*
		Bind parameters
	*/
	if (!$stmt->bind_param("s", $param_str_JobCode)) {
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	/*
		Execute the prepared statement
	*/
	if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	
	$classSpec_result = $stmt->get_result();
	$classSpec_row = $classSpec_result->fetch_assoc();

	$stmt->close(); // Close statment

	/*
		If no results from classSpec table, echo json_encode(null);
		else, get JobFamilyID and continue.
	*/
	if ($classSpec_result->num_rows > 0) {

		/*
			Get Job Family ID
		*/
		$select_jobFamilyID_sql = "
			SELECT ID AS JobFamilyID
			FROM job_families
			WHERE JobFamily_short = '" . $classSpec_row['JobFamily'] . "'
		";
		$qry_jobFamilyID = $conn->query($select_jobFamilyID_sql);
		$jobFamily_row = $qry_jobFamilyID->fetch_assoc();

		// Add JobFamilyID to associative array
		$classSpec_row['JobFamilyID'] = $jobFamily_row['JobFamilyID'];

		// Convert classSpec_row associative array to json object
		echo json_encode($classSpec_row);
	}
	// Else, the JobCode was not found in the pay_levels table
	else {
		echo json_encode(null);
	}
	
	// Close db connection
	mysqli_close($conn);
?>
