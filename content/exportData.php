<style type="text/css">
table#data-fields th{
	font-weight:bold;
}
</style>

<?php

// Require page to be loaded through index
if (!isset($loggedIn)) {
	header("Location: ../index.php");
}

/***  TEST LOGGED IN  ***/
if (!$loggedIn) {
	echo '<span class="text-danger">Must be logged in to access this page</span>';
	exit;
}

// Create database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
?>

<div class="container">
	<div class="row">
		<h3>Export Data to Excel Spreadsheet</h3>
	</div>
	<div class="row">
		Please select the data fields you would like to export
	</div>

	<table id="data-fields" class="table">
		<tr>
			<th>
				<input type="checkbox" id="select-all-data-fields">
			</th>
			<th>
				<label for="select-all-data-fields">Select all</label>
			</th>
		</tr>
<?php
	// select all data field names from class_specs table
	$stmt = $conn->prepare("
		show columns from hrodt.class_specs
	");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($field, $type, $null, $key, $default, $extra);

	while ($stmt->fetch()) {
?>
	<tr>
		<td>
			<div class="checkbox">
				<label><input type="checkbox" name="<?= $field ?>" class="data-field"><?= $field ?></label>
			</div>
		</td>
	</tr>
<?php
	}
?>
	</table>
	
</div>

<script>
	$(document).ready(function() {
		console.log('hello');
	});
</script>