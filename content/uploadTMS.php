<?php
	/***  TEST LOGGED IN  ***/
	if (!isset($loggedIn) || !$loggedIn) {
		exit;
	}
?>

<script src="./scripts/uploadTMS.js"></script>
<link href="./css/uploadTMS.css" rel="stylesheet">

<?php
	// Open DB connection
	require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php');

	// Get upload history
	$sel_upload_history = "
		SELECT uh.ID, uh.UploadDate, uh.FileName, CONCAT(u.firstName, ' ', u.lastName) name
		FROM hrodt.tms_upload_history uh
		JOIN secure_login.users u
		ON uh.UploaderID = u.id
		ORDER BY uh.UploadDate DESC
	";
	$stmt = $conn->prepare($sel_upload_history);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($fileID, $uploadDate, $fileName, $uploaderName);
	$upload_history_result = $stmt;
?>

<div class="container" style="padding-top:20px;">
	<div class="row form-container">
		<h4>Upload New TMS</h4>
		<form
			name="uploadFile-form"
			id="uploadFile-form"
			role="form"
			method="post"
			action=""
			enctype="multipart/form-data">

			<div class="row">
				<div class="col-lg-5">
					<div class="form-group">
						<label for="fileToUpload">Select TMS spreadsheet to upload <span style="font-style:italic;color:red;">(must be a CSV file)</span>:</label><br />
						<div class="input-group">
							<span class="input-group-btn">
								<span class="btn btn-primary btn-file">
									Browse <input type="file" name="fileToUpload" id="fileToUpload">
								</span>
							</span>
							<input type="text" class="form-control" readonly="readonly">
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:20px;">
				<div class="col-lg-2">					
					<input
						type="submit"
						name="upload-submit"
						class="btn btn-primary form-control"
						value="Upload File"
						>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div id="ajax_uploadResponse"></div>
				</div>
			</div>
		</form>
	</div>
	<br />

	<!-- Upload History -->
	<div class="row">
		<div class="col-lg-6 form-container">
			<div style="max-height: 300px; overflow:scroll;">
				<h4>Upload History</h4>
				<table class="table table-striped">
					<tr>
						<th>Filename</th>
						<th>Uploaded By</th>
						<th>Date</th>
					</tr>
					<?php
						while ($upload_history_result->fetch()) {
					?>
					<tr>
						<td><?= $fileName ?></td>
						<td><?= $uploaderName ?></td>
						<td><?= date("n/j/Y g:ia", strtotime($uploadDate)) ?></td>
					</tr>
					<?php
						}
					?>
				</table>
			</div>
		</div>
	</div>
<!-- $fileID, $uploadDate, $fileName, $uploaderName -->
</div>

