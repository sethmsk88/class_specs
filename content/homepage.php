<?php
	// Require page to be loaded through index
	if (!isset($GLOBALS['LOGGED_IN'])) {
		header("Location: ../index.php");
	}
?>
<link href="./css/homepage.css" rel="stylesheet">

<?php
	/****************************************************/
	/* Function: outputJobCodeTable                     */
	/* Description: Output a table containing all Jobs  */
	/*    in qry_result. qry_result is the result of a  */
	/*    query on the pay_levels table.                */
	/****************************************************/
	function outputJobCodeTable($qry_result, $tableId, $payPlan) {
		echo '<table id="' . $tableId . '" class="table table-striped highlighted-rows">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Classification Code</th>';
					echo '<th>Classification Title</th>';
					echo '<th>Classification Family</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

				$jobFamily_i = 0; // Iterator for jobFamily array

				// For each row in query result
				while ($row = $qry_result->fetch_assoc()){
					/*
						If JobCode has an entry in the class_specs table,
						link to details page, otherwise, link to add page
					*/
					if ($row['classID'] == null) {
						$jobSpecURL = '?page=jobSpec_add&jc=' .
							$row['JobCode'] .
							'&pp=' . $payPlan;
					}
					else {
						$jobSpecURL = "?page=jobSpec_details&jc={$row['JobCode']}&pp={$payPlan}";

						// if dept id is NOT null, add it to the URL as a GET var
						if (!is_null($row['DeptID'])) {
							$jobSpecURL .= "&deptid={$row['DeptID']}";
						}
					}
					?>

					<?php
						// Give deactivated class specs a red background
						if ($row['Active'] == 0)
							$deactivatedClass = "bg-danger";
						else
							$deactivatedClass = "";
					?>

					<!--
						Using an inline onclick attribute instead of an event handler,
						because the event handlers were not working properly with
						the use of datatables.
					-->
					<tr
						class='clickable'
						onclick='window.location.assign("<?php echo $jobSpecURL; ?>");'
						>
						<td class="<?= $deactivatedClass ?>"><?= $row['JobCode'] . $row['letter'] ?></td>
						<td class="<?= $deactivatedClass ?>"><?= $row['JobTitle'] ?></td>
						<td class="<?= $deactivatedClass ?>"><?= $row['JobFamily_long'] ?></td>
					</tr>
					<?php
				}
			echo '</tbody>';
		echo '</table>';
	}


	/******************************************************/
	/* Function: getClassSpecs                            */
	/* Description: Run a query to get to get all class   */
	/*    specs in a particular pay plan.                 */
	/* Params:                                            */
	/*	  payPlan is a string representing the Pay        */
	/*    Plan for which you would like to run the query. */
	/*	  conn is a reference to a DB connection.         */
	/* Return: Query object containing query result       */
	/************************************************&*****/
	function getClassSpecs($payPlan, &$conn) {

		$sel_classSpecs_sql = "
			SELECT c.JobCode,
				c.JobTitle,
				c.ID classID,
				j.JobFamily_long,
				c.JobFamilyID,
				c.Active,
				c.DeptID,
				d.letter
			FROM class_specs c
			LEFT JOIN job_families j
				ON c.JobFamilyID = j.ID
			LEFT JOIN hrodt.departments d
				ON d.id = c.DeptID
			WHERE c.PayPlan = '$payPlan'
		";
		// If not logged in, only show active class specs
		if (!$GLOBALS['LOGGED_IN'] OR is_null($GLOBALS['ACCESS_LEVEL'])) {
			$sel_classSpecs_sql .= " AND c.Active = 1";
		}
		$sel_classSpecs_sql .= " ORDER BY c.JobCode ASC";

		if (!($sel_classSpecs_result = $conn->query($sel_classSpecs_sql))){
			echo "Query failed: (" . $conn->errno . ") " . $conn->error;
			echo "<br />" . $sql;
		}

		return $sel_classSpecs_result;
	}


	// Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	$sel_classSpec_ap_result = getClassSpecs('ap', $conn);
	$sel_classSpec_usps_result = getClassSpecs('usps', $conn);
	$sel_classSpec_exec_result = getClassSpecs('exec', $conn);
	$sel_classSpec_fac_result = getClassSpecs('fac', $conn);

	/*
		If JobCode get variable exists, get the associated job title
	*/
	if (isset($_GET['jc'])) {
		$param_str_jobCode = $_GET['jc'];
		$sel_jobTitle_sql = "
			SELECT JobTitle
			FROM class_specs
			WHERE JobCode = ?
		";
		if (!$stmt = $conn->prepare($sel_jobTitle_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		}
		if (!$stmt->bind_param("s", $param_str_jobCode)) {
			echo 'Binding parameters failed: (' . $stmt->error . ') ' . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		$sel_jobTitle_result = $stmt->get_result();
		$stmt->close();
		$sel_jobTitle_row = $sel_jobTitle_result->fetch_assoc();
	}

?>	

<div class="container">
	<!-- Password changed Message -->
	<!-- Message displayed when password has been changed -->
	<?php
		// If a password reset email was successful
		if (isset($_GET['pwreset']) && $_GET['pwreset'] === 'true') {
	?>
	<div class="row" style="margin-bottom:16px">
		<div class="col-xs-12">
			<span class="bg-success text-success">
				Your password has been changed. Please login with using your new password.
			</span>
		</div>
	</div>
	<?php
		}
	?>
	<!-- End Password Changed Message -->

	<!-- Password Reset Message -->
	<!-- Message displayed when a password reset was requested -->
	<?php
		// If a password reset email was successfully sent
		if (isset($_GET['emailsent']) && $_GET['emailsent'] === 'true') {
	?>
	<div class="row" style="margin-bottom:16px">
		<div class="col-xs-12">
			<span class="bg-success text-success">
				You have been sent an email with a Reset Password link. Please click the link in the email to change your password.<br><b>Note: <i>If you do not receive an email in the next few minutes, please check your spam or junk email folder.</i></b>
			</span>
		</div>
	</div>
	<?php
		// Else, if password reset email did not send successfully
		} elseif (isset($_GET['emailsent']) && $_GET['emailsent'] !== 'true') {
	?>
	<div class="row" style="margin-bottom:16px">
		<div class="col-xs-12">
			<span class="bg-danger text-danger">
				<b>Error!</b> Password could not be reset. Please contact your administrator.
			</span>
		</div>
	</div>
	<?php
		}
	?>
	<!-- End Password Reset Message -->

	<div class"row">
		<div class="col-xs-9">
			<br />
			
			<ul id="myTabs" class="nav nav-tabs">
				<li class="active tab_title"><a data-toggle="tab" href="#ap">A&amp;P</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#usps">USPS</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#exec">Exec</a></li>
				<?php if ($GLOBALS['LOGGED_IN'] AND !is_null($GLOBALS['ACCESS_LEVEL'])) { ?>
				<li class="tab_title"><a data-toggle="tab" href="#fac">Faculty</a></li>
				<?php } ?>
			</ul>

			<div class="tab-content myTabs">

				<!--~~~~~~~~~-->
				<!-- A&P Tab -->
				<!--~~~~~~~~~-->
				<div id="ap" class="tab-pane fade in active">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($sel_classSpec_ap_result, 'classSpecs_ap', 'ap'); ?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~-->
				<!-- USPS Tab -->
				<!--~~~~~~~~~~-->
				<div id="usps" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($sel_classSpec_usps_result, 'classSpecs_usps', 'usps'); ?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~-->
				<!-- Exec Tab -->
				<!--~~~~~~~~~~-->
				<div id="exec" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($sel_classSpec_exec_result, 'classSpecs_exec', 'exec'); ?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~~~~-->
				<!-- Faculty Tab -->
				<!--~~~~~~~~~~~~~-->
				<?php if ($GLOBALS['LOGGED_IN'] AND !is_null($GLOBALS['ACCESS_LEVEL'])) { ?>
				<div id="fac" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($sel_classSpec_fac_result, 'classSpecs_fac', 'fac'); ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<!-- End of page padding -->
<div style="clear:both;padding:100px 0;"></div>

<?php mysqli_close($conn); ?>
