<?php
	/*
		Delete row from class_specs_rec_competencies table, where
		ClassSpec_ID = (param_classSpecID) AND
		Competency_ID = (param_competencyID)
	*/

	// Include my database info
    include "../../shared/dbInfo.php";

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	$param_int_ClassSpec_ID = $_POST['classSpecID'];
	$param_int_Competency_ID = $_POST['competencyID'];

	$delete_row_sql = "
		DELETE
		FROM class_specs_rec_competencies
		WHERE ClassSpec_ID = ? AND
			Competency_ID = ?
	";

	if (!$stmt = $conn->prepare($delete_row_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param("ii", $param_int_ClassSpec_ID, $param_int_Competency_ID)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else{
		echo '1'; // Success!
	}
?>