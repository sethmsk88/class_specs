<script src="./scripts/changePw.js?v=1"></script>

<?php

// Check to see if password reset link is valid
try {
	$hashedTempPw = hash("sha512", $_GET['tempPw']);

	// Temp password can be no more than 1 day old
	if (!$stmt = $conn->prepare("
		select firstName, lastName
		from secure_login.users
		where id = ?
			and tempPassword = ?
			and tempPasswordCreated >= now() - interval 1 day
	")) {
		throw new Exception("Error preparing query: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->bind_param("is", $_GET['uid'], $hashedTempPw)) {
		throw new Exception("Error binding params: ({$conn->errno}) {$conn->error}<br>");
	}
	if (!$stmt->execute()) {
		throw new Exception("Error executing query: ({$conn->errno}) {$conn->error}<br>");
	}
	$stmt->store_result();
	$stmt->bind_result($firstName, $lastName);
	$stmt->fetch();

	// Check to see if user exists
	if ($stmt->num_rows === 0) {
		throw new Exception("Password reset link is invalid!");
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}


?>

<div class="container">	
	<div class="form-container">
		<div class="row">
			<div class="col-xs-12">
				<h4>Reset Password</h4>
				<hr />
			</div>
		</div>

		<form
			name="changePassword-form"
			id="changePassword-form"
			role="form"
			method="post"
			action="<?= $GLOBALS['APP_PATH_URL'] ?>content/act_changePassword.php">

			<div class="row" style="margin-bottom:12px">
				<div class="col-lg-6">
					Set a new password for user: <b><?= $firstName ?> <?= $lastName ?></b>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="newPassword" class="control-label col-lg-2">New Password</label>
					<div class="col-lg-4">
						<input
							name="newPassword"
							id="newPassword"
							type="password"
							class="form-control">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="confirmPassword" class="control-label col-lg-2">Confirm Password</label>
					<div class="col-lg-4">
						<input
							name="confirmPassword"
							id="confirmPassword"
							type="password"
							class="form-control">
					</div>
				</div>
			</div>

			<input type="hidden" name="tempPw" value="<?= $_GET['tempPw'] ?>">
			<input type="hidden" name="uid" value="<?= $_GET['uid'] ?>">

			<div class="row">
				<div class="col-lg-2">
					<input
						type="submit"
						value="Submit"
						class="btn btn-primary form-control">
				</div>
			</div>
		</form>
	</div>
</div>
