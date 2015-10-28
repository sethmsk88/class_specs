<?php
	// List of validation errors
	$errors = "";

	// Server-side validation in case client-side JS validation fails
	function validateFormFields() {
		global $errors;
		$passedValidation = true;

		return $passedValidation;
	}

	/** If this page was POSTed to **/
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (validateFormFields()) {
			include "../../shared/dbInfo.php";

			// Connect to DB
			$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
			if (mysqli_connect_error()) {
				echo mysqli_connect_error();
				exit();
			}

			$param_Descr = $conn->escape_string(trim($_POST['newCompetency']));
			
			$insert_competency_sql = "
				INSERT INTO competencies (Descr)
				VALUES (?)
			";

			if (!$stmt = $conn->prepare($insert_competency_sql)) {
				echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
				exit;
			}

			if (!$stmt->bind_param("s", $param_Descr)) {
				echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
				exit;
			}

			if (!$stmt->execute()) {
				echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
			}
			else {
				echo '<script>console.log("ID: ' . $conn->insert_id . '\nDescr: ' . $param_Descr . '");</script>';

				echo '<script>updateCompetencies(' . $conn->insert_id . ',"' . $param_Descr . '");</script>';
			}

			$stmt->close();

			// Close DB connection
			mysqli_close($conn);
		}
	}
?>