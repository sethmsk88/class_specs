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
	include_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
?>

<div class="container" style="padding-top:20px;">
	<form
		name="uploadFile-form"
		id="uploadFile-form"
		role="form"
		method="post"
		action=""
		enctype="multipart/form-data"
		>
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
