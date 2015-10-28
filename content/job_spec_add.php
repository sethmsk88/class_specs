<link href="./css/job_spec_add.css" rel="stylesheet">
<script src="./scripts/job_spec_add.js"></script>

<?php
	//Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
?>

<div id="overlay" style="display:none;"></div>

<br />

<div class="container">

<!-- Testing textillate triggers --><!--
	<div class="row">
		<div class="col-lg-6">
			<button
				id="in-button"
				type="button"
				class="btn btn-primary"
				>
				In
			</button>
			<button
				id="out-button"
				type="button"
				class="btn btn-primary"
				>
				Out		
			</button>
			<div
				id="in-out-2"
				class="animatedText"
				>
				Testing Line 2
			</div>
			<div
				id="in-out"
				class="animatedText"
				>
				Testing
			</div>
		</div>
	</div>
-->

	<form
		name="addJobSpec-form"
		id="addJobSpec-form"
		role="form"
		method="post"
		action=""
		>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="jobCode">Job Code:</label>
					<input
						type="text"
						name="jobCode"
						id="jobCode"
						class="form-control"
						>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="jobTitle">Job Title:</label>
					<input
						type="text"
						name="jobTitle"
						id="jobTitle"
						class="form-control"
						>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">

					<?php
						// Get Job Families
						$sql = "
							SELECT *
							FROM job_families
						";

						$qry_jobFamilies = $conn->query($sql);
					?>

					<label for="jobFamily">Job Family:</label>
					<select
						name="jobFamily"
						id="jobFamily"
						class="form-control"
						>
						<option value=""></option>
						<?php
							while ($row = $qry_jobFamilies->fetch_assoc()){
								echo '<option value="' . $row['ID'] . '">' . $row['JobFamily_long'] . '</option>;';
							}
						?>
						
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="payPlan">Pay Plan:</label>
					<select
						name="payPlan"
						id="payPlan"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option name="usps" value="usps">USPS</option>
						<option name="ap" value="ap">A&amp;P</option>
						<option name="exec" value="exec">Executive</option>
						<option name="fac" value="fac">Faculty</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="oldPaygrade">Old Paygrade:</label>
					<input
						type="text"
						name="oldPaygrade"
						id="oldPaygrade"
						class="form-control"
						>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div id="eeoCode-container">
					<div class="form-group">
						<label for="eeoCode">EEO Code:</label>
						<select
							name="eeoCode"
							id="eeoCode"
							class="form-control"
							>
							<option value="" selected="selected"></option>
							<?php
								// Get EEO codes from table
								$sql = "
									SELECT *
									FROM eeo_codes
									ORDER BY EEO_Code_Descr
								";

								$qry_eeoCodes = $conn->query($sql);

								// Loop through EEO codes to create options
								while ($row = $qry_eeoCodes->fetch_assoc()){
									echo '<option value="' . $row['EEO_Code_ID'] . '">' . $row['EEO_Code_Descr'] . '</option>';
								}
							?>
						</select>
					</div>
				</div>

				<!-- Add New EEO Code -->
				<button
					id="addNewEEO-button"
					type="button"
					class="btn btn-primary"
					style="margin-bottom:20px;"
					>
					Add New EEO Code to Select Box
				</button>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="ipedsCode">IPEDS Code:</label>
					<input
						type="text"
						name="ipedsCode"
						id="ipedsCode"
						class="form-control"
						>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="cupaHR">CUPA-HR #:</label>
					<input
						type="text"
						name="cupaHR"
						id="cupaHR"
						class="form-control"
						>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="flsa">FLSA Status:</label>
					<select
						name="flsa"
						id="flsa"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="0">Non-Exempt</option>
						<option value="1">Exempt</option>
						<option value="2">Both</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div id="cbuCode-container">
					<div class="form-group">
						<label for="cbuCode">CBU Code:</label>
						<select
							name="cbuCode"
							id="cbuCode"
							class="form-control"
							>
							<option value="" selected="selected"></option>
							<?php
								// Get CBU codes from table
								$sql = "
									SELECT *
									FROM cbu_codes
									ORDER BY CBU_Code_Descr
								";

								$qry_cbuCodes = $conn->query($sql);

								// Loop through CBU codes to create options
								while ($row = $qry_cbuCodes->fetch_assoc()){
									echo '<option value="' . $row['CBU_Code_ID'] . '">' . $row['CBU_Code_Descr'] . '</option>';
								}
							?>		
						</select>
					</div>
				</div>

				<!-- Add New CBU Code -->
				<button
					id="addNewCBU-button"
					type="button"
					class="btn btn-primary"
					style="margin-bottom:20px;"
					>
					Add New CBU Code to Select Box
				</button>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-9">
				<div class="form-group">
					<label for="posDescr">Description</label>
					<textarea
						name="posDescr"
						id="posDescr"
						class="form-control"
						></textarea>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-9">
				<div class="form-group">
					<label for="eduExp">Education/Experience</label>
					<textarea
						name="eduExp"
						id="eduExp"
						class="form-control"
						></textarea>
				</div>
			</div>
		</div>

		<!-- Container for all competencies -->
		<div class="row">
			<div class="col-lg-6">
				<div id="competencies-container">
					<div class="form-group">
						<label for="competency_1">Recommended Competencies:</label>
						<select
							name="competency_1"
							id="competency_1"
							class="form-control"
							>
							<option value="" selected="selected"></option>
							<?php
								// Get competencies from table
								$sql = "
									SELECT *
									FROM competencies
									ORDER BY Descr
								";

								$qry_competencies = $conn->query($sql);

								// Loop through competencies to create options
								while ($row = $qry_competencies->fetch_assoc()){
									echo '<option value="' . $row['ID'] . '">' . $row['Descr'] . '</option>';
								}
							?>
						</select>						
					</div>
				</div>
				
				<!-- Buttons that follow Competencies -->
				<div class="row">
					<div class="col-sm-6">
						<!-- Add New Competency -->
						<button
							id="addNewComp-button"
							type="button"
							class="btn btn-primary"
							style="margin-bottom:10px; width:100%;"
							>
							Add New Competency to Select Box
						</button>
					</div>

					<!-- Clear List Button -->
					<div class="col-sm-offset-3 col-sm-3">
						<button
							id="clearCompList-button"
							type="button"
							class="btn btn-danger"
							style="width:100%;"
							>
							Clear List
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="backgroundCheck" style="margin-top:20px;">Police Background Check</label>
					<select
						name="backgroundCheck"
						id="backgroundCheck"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="financialDisclosure">Financial Disclosure</label>
					<select
						name="financialDisclosure"
						id="financialDisclosure"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="physical">Pre/Post Offer Physical</label>
					<select
						name="physical"
						id="physical"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="confidentialityStmt">Confidentiality Statement</label>
					<select
						name="confidentialityStmt"
						id="confidentialityStmt"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label for="childCareSecurityCheck">Child Care Security Check</label>
					<select
						name="childCareSecurityCheck"
						id="childCareSecurityCheck"
						class="form-control"
						>
						<option value="" selected="selected"></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-sm-4">
				<input
					type="submit"
					name="submitButton"
					id="submitButton"
					class="btn btn-md btn-success"
					value="Submit"
					style="width:100%;"
					>
			</div>
		</div>
	</form>

	<div class="row">
		<div class="col-lg-6">
			<div
				id="ajax_response_submit"
				class="animatedText"
				>
				<!-- To be filled by AJAX response -->
			</div>
		</div>
	</div>

	<?php
	// Padding at bottom of page
	for ($i = 0; $i < 20; $i++){
		echo '<br />';
	}
	?>

</div>

<?php
	// Disconnect from DB
	mysqli_close($conn);
?>

<!-- Add New Competency Form (absolutely positioned) -->
<div
	id="addNewComp-container"
	class="modalForm"
	>
	<form
		name="addNewComp-form"
		id="addNewComp-form"
		role="form"
		method="post"
		action=""
		>
		<div class="form-group">
			<input
				type="text"
				name="newCompetency"
				id="newCompetency"
				class="form-control"
				placeholder="New Competency"
				>
			<input
				type="submit"
				name="submitNewComp"
				id="submitNewComp"
				class="btn btn-md btn-primary"
				value="Add Competency"
				>
		</div>
	</form>
</div>

<div id="ajax_response_addNewComp">
	<!-- This div will be filled with response from AJAX request -->
</div>

<!-- Add New EEO Code Form (absolutely positioned) -->
<div
	id="addNewEEO-container"
	class="modalForm"
	>
	<form
		name="addNewEEO-form"
		id="addNewEEO-form"
		role="form"
		method="post"
		action=""
		>
		<div class="form-group">
			<input
				type="text"
				name="newEEO"
				id="newEEO"
				class="form-control"
				placeholder="New EEO Code"
				>
			<input
				type="submit"
				name="submitNewEEO"
				id="submitNewEEO"
				class="btn btn-md btn-primary"
				value="Add EEO Code"
				>
		</div>
	</form>
</div>

<div id="ajax_response_addNewEEO">
	<!-- This div will be filled with response from AJAX request --> 
</div>

<!-- Add New CBU Code Form (absolutely positioned) -->
<div
	id="addNewCBU-container"
	class="modalForm"
	>
	<form
		name="addNewCBU-form"
		id="addNewCBU-form"
		role="form"
		method="post"
		action=""
		>
		<div class="form-group">
			<input
				type="text"
				name="newCBU"
				id="newCBU"
				class="form-control"
				placeholder="New CBU Code"
				>
			<input
				type="submit"
				name="submitNewCBU"
				id="submitNewCBU"
				class="btn btn-md btn-primary"
				value="Add CBU Code"
				>
		</div>
	</form>
</div>

<div id="ajax_response_addNewCBU">
	<!-- This div will be filled with response from AJAX request --> 
</div>