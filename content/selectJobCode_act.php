<?php
	/*
		This page is accessed via an AJAX request by the
		input#jobCode event handler in job_spec_details.js.

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

	// Create sql params
	$param_str_JobCode = $conn->escape_string(trim($_POST['jobCode']));

	/*
		Prepeare SQL statement
	*/
	$select_payLevel_sql = "
		SELECT *
		FROM pay_levels
		WHERE JobCode = ?
	";
	if (!$stmt = $conn->prepare($select_payLevel_sql)) {
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
	
	$payLevel_result = $stmt->get_result();
	$payLevel_row = $payLevel_result->fetch_assoc();

	$stmt->close(); // Close statment

	/*
		If no results from payLevel table, echo json_encode(null);
		else, get JobFamilyID and continue.
	*/
	if ($payLevel_result->num_rows > 0) {

		/*
			Get Job Family ID
		*/
		$select_jobFamilyID_sql = "
			SELECT ID AS JobFamilyID
			FROM job_families
			WHERE JobFamily_short = '" . $payLevel_row['JobFamily'] . "'
		";
		$qry_jobFamilyID = $conn->query($select_jobFamilyID_sql);
		$jobFamily_row = $qry_jobFamilyID->fetch_assoc();

		// Add JobFamilyID to associative array
		$payLevel_row['JobFamilyID'] = $jobFamily_row['JobFamilyID'];

		// Convert PayPlan to format used in job_spec_details.php
		switch ($payLevel_row['PayPlan']) {
			case 'USPS':
				$payLevel_row['PayPlan'] = 'usps';
				break;
			case 'A&P':
				$payLevel_row['PayPlan'] = 'ap';
				break;
			case 'Faculty':
				$payLevel_row['PayPlan'] = 'fac';
				break;
			case 'EXC':
				$payLevel_row['PayPlan'] = 'exec';
				break;
		}

		// Convert FLSA Status to format used in job_spec_details.php
		switch ($payLevel_row['FLSA']) {
			case 'N':
			case 'NE':
				$payLevel_row['FLSA'] = 0;
				break;
			case 'X':
			case 'E':
				$payLevel_row['FLSA'] = 1;
				break;
			case '1X N':
			case 'both':
				$payLevel_row['FLSA'] = 2;
				break;
		}

		// Convert payLevel_row associative array to json object
		echo json_encode($payLevel_row);
	}
	// Else, the JobCode was not found in the pay_levels table
	else {
		echo json_encode(null);
	}
	

	// Close db connection
	mysqli_close($conn);
?>
