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
		Make updates in class_specs table
	***/
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
		$param_int_ID
		)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	/*
	$param_str_JobCode
	$param_str_JobTitle
	$param_int_FLSA // needs to be converted to varchar representation for pay_levels table
	$param_int_OldPayGrade // this is a varchar(8) in pay_levels table
	$param_int_JobFamilyID // need to use the ID to get JobFamily_short from job_families table and insert that value
	$param_str_PayPlan
	*/





	// Execute the prepared statement
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else{
		echo 'Class Spec has been updated!';
	}
	
	$stmt->close();


	/*
		Loop through all competencies in form.
		Add the ID pair to the class_specs_rec_competencies table.
	*/
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








	// Close DB connection
	mysqli_close($conn);
?>