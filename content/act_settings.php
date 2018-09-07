<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/class_specs/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/class_specs/includes/globals.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
require_once '../includes/functions.php';

/* Check to see if User is logged in */
sec_session_start();
$accessArray = login_check($conn);


if ($LOGGED_IN) {

	if (isset($_POST['newPwHashed'], $_POST['oldPwHashed'])) {

		$param_str_oldPassword = $_POST['oldPwHashed'];
		$param_str_newPassword = $_POST['newPwHashed'];
		$user_id = $_SESSION['user_id'];

		$sel_user_pw_sql = "
			SELECT Password
			FROM user_management.users
			WHERE UserId = ?
		";

		if (!$stmt = $conn->prepare($sel_user_pw_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		} else if (!$stmt->bind_param('i', $user_id)) {
			echo 'Binding params failed: (' . $stmt->errno . ') ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		} else {
			$stmt->store_result();
			$stmt->bind_result($db_password);
			$stmt->fetch();

			/*
				If old password matches the password stored in database,
				and the new password matches the confirmed new password,
				update the user password in the database.
			*/
			if ($db_password == $param_str_oldPassword) {

				$update_user_pw_sql = "
					UPDATE user_management.users
					SET	Password = ?
					WHERE UserId = ?
				";

				if ($stmt = $conn->prepare($update_user_pw_sql)) {
					$stmt->bind_param('si', $param_str_newPassword, $user_id);
					$stmt->execute();
					$stmt->store_result();

					if ($stmt->affected_rows == 1) {
						echo '<div class="text-success">Password has been changed!</div>';
					}
				}
			}
			else {
				echo '<div class="text-danger">Old Password is incorrect!</div>';
			}
		}
	}
	else {
		// Display error message
		echo '<div class="text-danger">Password change unsuccessful!</div>';
	}
}
else {
	// Display error message
	echo '<div class="text-danger">Must be logged in to change password!</div>';
}

?>
