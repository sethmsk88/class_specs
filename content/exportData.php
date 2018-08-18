<?php
// Require page to be loaded through index
if (!isset($LOGGED_IN)) {
	header("Location: ../index.php");
}

/***  TEST LOGGED IN  ***/
if (!$LOGGED_IN) {
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

	<form action="<?= $GLOBALS['APP_PATH'] ?>/content/act_exportData.php" method="post">
		<div class="row">
			Please select the data fields you would like to export
		</div>
		<div class="row">
			<div class="col-xs-2">
				<div class="checkbox" style="border-bottom:1px solid #AAA;">
					<label><input type="checkbox" id="select-all-data-fields"><b>Select all</b></label>
				</div>
			</div>
		</div>
<?php
	// select all data field names from class_specs table
	$stmt = $conn->prepare("
		show columns from hrodt.class_specs
	");
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($field, $type, $null, $key, $default, $extra);

	$doNotExportFields = ['id', 'active'];

	while ($stmt->fetch()) {

		// Skip over data fields that shouldn't be displayed
		if (array_search(strtolower($field), $doNotExportFields) !== false) {
			continue;
		}
?>
		<div class="checkbox">
			<label><input type="checkbox" name="<?= $field ?>" class="data-field"><?= $field ?></label>
		</div>		
<?php
	}
?>
		<br>
		<div class="row">
			<div class="col-xs-2">
				<input type="submit" class="btn btn-primary" value="Export" style="width:100%">
			</div>
		</div>
	</form>
</div>

<script>
$(document).ready(function() {
	// Change event handler for "Select All" checkbox
	$('#select-all-data-fields').change(function() {
		if ($(this).prop('checked') === true) {
			$('.data-field').prop('checked', true);
		} else {
			$('.data-field').prop('checked', false);
		}
	});

	// Uncheck "Select All" checkbox if one of the data field checkboxes are unchecked
	$('.data-field').change(function() {
		if ($(this).prop('checked') === false) {
			$('#select-all-data-fields').prop('checked', false);
		}
	});
});
</script>