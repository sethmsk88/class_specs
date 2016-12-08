<?php
	/***  CHECK IF PAGE WAS POSTED TO  ***/
	if (!isset($_SERVER["REQUEST_METHOD"]) ||
		$_SERVER["REQUEST_METHOD"] != "POST") {
		exit;
	}

	// Include my database info
    require "../../shared/dbInfo.php";

    require_once "../includes/functions.php";

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	//	This function prepares text to be displayed in a textarea
	function prepare_text_textarea($text)
	{
		global $conn;
		return $conn->escape_string(str_replace("\r\n",'&#13;&#10;', trim($text)));
	}

	/***
		Make updates in class_specs table
	***/
	/***
		Prepare sql stmt params
	***/
	$param_str_JobCode = prepare_text_textarea($_POST['jobCode']);
	$param_str_JobTitle = prepare_text_textarea($_POST['jobTitle']);
	$param_str_PayPlan = prepare_text_textarea($_POST['payPlan']);
	$param_str_CUPA_HR = prepare_text_textarea($_POST['cupaHR']);
	$param_str_PositionDescr = prepare_text_textarea($_POST['posDescr']);
	$param_str_EducationExp = prepare_text_textarea($_POST['eduExp']);

	// Check for max length errors
	$errors = "";
	if (strlen($param_str_JobCode) > 8)
		$errors .= "Classification Code is too long<br>";
	if (strlen($param_str_JobTitle) > 128)
		$errors .= "Classification Title is too long<br>";
	if (strlen($param_str_CUPA_HR) > 32)
		$errors .= "CUPA-HR # is too long<br>";
	if (strlen($param_str_PositionDescr) > 8000)
		$errors .= "Description is too long<br>";
	if (strlen($param_str_EducationExp) > 4000)
		$errors .= "Education/Experience is too long<br>";

	// Display max length errors and exit
	if (strlen($errors) > 0) {
		echo '<span class="notice">Update Failed!<br>' . $errors . '</span>';
		exit;
	}

	/***
		If blank, insert NULL for these fields
	***/
	if ($_POST['classSpecID'] != '') {
		$param_int_ID = $_POST['classSpecID'];
	}
	else {
		$param_int_ID = NULL;
	}
	if ($_POST['jobFamily'] != '') {
		$param_int_JobFamilyID = $_POST['jobFamily'];
	}
	else {
		$param_int_JobFamilyID = NULL;
	}
	if (trim($_POST['oldPaygrade']) != '') {
		$param_int_OldPayGrade = trim($_POST['oldPaygrade']);
	}
	else {
		$param_int_OldPayGrade = NULL;
	}
	if ($_POST['eeoCode'] != '') {
		$param_int_EEO_Code_ID = $_POST['eeoCode'];
	}
	else {
		$param_int_EEO_Code_ID = NULL;
	}
	if ($_POST['cbuCode'] != '') {
		$param_int_CBU_Code_ID = $_POST['cbuCode'];
	}
	else {
		$param_int_CBU_Code_ID = NULL;
	}
	if (trim($_POST['ipedsCode']) != '') {
		$param_int_IPEDS_Code = trim($_POST['ipedsCode']);
	}
	else {
		$param_int_IPEDS_Code = NULL;
	}
	if ($_POST['flsa'] != '') {
		$param_int_FLSA = $_POST['flsa'];
	}
	else {
		$param_int_FLSA = NULL;
	}
	if ($_POST['backgroundCheck'] != '') {
		$param_int_BackgroundCheck = $_POST['backgroundCheck'];
	}
	else {
		$param_int_BackgroundCheck = NULL;
	}
	if ($_POST['physical'] != '') {
		$param_int_Physical = $_POST['physical'];
	}
	else {
		$param_int_Physical = NULL;
	}
	if ($_POST['childCareSecurityCheck'] != '') {
		$param_int_ChildCareSecurityCheck = $_POST['childCareSecurityCheck'];
	}
	else {
		$param_int_ChildCareSecurityCheck = NULL;
	}
	if ($_POST['financialDisclosure'] != '') {
		$param_int_FinancialDisclosure = $_POST['financialDisclosure'];
	}
	else {
		$param_int_FinancialDisclosure = NULL;
	}
	if ($_POST['confidentialityStmt'] != '') {
		$param_int_ConfidentialityStmt = $_POST['confidentialityStmt'];
	}
	else {
		$param_int_ConfidentialityStmt = NULL;
	}


	/*
		Check to see if Job Code already exists in class_specs table
		with a different ID. This would mean that the User is attempting
		to change the Class Spec they are editing into a different Job
		Code that is already in the table. This is NOT allowed.
	*/
	$select_classSpec_sql = "
		SELECT *
		FROM class_specs
		WHERE JobCode = ? AND
			Active = 1
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

	$duplicateJobCode = false; // Flag

	// If there is a duplicate Job Code in the class_specs table
	if ($classSpec_result->num_rows > 0) {

		if ($param_int_ID != $classSpec_row['ID']) {
			$duplicateJobCode = true;
		}
	}


	/*
		If there aren't any duplicate Job Codes, then execution
		continues as normal.
	*/
	if (!$duplicateJobCode) {
		$update_classSpec_sql = "
			UPDATE class_specs
			SET JobCode = ?,
				JobTitle = ?,
				PayPlan = ?,
				JobFamilyID = ?,
				OldPayGrade = ?,
				EEO_Code_ID = ?,
				CUPA_HR = ?,
				FLSA = ?,
				CBU_Code_ID = ?,
				PositionDescr = ?,
				EducationExp = ?,
				BackgroundCheck = ?,
				Physical = ?,
				ChildCareSecurityCheck = ?,
				FinancialDisclosure = ?,
				ConfidentialityStmt = ?
			WHERE ID = ?
		";

		// Prepare SQL statement
		if (!$stmt = $conn->prepare($update_classSpec_sql)){
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}

		// Bind parameters
		if (!$stmt->bind_param("sssiiisiissiiiiii",
			$param_str_JobCode,
			$param_str_JobTitle,
			$param_str_PayPlan,
			$param_int_JobFamilyID,
			$param_int_OldPayGrade,
			$param_int_EEO_Code_ID,
			$param_str_CUPA_HR,
			$param_int_FLSA,
			$param_int_CBU_Code_ID,
			$param_str_PositionDescr,
			$param_str_EducationExp,
			$param_int_BackgroundCheck,
			$param_int_Physical,
			$param_int_ChildCareSecurityCheck,
			$param_int_FinancialDisclosure,
			$param_int_ConfidentialityStmt,
			$param_int_ID)){
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}

		// Execute the prepared statement
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		else{
			echo 'Class Spec has been updated!';
		}
		
		$stmt->close();

		/*
			Update fields that also appear in pay_levels table
		*/
		$param_str_PayPlan = convertPayPlan($param_str_PayPlan, 'pay_levels');
		$param_str_JobFamily = $conn->query("
			SELECT *
			FROM job_families
			WHERE ID = $param_int_JobFamilyID")->fetch_assoc()['JobFamily_short'];
		$param_str_OldPayGrade = $param_int_OldPayGrade;
		$param_str_FLSA = convertFLSA($param_int_FLSA, 'symbolic');

		$update_payLevel_sql = "
			UPDATE pay_levels
			SET JobTitle = ?,
				PayPlan = ?,
				JobFamily = ?,
				OldPayGrade = ?,
				FLSA = ?,
				IPEDS_SOCs = ?
			WHERE JobCode = ?
		";

		if (!$stmt = $conn->prepare($update_payLevel_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}
		if (!$stmt->bind_param("sssssis",
				$param_str_JobTitle,
				$param_str_PayPlan,
				$param_str_JobFamily,
				$param_str_OldPayGrade,
				$param_str_FLSA,
				$param_int_IPEDS_Code,
				$param_str_JobCode)) {
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}

		$stmt->close();

		/*
			Insert classSpecID and competencyID pair into table
			Loop through all competencies in form.
			Add the ID pair to the class_specs_rec_competencies table.
		*/
		$insert_classSpecComp_sql = "
			INSERT INTO class_specs_rec_competencies (ClassSpec_ID, Competency_ID)
			VALUES (?,?)
		";

		// Prepare statement
		if (!$stmt = $conn->prepare($insert_classSpecComp_sql)){
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}

		// Bind parameters
		if (!$stmt->bind_param("ii", $param_int_ID, $param_int_competencyID)){
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}

		// Loop through POST array to get competencies
		foreach ($_POST as $key => $val){
			if (strpos($key, 'competency_') !== false){
				if (is_numeric($val)){
					$param_int_competencyID = $val;

					// Execute prepared statement
					if (!$stmt->execute()){
						echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
					}
				}
			}
		}

		$stmt->close();
	} // End Not Duplicate Job Code
	else {
		echo '<span class="notice">Error: Classification Code (' . $param_str_JobCode . ') already exists in table!</span>';
	}

	// Close DB connection
	mysqli_close($conn);
?>
