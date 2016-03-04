<?php
/***  MAKE SURE PAGE WAS ACCESSED THROUGH INDEX.PHP  ***/
if (!isset($loggedIn)) {
	exit;
}
?>

<link href="./css/jobSpec_details.css" rel="stylesheet">
<script src="./scripts/jobSpec_details.js"></script>

<?php
	// If Job Code GET variable is not set, redirect to homepage.
	if (!isset($_GET['jc'])){
		// Declare Job Code param in case redirect fails
		$param_JobCode = "";
	}
	else{
		$param_JobCode = $_GET['jc'];
	}

	// Connect to DB
	$conn = new mysqli($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if (mysqli_connect_error()){
		echo mysqli_connect_error();
		exit();
	}

	$select_classSpec_sql = "
		SELECT c.*, p.IPEDS_SOCs AS IPEDS_Code, p.Contract, eeo.EEO_Code_Descr, cbu.CBU_Code_Descr
		FROM class_specs AS c
		JOIN pay_levels AS p
			ON c.JobCode = p.JobCode
		LEFT JOIN eeo_codes AS eeo
			ON c.EEO_Code_ID = eeo.EEO_Code_ID
		LEFT JOIN cbu_codes AS cbu
			ON c.CBU_Code_ID = cbu.CBU_Code_ID
		WHERE c.JobCode = ? AND
			c.Active = 1
	";
	
	// Prepare SQL statement
	if (!$stmt = $conn->prepare($select_classSpec_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}

	// Bind parameters
	if (!$stmt->bind_param('s', $param_JobCode)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	// Execute prepared statement
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}

	// Get query result
	$classSpecs_result = $stmt->get_result();

	// Close statement
	$stmt->close();

	// Get first row from result
	$classSpec_row = $classSpecs_result->fetch_assoc();

	/********************/
	/** Get Job Family **/
	$select_jobFamily_sql = "
		SELECT *
		FROM job_families
		WHERE ID = ?
	";
	$param_JobFamilyID = $classSpec_row['JobFamilyID'];
	if (!$stmt = $conn->prepare($select_jobFamily_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param('i', $param_JobFamilyID)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$jobFamily_result = $stmt->get_result();
	$stmt->close();
	$jobFamily_row = $jobFamily_result->fetch_assoc();


	/***************************************/
	/** Get complete list of job families **/
	$select_jobFamilies_sql = "
		SELECT *
		FROM job_families
	";
	$qry_jobFamilies = $conn->query($select_jobFamilies_sql);


	/*******************/
	/** Get Pay Level **/
	$select_payLevel_sql = "
		SELECT *
		FROM pay_levels
		WHERE JobCode = ?
	";
	if (!$stmt = $conn->prepare($select_payLevel_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param('s', $param_JobCode)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$payLevel_result = $stmt->get_result();
	$stmt->close();
	$payLevel_row = $payLevel_result->fetch_assoc();


	/***********************************/
	/** Get all employees in job code **/
	$select_emps_sql = "
		SELECT *
		FROM all_active_fac_staff
		WHERE JobCode = ?
	";
	if (!$stmt = $conn->prepare($select_emps_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param('s', $param_JobCode)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$emps_result = $stmt->get_result();
	$stmt->close();

	// Get actual min and max salaries in Job Code
	$actual_minSal = 9999999;
	$actual_maxSal = 0;

	while ($row = $emps_result->fetch_assoc()) {
		if ($row['Annual_Rt'] < $actual_minSal)
			$actual_minSal = $row['Annual_Rt'];
		
		if ($row['Annual_Rt'] > $actual_maxSal)
			$actual_maxSal = $row['Annual_Rt'];
	}


	/********************************************/
	/** Get all competencies for this job code **/
	$select_competencies_sql = "
		SELECT *
		FROM class_specs_rec_competencies AS a
		JOIN competencies AS b
		ON a.Competency_ID = b.ID
		WHERE a.ClassSpec_ID = ?
		ORDER BY b.Descr
	";
	$param_ClassSpecID = $classSpec_row['ID'];
	if (!$stmt = $conn->prepare($select_competencies_sql)){
		echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
	}
	if (!$stmt->bind_param('i', $param_ClassSpecID)){
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	if (!$stmt->execute()){
		echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
	}
	$competencies_result = $stmt->get_result();
	$stmt->close();


	/***********************/
	/** Get all EEO codes **/
	$select_eeoCodes_sql = "
		SELECT *
		FROM eeo_codes
		ORDER BY EEO_Code_Descr
	";
	$qry_eeoCodes = $conn->query($select_eeoCodes_sql);

	/***********************/
	/** Get all CBU codes **/
	$select_cbuCodes_sql = "
		SELECT *
		FROM cbu_codes
		ORDER BY CBU_Code_Descr
	";
	$qry_cbuCodes = $conn->query($select_cbuCodes_sql);


	/***************************
	/** Get all competencies **/
	// Get competencies from table
	$select_competencies_sql = "
		SELECT *
		FROM competencies
		ORDER BY Descr
	";
	$qry_competencies = $conn->query($select_competencies_sql);

	/*******************************/
	/** Get all pay levels        **/
	/** (Numeric representations) **/
	$select_payLevelNums_sql = "
		SELECT *
		FROM pay_levels_descr
		ORDER BY PayLevel
	";
	$qry_payLevelNums = $conn->query($select_payLevelNums_sql);


	/*
		Make sure class spec has a pay level assigned to it
		in the pay_levels table before running this query
	*/
	if ($payLevel_row['PayLevel'] !== null) {

		/*************************/
		/** Get Pay Level Range **/
		$select_payLevelRange_sql = "
			SELECT *
			FROM pay_levels_descr
			WHERE PayLevel = " . $payLevel_row['PayLevel'];
		$qry_payLevelRange = $conn->query($select_payLevelRange_sql);
		$payLevelRange_row = $qry_payLevelRange->fetch_assoc();
	}

?>

<div id="overlay" style="display:none;"></div>

<br />

<?php
	if ($loggedIn) {
?>
<div class="container default-style">
	<?php
		/*
			If URL var pp exists, create a Back button
		*/
		if (isset($_GET['pp'])) {
	?>
	<div class="row">
		<div class="col-lg-2">
			<button
				id="back-btn"
				type="button"
				class="btn btn-primary"
				style="margin-bottom:20px;"
				payPlan="<?php echo $_GET['pp']; ?>"
				>
				<span class="glyphicon glyphicon glyphicon-arrow-left" aria-hidden="true" style="padding-right:10px;"></span>Back to Homepage
			</button>
		</div>
	</div>
	<?php
		}
	?>

	<form
		name="editJobSpec-form"
		id="editJobSpec-form"
		role="form"
		class="form-horizontal"
		method="post"
		action=""
		>
		
		<!-- (HIDDEN) Class Spec ID -->
		<input
			name="classSpecID"
			type="hidden"
			value="<?php echo $classSpec_row['ID']; ?>"
			>

		<!-- Job Code -->
		<div class="row">
			<div class="form-group">
				<label for="jobCode" class="control-label col-lg-2">Job Code:</label>
				<div class="col-lg-4">
					<input
						id="jobCode"
						name="jobCode"
						type="text"
						class="form-control editable"
						value="<?php echo $classSpec_row['JobCode']; ?>"
						>
				</div>
			</div>
		</div>


		<!-- Job Title -->
		<div class="row">
			<div class="form-group">
				<label for="jobTitle" class="control-label col-lg-2">Job Title:</label>
				<div class="col-lg-4">
					<input
						id="jobTitle"
						name="jobTitle"
						type="text"
						class="form-control editable"
						value="<?php echo $classSpec_row['JobTitle']; ?>"
						>
				</div>
			</div>
		</div>

		<!-- Job Family -->
		<div class="row">
			<div class="form-group">
				<label for="jobFamily" class="control-label col-lg-2">Job Family:</label>
				<div class="col-lg-4">
					<select
						name="jobFamily"
						id="jobFamily"
						class="form-control"
						>
						<option value=""></option>
						<?php							
							while ($row = $qry_jobFamilies->fetch_assoc()){
															
								if ($row['ID'] == $jobFamily_row['ID']) {
									$optionSelected = 'selected="selected"';
								}
								else {
									$optionSelected = '';
								}
								echo '<option value="' . $row['ID'] . '" ' . $optionSelected . '>' . $row['JobFamily_long'] . '</option>;';
							}
						?>
					</select>
				</div>
			</div>
		</div>

		<!-- Pay Plan -->
		<div class="row">
			<div class="form-group">
				<label for="payPlan" class="control-label col-lg-2">Pay Plan:</label>
				<div class="col-lg-4">
					<select
						name="payPlan"
						id="payPlan"
						class="form-control"
						>
						<option value=""></option>
						<option name="usps" value="usps">USPS</option>
						<option name="ap" value="ap">A&amp;P</option>
						<option name="exec" value="exec">Executive</option>
						<option name="fac" value="fac">Faculty</option>
					</select>

					<!-- Create JS to set the correct option -->
					<script>
						$('#payPlan').val('<?php echo $classSpec_row['PayPlan']; ?>');
					</script>
				</div>
			</div>
		</div>

		<!-- Pay Level -->
		<div class="row">
			<div class="form-group">
				<label for="payLevel" class="control-label col-lg-2">Pay Level:</label>
				<div class="col-lg-4">
					<select
						id="payLevel"
						name="payLevel"
						class="form-control"
						>
						<option value=""></option>
						<?php
							while ($row = $qry_payLevelNums->fetch_assoc()) {
								if ($row['PayLevel'] == $payLevel_row['PayLevel']) {
									$optionSelected = 'selected="selected"';
								}
								else {
									$optionSelected = '';
								}

								echo '<option value="' . $row['PayLevel'] . '" ' . $optionSelected . '>' . $row['PayLevel'] . '</option>';
							}
						?>
					</select>
				</div>
			</div>
		</div>

		<!-- Old Paygrade -->
		<div class="row">
			<div class="form-group">
				<label for="oldPaygrade" class="control-label col-lg-2">Old Paygrade:</label>
				<div class="col-lg-4">
					<input
						id=""
						name="oldPaygrade"
						type="text"
						class="form-control"
						value="<?php echo $payLevel_row['OldPayGrade']; ?>"
						>
				</div>
			</div>
		</div>

		<!-- EEO Code -->
		<div class="row">
			<div class="form-group">
				<label for="eeoCode" class="control-label col-lg-2">EEO Code:</label>
				<div class="col-lg-4">
					<select
						name="eeoCode"
						id="eeoCode"
						class="form-control"
						>
						<option value=""></option>
						<?php
							while ($row = $qry_eeoCodes->fetch_assoc()) {

								if ($row['EEO_Code_ID'] == $classSpec_row['EEO_Code_ID']) {
									$optionSelected = 'selected="selected"';
								}
								else {
									$optionSelected = '';
								}
						
								echo '<option value="' . $row['EEO_Code_ID'] . '" ' . $optionSelected . '>' . $row['EEO_Code_Descr'] . '</option>';
						
							}
						?>
					</select>
				</div>
			</div>
		</div>

		<!-- IPEDS Code -->
		<div class="row">
			<div class="form-group">
				<label for="jobTitle" class="control-label col-lg-2">IPEDS Code:</label>
				<div class="col-lg-4">
					<input
						id="ipedsCode"
						name="ipedsCode"
						type="text"
						class="form-control"
						value="<?php echo $classSpec_row['IPEDS_Code']; ?>"
						>
				</div>
			</div>
		</div>

		<!-- CUPA-HR Code -->
		<div class="row">
			<div class="form-group">
				<label for="cupaHR" class="control-label col-lg-2">CUPA-HR #:</label>
				<div class="col-lg-4">
					<input
						id="cupaHR"
						name="cupaHR"
						type="text"
						class="form-control"
						value="<?php echo $classSpec_row['CUPA_HR']; ?>"
						>
				</div>
			</div>
		</div>

		<!-- FLSA Status -->
		<div class="row">
			<div class="form-group">
				<label for="flsa" class="control-label col-lg-2">FLSA Status:</label>
				<div class="col-lg-4">
					<select
						name="flsa"
						id="flsa"
						class="form-control"
						>
						<option value=""></option>
						<option value="0">Non-Exempt</option>
						<option value="1">Exempt</option>
						<option value="2">Both</option>
					</select>

					<!-- Create JS to set the correct option -->
					<script>
						$('select#flsa option[value="' + <?php echo $classSpec_row['FLSA']; ?> + '"]').prop('selected', true);
					</script>

				</div>
			</div>
		</div>


		<!-- CBU Code -->
		 <div class="row">
			<div class="form-group">
				<label for="cbuCode" class="control-label col-lg-2">CBU Code:</label>
				<div class="col-lg-4">
					<select
						name="cbuCode"
						id="cbuCode"
						class="form-control"
						>
						<option value=""></option>
						<?php
							while ($row = $qry_cbuCodes->fetch_assoc()) {

								if ($row['CBU_Code_ID'] == $classSpec_row['CBU_Code_ID']) {
									$optionSelected = 'selected="selected"';
								}
								else {
									$optionSelected = '';
								}
								echo '<option value="' . $row['CBU_Code_ID'] . '" ' . $optionSelected . '>' . $row['CBU_Code_Descr'] . '</option>;';
							}
						?>
					</select>
				</div>
			</div>
		</div>

		<!-- Description -->
		<div class="row">
			<div class="form-group">
				<label for="posDescr" class="control-label col-lg-2">Position Description:</label>
				<div class="col-lg-8">
					<textarea
						name="posDescr"
						id="posDescr"
						class="form-control"
						><?php echo stripslashes($classSpec_row['PositionDescr']); ?></textarea>
				</div>
			</div>
		</div>

		<!-- Education/Experience -->
		<div class="row">
			<div class="form-group">
				<label for="eduExp" class="control-label col-lg-2">Education/Experience:</label>
				<div class="col-lg-8">
					<textarea
						name="eduExp"
						id="eduExp"
						class="form-control"
						><?php echo stripslashes($classSpec_row['EducationExp']); ?></textarea>
				</div>
			</div>
		</div>



		<!-- Recommended Competencies -->
		<div class="row" style="padding-bottom:0;">
			<div class="form-group" style="margin-bottom:0;">
				<label for="" class="control-label col-lg-2">Recommended Competencies:</label>
				<div class="col-lg-10">

					<!-- Create a table -->
					<table id="competenciesTable" class="table table-bordered">
						<tbody>
							<!--
								Loop through all stored competencies for this job code.
								On each row, show a stored competency, and a remove button.
							-->
							<?php
								while ($row = $competencies_result->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo stripslashes($row['Descr']); ?></td>
								<td>
									<button
										id="<?php echo $row['Competency_ID']; ?>"
										type="button"
										class="icon-btn edit-comp"
										title="Edit"
										>
										<span class="icon-edit glyphicon glyphicon-pencil" aria-hidden="true"></span>
									</button>
								</td>
								<td>
									<button
										id="<?php echo $row['Competency_ID']; ?>"
										type="button"
										class="icon-btn del-comp"
										title="Delete"
										>
										<span class="icon-remove glyphicon glyphicon-remove" aria-hidden="true"></span>
									</button>
								</td>
							</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
					<div id="competencies-container">

						<!-- Create a select box and fill it with options -->
						<select
							name="competency_1"
							id="competency_1"
							class="form-control"
							style="margin-bottom:10px;"
							>
							<option value="" selected="selected"></option>

							<!-- Loop through competencies to create options -->
							<?php
								while ($row = $qry_competencies->fetch_assoc()) {
									echo '<option value="' . $row['ID'] . '">';
										echo $row['Descr'];
									echo '</option>';
								}
							?>
						</select>
					</div>
				</div>
			</div>
		</div>



		<!-- Police Background Check -->
		<div class="row">
			<div class="form-group">
				<label for="" class="control-label col-lg-2">Police Background Check:</label>
				<div class="col-lg-8">
					<label class="radio-inline">
						<input type="radio" name="backgroundCheck" value="0">No
					</label>
					<label class="radio-inline">
						<input type="radio" name="backgroundCheck" value="1">Yes
					</label>

					<!-- Create JS to set the radio button -->
					<script>
						$('input:radio[name=backgroundCheck][value="<?php echo $classSpec_row['BackgroundCheck']; ?>"]').prop('checked', true);
					</script>
				</div>
			</div>
		</div>

		<!-- Financial Disclosure -->
		<div class="row">
			<div class="form-group">
				<label for="" class="control-label col-lg-2">Financial Disclosure:</label>
				<div class="col-lg-8">
					<label class="radio-inline">
						<input type="radio" name="financialDisclosure" value="0">No
					</label>
					<label class="radio-inline">
						<input type="radio" name="financialDisclosure" value="1">Yes
					</label>

					<!-- Create JS to set the radio button -->
					<script>
						$('input:radio[name=financialDisclosure][value="<?php echo $classSpec_row['FinancialDisclosure']; ?>"]').prop('checked', true);
					</script>
				</div>
			</div>
		</div>

		<!-- Pre/Post Offer Physical -->
		<div class="row">
			<div class="form-group">
				<label for="" class="control-label col-lg-2">Pre/Post Offer Physical:</label>
				<div class="col-lg-8">
					<label class="radio-inline">
						<input type="radio" name="physical" value="0">No
					</label>
					<label class="radio-inline">
						<input type="radio" name="physical" value="1">Yes
					</label>

					<!-- Create JS to set the radio button -->
					<script>
						$('input:radio[name=physical][value="<?php echo $classSpec_row['Physical']; ?>"]').prop('checked', true);
					</script>
				</div>
			</div>
		</div>

		<!-- Confidentiality Statement -->
		<div class="row">
			<div class="form-group">
				<label for="" class="control-label col-lg-2">Confidentiality Statement:</label>
				<div class="col-lg-8">
					<label class="radio-inline">
						<input type="radio" name="confidentialityStmt" value="0">No
					</label>
					<label class="radio-inline">
						<input type="radio" name="confidentialityStmt" value="1">Yes
					</label>

					<!-- Create JS to set the radio button -->
					<script>
						$('input:radio[name=confidentialityStmt][value="<?php echo $classSpec_row['ConfidentialityStmt']; ?>"]').prop('checked', true);
					</script>
				</div>
			</div>
		</div>

		<!-- Child Care Security Check -->
		<div class="row">
			<div class="form-group">
				<label for="" class="control-label col-lg-2">Child Care Security Check:</label>
				<div class="col-lg-8">
					<label class="radio-inline">
						<input type="radio" name="childCareSecurityCheck" value="0">No
					</label>
					<label class="radio-inline">
						<input type="radio" name="childCareSecurityCheck" value="1">Yes
					</label>

					<!-- Create JS to set the radio button -->
					<script>
						$('input:radio[name=childCareSecurityCheck][value="<?php echo $classSpec_row['ChildCareSecurityCheck']; ?>"]').prop('checked', true);
					</script>
				</div>
			</div>
		</div>

		<!-- Submit Changes Button -->
		<div class="row">
			<div class="col-lg-offset-1 col-lg-2">
				<input
					type="submit"
					name="submitButton"
					id="submitButton"
					class="btn btn-md btn-success"
					value="Submit Changes"
					style="width:100%;"
					>
			</div>
		</div>

		<!-- AJAX Response -->
		<div class="row">
			<div class="col-lg-6">
				<div id="ajax_response_submit"></div>
			</div>
		</div>

	</form>
</div>
<!-- End Container -->

<?php
	}
	else { // If not logged in
?>
<div class="container default-style">

	<div class="row">
		<div class="col-lg-2">
		<?php
			/*
				If URL var pp exists, create a Back button
			*/
			if (isset($_GET['pp'])) {
		?>
			<button
				id="back-btn"
				type="button"
				class="btn btn-primary"
				style="margin-bottom:20px;"
				payPlan="<?php echo $_GET['pp']; ?>"
				>
				<span class="glyphicon glyphicon glyphicon-arrow-left" aria-hidden="true" style="padding-right:10px;"></span>Back to Homepage
			</button>
		<?php
			}
		?>
		</div>

		<?php
		if ($loggedIn) {
		?>
		<div class="col-lg-offset-8 col-lg-2">
			<button
				id="deleteClassSpec"
				jobCode="<?php echo $classSpec_row['JobCode']; ?>"
				type="button"
				class="btn btn-danger"
				>
				Delete Class Spec
			</button>
		</div>
		<?php
		}
		?>
	</div>

	<div class="row">
		<div class="col-lg-3">
			<span class="myLabel">Job Code:</span>
			<?php echo $classSpec_row['JobCode']; ?>
		</div>
		<div class="col-lg-9">
			<span class="myLabel">Job Title:</span>
			<?php echo stripslashes($classSpec_row['JobTitle']); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">Job Family:</span>
			<?php echo $jobFamily_row['JobFamily_long']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">Pay Plan:</span>
			<?php 
				echo convertPayPlan($classSpec_row['PayPlan'], 'long');

				// If it is a contract position, append "Multi year"
				if ($classSpec_row['Contract'] == 1) {
					echo '/Multi year';
				}
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">Recommended Competitive Pay Range for Postings:</span>
			<?php
			if ($loggedIn) {
				echo '$' . number_format($payLevel_row['MinSal'], 2, '.', ',') . ' - ';
				if ($payLevel_row['MaxSal'] >= 0) {
					echo '$' . number_format($payLevel_row['MaxSal'], 2, '.', ',');
				}
				else {
					echo 'No Max';
				}
			}
			?>
		</div>
		<div class="col-lg-12 note">
			(Range estimated from internal and external benchmarks and does not represent the definitive minimum and maximum of the classification, please see Pay Level range for the recommended minimum and maximum of classifications in this level of responsibility)
		</div>
	</div>


	<?php
		/*
			Make sure class spec has a pay level assigned to it
			in the pay_levels table before running this query
		*/
		if ($payLevel_row['PayLevel'] !== null) {
	?>
	<div class="row">
		<div class="col-lg-3">
			<span class="myLabel">Pay Level:</span>
			<?php
			if ($loggedIn) {
				echo $payLevel_row['PayLevel'];
			}
			?>
		</div>
		<div class="col-lg-9">
			<span class="myLabel">Pay Level Range:</span>
			<?php
			if ($loggedIn) {
				echo '$' . number_format($payLevelRange_row['PayLevelMin'], 2, '.', ',') . ' - ';
				if ($payLevelRange_row['PayLevelMax'] >= 0) {
					echo '$' . number_format($payLevelRange_row['PayLevelMax'], 2, '.', ',');
				}
				else {
					echo 'No Max';
				}
			}
			?>
		</div>
	</div>
	<?php
		}
	?>


	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">Old Paygrade:</span>
			<?php echo $classSpec_row['OldPayGrade']; ?>
		</div>
	</div>				

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">Approximate Number of People in Classification:</span>
			<?php
				echo $emps_result->num_rows . '&nbsp;&nbsp;';
				if ($emps_result->num_rows)
					echo '<span class="bg-success text-success" style="padding:0 3px;">Active</span>';
				else
					echo '<span class="bg-danger text-danger" style="padding:0 3px;">Inactive</span>';
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">EEO Code:</span>
			<?php echo $classSpec_row['EEO_Code_Descr']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">IPEDS Code:</span>
			<?php echo $classSpec_row['IPEDS_Code']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">CUPA-HR #:</span>
			<?php echo $classSpec_row['CUPA_HR']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">This position is FLSA:</span>
			<?php echo convertFLSA($classSpec_row['FLSA'], 'string'); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<span class="myLabel">CBU Code:</span>
			<?php echo $classSpec_row['CBU_Code_Descr']; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>Position Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo stripslashes($classSpec_row['PositionDescr']); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<br />

	<div class="row">
		<div class="col-lg-12 note">
			Please Note: Examples listed are not an all-inclusive list of duties and tasks.
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>Education/Experience</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo stripslashes($classSpec_row['EducationExp']); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<!-- <table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td></td>
					</tr>
				</tbody>
			</table> -->

	<div class="row">
		<div class="col-lg-12">
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>Recommended Competencies</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Create cell for each competency
					while ($row = $competencies_result->fetch_assoc()) {
						echo '<tr>';
						echo '<td>' . stripslashes($row['Descr']) . '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<table id="otherReq-table" class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th colspan="2">Other Specific Requirements</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Police Background Check</td>
						<td>
							<?php echo convertYesNo($classSpec_row['BackgroundCheck']); ?>
						</td>
					</tr>
					<tr>
						<td>Financial Disclosure</td>
						<td>
							<?php echo convertYesNo($classSpec_row['FinancialDisclosure']); ?>
						</td>
					</tr>
					<tr>
						<td>Pre/Post Offer Physical</td>
						<td>
							<?php echo convertYesNo($classSpec_row['Physical']); ?>
						</td>
					</tr>
					<tr>
						<td>Confidentiality Statement</td>
						<td>
							<?php echo convertYesNo($classSpec_row['ConfidentialityStmt']); ?>
						</td>
					</tr>
					<tr>
						<td>Child Care Security Check</td>
						<td>
							<?php echo convertYesNo($classSpec_row['ChildCareSecurityCheck']); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	
</div><!-- End container -->
<?php
	} // Endif
?>






<!-- Footer -->
<?php
	mysqli_close($conn);

	/** Spacing for bottom of page **/
	for ($i=0; $i<20; $i++){
		echo '<br />';
	}
?>

<?php if ($loggedIn) { ?>
<!-- Edit Competency Form (absolutely positioned) -->
<div
	id="editComp-container"
	class="modalForm"
	>
	<form
		name="editComp-form"
		id="editComp-form"
		role="form"
		method="post"
		action=""
		>
		<div class="form-group">

			<!-- Filled with compID of the comp that is being edited -->
			<input
				name="_compID"
				id="_compID"
				type="hidden"
				>

			<label for="comp">Edit Competency</label>
			<textarea
				name="updatedComp"
				id="updatedComp"
				class="form-control"
				style="width:100%;"
				></textarea>
			<input
				type="submit"
				name="submitCompChanges"
				id="submitCompChanges"
				class="btn btn-md btn-primary"
				value="Submit Changes"
				>
		</div>
	</form>
</div>
<?php } ?>
