<?php
	/*
		Update competency
	*/

	/*
		If competencyID was posted to this page
	*/
	if (isset($_POST['_compID'])) {
		// Include my database info
	    include "../../shared/dbInfo.php";

		// Connect to DB
		$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
		if (mysqli_connect_error()){
			echo mysqli_connect_error();
			exit();
		}

		$param_int_ID = $_POST['_compID'];
		$param_str_Descr = $conn->escape_string(trim($_POST['updatedComp']));

		$update_comp_sql = "
			UPDATE competencies
			SET Descr = ?
			WHERE ID = ?
		";

		if (!$stmt = $conn->prepare($update_comp_sql)){
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}
		if (!$stmt->bind_param("si", $param_str_Descr, $param_int_ID)){
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		if (!$stmt->execute()){
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		else {
			echo $_POST['updatedComp'];
		}
	}
?>
