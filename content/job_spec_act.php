<?php
	// Include my database info
    include "../../shared/dbInfo.php";

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	/***
		Prepare sql stmt params
	***/
	$param_str_JobCode = $conn->escape_string(trim($_POST['jobCode']));
	$param_str_JobTitle = $conn->escape_string(trim($_POST['jobTitle']));
	$param_str_PayPlan = $conn->escape_string(trim($_POST['payPlan']));
	$param_str_CUPA_HR = $conn->escape_string(trim($_POST['cupaHR']));
	$param_str_PositionDescr = $conn->escape_string(trim($_POST['posDescr']));
	$param_str_EducationExp = $conn->escape_string(trim($_POST['eduExp']));

	/***
		If blank, insert NULL for these fields
	***/
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
	/*if (trim($_POST['ipedsCode']) != '') {
		$param_int_IPEDS_Code = trim($_POST['ipedsCode']);
	}
	else {
		$param_int_IPEDS_Code = NULL;
	}*/
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

	$insert_classSpec_sql = "
		INSERT INTO class_specs (
			JobCode,
			JobTitle,
			PayPlan,
			JobFamilyID,
			OldPayGrade,
			EEO_Code_ID,
			CUPA_HR,
			FLSA,
			CBU_Code_ID,
			PositionDescr,
			EducationExp,
			BackgroundCheck,
			Physical,
			ChildCareSecurityCheck,
			FinancialDisclosure,
			ConfidentialityStmt
		)
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
	";

	// Prepare SQL statement
	if (!$stmt = $conn->prepare($insert_classSpec_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}

	// Bind parameters
	if (!$stmt->bind_param("sssiiisiissiiiii",
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
		$param_int_ConfidentialityStmt
		)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	// Execute the prepared statement
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else{
		echo 'Class Spec has been added (Job Code: ' . $param_str_JobCode . ')';
	}
	
	$stmt->close();

	// Get insert ID from previous insert query
	$classSpecID = $conn->insert_id;

	// Insert classSpecID and competencyID into table
	$insert_classSpecComp_sql = "
		INSERT INTO class_specs_rec_competencies (ClassSpec_ID, Competency_ID)
		VALUES (?,?)
	";

	// Prepare statement
	if (!$stmt = $conn->prepare($insert_classSpecComp_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}

	// Bind parameters
	if (!$stmt->bind_param("ii", $classSpecID, $competencyID)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	// Loop through POST array to get competencies
	foreach ($_POST as $key => $val){
		if (strpos($key, 'competency_') !== false){
			if (is_numeric($val)){
				$competencyID = $val;

				// Execute prepared statement
				if (!$stmt->execute()){
					echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
				}
			}
		}
	}

	$stmt->close();

	// Close DB connection
	mysqli_close($conn);
?>