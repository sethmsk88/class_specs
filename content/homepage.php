<?php
	/****************************************************/
	/* Function: outputJobCodeTable                     */
	/* Description: Output a table containing all Jobs  */
	/*    in qry_result. qry_result is the result of a  */
	/*    query on the pay_levels table.                */
	/****************************************************/
	function outputJobCodeTable($qry_result, $tableId)
	{
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
						$jobSpecURL = '?page=job_spec_add&jc=' . $row['JobCode'];
					}
					else {
						$jobSpecURL = '?page=job_spec_details&jc=' . $row['JobCode'];
					}

					echo "<tr class='clickable' onclick='window.location.assign(\"" . $jobSpecURL . "\");'>";
						echo '<td>' . $row['JobCode'] . '</td>';
						echo '<td>' . $row['JobTitle'] . '</td>';
						echo '<td>' . $row['JobFamily_long'] . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}


	// Connect to DB
	$conn = mysqli_connect($dbInfo['dbIP'], $dbInfo['user'], $dbInfo['password'], $dbInfo['dbName']);
	if ($conn->connect_errno){
		echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
	}

	// Get A&P jobs
	$sql = "
		SELECT p.JobCode, p.JobTitle, c.ID classID, j.JobFamily_long
		FROM pay_levels p
		INNER JOIN job_families j
		ON p.JobFamily = j.JobFamily_short
		LEFT JOIN class_specs c
		ON p.JobCode = c.JobCode
		WHERE p.PayPlan = 'A&P'
		ORDER BY p.JobCode ASC";

	// Run Query for A&P
	if (!($qry_ap = $conn->query($sql))){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
		echo "<br />" . $sql;
	}

	// Get USPS jobs
	$sql = "
		SELECT p.JobCode, p.JobTitle, c.ID classID, j.JobFamily_long
		FROM pay_levels p
		INNER JOIN job_families j
		ON p.JobFamily = j.JobFamily_short
		LEFT JOIN class_specs c
		ON p.JobCode = c.JobCode
		WHERE p.PayPlan = 'USPS'
		ORDER BY p.JobCode ASC";

	// Run Query for USPS
	if (!($qry_usps = $conn->query($sql))){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
		echo "<br />" . $sql;
	}

	// Get Exec jobs
	$sql = "
		SELECT p.JobCode, p.JobTitle, c.ID classID, j.JobFamily_long
		FROM pay_levels p
		INNER JOIN job_families j
		ON p.JobFamily = j.JobFamily_short
		LEFT JOIN class_specs c
		ON p.JobCode = c.JobCode
		WHERE p.PayPlan = 'EXC'
		ORDER BY p.JobCode ASC";

	// Run Query for Exec
	if (!($qry_exec = $conn->query($sql))){
		echo "Query failed: (" . $conn->errno . ") " . $conn->error;
		echo "<br />" . $sql;
	}

	// Close DB connection
	mysqli_close($conn);
?>	

<div class="container">
	<div class"row">
		<div class="col-xs-9">
			<br />
			
			<ul id="myTabs" class="nav nav-tabs">
				<li class="active tab_title"><a data-toggle="tab" href="#ap">A&amp;P</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#usps">USPS</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#exec">Exec</a></li>
				<li class="tab_title"><a data-toggle="tab" href="#fac">Faculty</a></li>
			</ul>

			<div class="tab-content myTabs">

				<!--~~~~~~~~~-->
				<!-- A&P Tab -->
				<!--~~~~~~~~~-->
				<div id="ap" class="tab-pane fade in active">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($qry_ap, 'classSpecs_ap');?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~-->
				<!-- USPS Tab -->
				<!--~~~~~~~~~~-->
				<div id="usps" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($qry_usps, 'classSpecs_usps');?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~-->
				<!-- Exec Tab -->
				<!--~~~~~~~~~~-->
				<div id="exec" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<?php outputJobCodeTable($qry_exec, 'classSpecs_exec');?>
						</div>
					</div>
				</div>

				<!--~~~~~~~~~~~~~-->
				<!-- Faculty Tab -->
				<!--~~~~~~~~~~~~~-->
				<div id="fac" class="tab-pane fade">
					<div class="row">
						<div class="col-md-12">
							<table id="classSpecs_fac" class="table table-striped">
								<thead>
									<tr>
										<th>Job Code</th>
										<th>Job Title</th>
										<th>Salary Range</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- End of page padding -->
<div style="clear:both;padding:100px 0;"></div>


