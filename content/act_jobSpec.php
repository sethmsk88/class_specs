<?php
	require_once '../includes/functions.php';

	// Make sure required fields have been posted
	$errMsg = "";
	if ($_POST['jobCode'] == '')
		$errMsg .= "Classification Code is required<br>";
	if ($_POST['jobTitle'] == '')
		$errMsg .= "Classification Title is required<br>";
	if ($_POST['jobFamily'] == '')
		$errMsg .= "Classification Family is required<br>";
	if ($_POST['payPlan'] == '')
		$errMsg .= "Pay Plan is required<br>";

	// If Required Fields Were Not All Posted
	if (strlen($errMsg) >0) {
		// display error message and exit
		echo '<span class="text-danger">' . $errMsg . '</span>';
		exit;
	}

	// if a department is being assigned to a class spec
	if (isset($_POST['assignDept']) && $_POST['assignDept'] === 'on') {
		$param_int_DeptID = (isset($_POST['deptId']) && $_POST['deptId'] !== '') ? $_POST['deptId'] : NULL;
	} else {
		$param_int_DeptID = NULL;
	}
	
	// Include my database info
    include "../../shared/dbInfo.php";

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	/*
		Check to see if Job Code already exists in class_specs table
	*/
	$param_str_JobCode = $conn->escape_string(trim($_POST['jobCode']));
	$select_classSpec_sql = "
		SELECT c.*
		FROM hrodt.class_specs c
		JOIN hrodt.departments d
			ON d.id = c.DeptID
		WHERE c.JobCode = ?
			AND c.DeptID = ?
			AND c.Active = 1
	";
	if (!$stmt = $conn->prepare($select_classSpec_sql)) {
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param("si", $param_str_JobCode, $param_int_DeptID)) {
		echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()) {
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	$classSpec_result = $stmt->get_result();
	$stmt->close();

	/*
		If there is a duplicate job code in class_specs table
	*/
	if ($classSpec_result->num_rows > 0) {
		// Alert user
		echo '<span class="notice">Error: Classification Code (' . $param_str_JobCode . ') already exists in table!</span>';
	}
	else {
		/***
			Prepare sql stmt params
		***/
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
		} else {
			$param_int_JobFamilyID = NULL;
		}
		if (trim($_POST['oldPaygrade']) != '') {
			$param_int_OldPayGrade = trim($_POST['oldPaygrade']);
		} else {
			$param_int_OldPayGrade = NULL;
		} if ($_POST['eeoCode'] != '') {
			$param_int_EEO_Code_ID = $_POST['eeoCode'];
		} else {
			$param_int_EEO_Code_ID = NULL;
		} if ($_POST['cbuCode'] != '') {
			$param_int_CBU_Code_ID = $_POST['cbuCode'];
		} else {
			$param_int_CBU_Code_ID = NULL;
		} if ($_POST['flsa'] != '') {
			$param_int_FLSA = $_POST['flsa'];
		} else {
			$param_int_FLSA = NULL;
		}
		if ($_POST['backgroundCheck'] != '') {
			$param_int_BackgroundCheck = $_POST['backgroundCheck'];
		} else {
			$param_int_BackgroundCheck = NULL;
		} if ($_POST['physical'] != '') {
			$param_int_Physical = $_POST['physical'];
		} else {
			$param_int_Physical = NULL;
		} if ($_POST['childCareSecurityCheck'] != '') {
			$param_int_ChildCareSecurityCheck = $_POST['childCareSecurityCheck'];
		} else {
			$param_int_ChildCareSecurityCheck = NULL;
		} if ($_POST['financialDisclosure'] != '') {
			$param_int_FinancialDisclosure = $_POST['financialDisclosure'];
		} else {
			$param_int_FinancialDisclosure = NULL;
		}
		if ($_POST['confidentialityStmt'] != '') {
			$param_int_ConfidentialityStmt = $_POST['confidentialityStmt'];
		} else {
			$param_int_ConfidentialityStmt = NULL;
		}
		if ($_POST['ipedsCode'] != '') {
			$param_int_IPEDS_SOCs = $_POST['ipedsCode'];
		} else {
			$param_int_IPEDS_SOCs = NULL;
		}

		// Update departments table if necessary
		$param_str_letter = isset($_POST['deptLetter']) ? $_POST['deptLetter'] : NULL;
		if (isset($_POST['assignDept']) && $_POST['assignDept'] === 'on') {
			$stmt = $conn->prepare("
				UPDATE hrodt.departments
				SET letter = ?
				WHERE id = ?
			");
			$stmt->bind_param("si", $param_str_letter, $param_int_DeptID);
			$stmt->execute();
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
				ConfidentialityStmt,
				DeptId
			)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
		";

		// Prepare SQL statement
		if (!$stmt = $conn->prepare($insert_classSpec_sql)){
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
			$param_int_DeptID
			)){
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}

		// Execute the prepared statement
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		else{
			echo 'Class Spec has been added (Classification Code: ' . $param_str_JobCode . $param_str_letter . ')';
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

		// Check to see if Job Code exists in Pay Levels table
		$sel_payLevel_sql = "
			SELECT *
			FROM hrodt.pay_levels
			WHERE JobCode = ?
		";

		if ($stmt = $conn->prepare($sel_payLevel_sql)) {
			$stmt->bind_param("s", $param_str_JobCode);
			$stmt->execute();
			$stmt->store_result();

			// If Job Code doesn't exist in pay_levels table
			if ($stmt->num_rows == 0) {

				// Get short form of Job Family
				$sel_jobFamily_sql = "
					SELECT JobFamily_short
					FROM hrodt.job_families
					WHERE ID = ?
				";
				$stmt = $conn->prepare($sel_jobFamily_sql);
				$stmt->bind_param("i", $param_int_JobFamilyID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($param_str_JobFamily);
				$stmt->fetch();

				$param_str_OldPayGrade = $param_int_OldPayGrade;

				// convert pay plan to format used in pay_levels table
				$param_str_PayPlan = convertPayPlan($param_str_PayPlan, "pay_levels");

				// convert FLSA to format used in pay_levels table
				$param_str_FLSA = convertFLSA($param_int_FLSA, "symbolic");

				$param_int_IPEDS_SOCs = $_POST['ipedsCode'];

				$ins_payLevel_sql = "
					INSERT INTO hrodt.pay_levels (
						JobCode,
						JobTitle,
						FLSA,
						JobFamily,
						OldPayGrade,
						PayPlan,
						IPEDS_SOCs)
					VALUES (?,?,?,?,?,?,?)
				";

				// Insert into pay_levels table
				if ($stmt = $conn->prepare($ins_payLevel_sql)) {
					$stmt->bind_param("ssssssi",
						$param_str_JobCode,
						$param_str_JobTitle,
						$param_str_FLSA,
						$param_str_JobFamily,
						$param_int_OldPayGrade,
						$param_str_PayPlan,
						$param_int_IPEDS_SOCs);
					$stmt->execute();
				}
			}
		}
	}
?>
