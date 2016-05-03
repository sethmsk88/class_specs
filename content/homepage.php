<?php
	// Require page to be loaded through index
	if (!isset($loggedIn)) {
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
					echo '<th>Job Code</th>';
					echo '<th>Job Title</th>';
					echo '<th>Job Family</th>';
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
						$jobSpecURL = '?page=jobSpec_details&jc=' .
							$row['JobCode'] .
							'&pp=' . $payPlan;
					}
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
						<td><?= $row['JobCode'] ?></td>
						<td><?= $row['JobTitle'] ?></td>
						<td><?= $row['JobFamily_long'] ?></td>
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
				c.JobFamilyID
			FROM class_specs c
			LEFT JOIN job_families j
				ON c.JobFamilyID = j.ID
			WHERE c.PayPlan = '$payPlan' AND
				c.Active = 1
			ORDER BY c.JobCode ASC
		";

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

	<?php
		/*
			If Class Spec was just deleted
		*/
		if (isset($_GET['jc'])) {
	?>
	<div class="row">
		<div class="col-xs-12">
			<div class="deleted">
				<p>
				<?php
				echo 'Class Spec (' . $_GET['jc'] . ' - ' . $sel_jobTitle_row['JobTitle'] . ') has been deleted';
				?>
				</p>
			</div>
		</div>
	</div>
	<?php
		}
	?>

	<div class"row">
		<div class="col-xs-9">
			<br />
			
			<ul id="myTabs" class="nav nav-tabs">
				<li class="active tab_title"><a data-toggle="tab" href="#ap">A&amp;P</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#usps">USPS</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#exec">Exec</a></li>
				<?php if ($loggedIn) { ?>
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
				<?php if ($loggedIn) { ?>
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
