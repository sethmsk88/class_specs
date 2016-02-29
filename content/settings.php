<?php
/***  TEST LOGGED IN  ***/
if (!isset($loggedIn) || !$loggedIn) {
	exit;
}
?>

<script src="./scripts/settings.js"></script> 

<style type="text/css" rel="stylesheet">
	.row{
		padding-bottom: 8px;
	}
</style>



<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h2>Settings</h2>
		</div>
	</div>
	
	<div class="form-container">
		<div class="row">
			<div class="col-xs-12">
				<h4>Change Password</h4>
				<hr />
			</div>
		</div>

		<form
			name="changePassword-form"
			id="changePassword-form"
			role="form"
			method="post"
			action="">

			<div class="row">
				<div class="col-lg-6">
					<span class="bg-danger text-danger">* Password must contain one number, one lowercase and one uppercase letter</span>
				</div>
			</div>

			<div class="row">
				<div class="form-group">
					<label for="oldPassword" class="control-label col-lg-2">Old Password</label>
					<div class="col-lg-4">
						<input
							name="oldPassword"
							id="oldPassword"
							type="password"
							class="form-control">
					</div>
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

			<div class="row">
				<div class="col-lg-2">
					<input
						type="submit"
						value="Submit"
						class="btn btn-primary form-control">
				</div>
				<div id="ajaxResponse_changePassword" class="col-lg-4">
					<!-- Intentionally left blank -->
				</div>
			</div>
		</form>
	</div>

</div>
