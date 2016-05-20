<?php
/***  TEST LOGGED IN  ***/
if (!isset($loggedIn) || !$loggedIn) {
	exit;
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

// select the most recent threshold
$sel_threshold_sql = "
	SELECT threshold, user_id, dateUpdated
	FROM hrodt.flsa_threshold
	ORDER BY dateUpdated DESC
	LIMIT 1
";
if (!$stmt = $conn->prepare($sel_threshold_sql)) {
	echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
} else if (!$stmt->execute()) {
	echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
}
$stmt->bind_result($threshold, $userID, $dateUpdated);
$stmt->fetch();
?>

<div class="container">
	<form
		name="editThreshold-form"
		id="editThreshold-form"
		class="form-horizontal"
		role="form"
		method="post"
		action="./content/act_flsa_threshold.php">

		<div class="row">
			<div class="col-sm-3 col-md-2 form-group">
				<label for="threshold" class="control-label">FLSA Threshold</label>
				<input
					name="threshold"
					id="threshold"
					type="text"
					class="form-control"
					value="$<?= number_format($threshold, 2, '.', ',') ?>">
			</div>
		</div>

		<div class="row">
			<div class="col-sm-3 col-md-2 form-group">
				<input
					type="submit"
					class="form-control btn btn-primary"
					value="Change Threshold">
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 form-group note">
				Last changed by <?= $_SESSION['firstName'] ?> <?= $_SESSION['lastName'] ?> on <?= date('n/j/Y g:ia', strtotime($dateUpdated)) ?>
			</div>
		</div>
	</form>
</div>
