<?php
/* If FormData was posted to this page */
if (isset($_FILES['fileToUpload'])) {

	require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php');
	require_once("../includes/functions.php");

	// Start session or regenerate session id
    sec_session_start();

	$uploads_dir = '../uploads/';

	// Start timer
	$timerStart = microtime(true);

	$error = array();
	$fileName = $_FILES['fileToUpload']['name'];
	$fileSize = $_FILES['fileToUpload']['size'];
	$fileTmpName = $_FILES['fileToUpload']['tmp_name'];
	$fileType = $_FILES['fileToUpload']['type'];
	$fileName_exploded = explode('.', $fileName);
	$fileExt = strtolower(end($fileName_exploded));
	
	$fileName = make_unique_filename($fileName_exploded[0] . '.' . $fileExt, 'uploads/');

	// Check to see if extension is valid
	$extensions = array("csv");
	if (in_array($fileExt, $extensions) === false) {
		$errors[] = "Invalid file type. Please choose a CSV file.";
	}

	// Make sure file is not too large
	$maxFileSize = 1024 * 1024 * 64; // 64MB
	if ($fileSize > $maxFileSize) {
		$errors[] = "Max file size exceeded. File size must be less than 64MB";
	}

	// Make sure filename is not too long
	$maxFileNameLength = 250;
	if (strlen($fileName) > $maxFileNameLength) {
		$errors[] = "File name is too long (max 250 characters)";
	}

	// If upload failed display error message
	if (empty($errors) == false) {
		echo '<div class="alert alert-danger"><strong>Error!</strong> File was NOT uploaded<br />';
		foreach ($errors as $errorMsg) {
			echo $errorMsg . '<br />';
		}
		echo '</div>';
	}
	// Else, parse uploaded file, and insert each row into the table
	else {
		move_uploaded_file($fileTmpName, $uploads_dir . $fileName);

		/* Delete all rows from all_actives table */
		$del_all_sql = "
			DELETE FROM hrodt.all_active_fac_staff
		";
		
		$conn->query($del_all_sql);

		// Parse CSV file into array
		$csv = array_map('str_getcsv', file($uploads_dir . $fileName));

		$column_headers = array();

		// Iterate through each column in the first row
		// Store column headers in array
		foreach ($csv[0] as $col) {
			array_push($column_headers, $col);
		}

		$numRows = 0; // Num rows inserted
		$duplicates_array = array();

		/* Insert row into table */
		$insert_csvRow_sql = "
			INSERT INTO hrodt.all_active_fac_staff (
				EmplID,
				EffDt,
				EmplRcd,
				Name,
				PayGroup,
				SalAdminPlan,
				Grade,
				PosNum,
				PosEntryDate,
				FundingDeptID,
				FundingDept,
				DeptID,
				WorkingDept,
				JobFamily,
				JobCode,
				JobTitle,
				UnionCode,
				Annual_Rt,
				BiweeklyRate,
				HourlyRate,
				FTE,
				Sex,
				TenureStatus,
				EthnicCD,
				Race,
				FAMU_HireDate,
				BudgetedWeeks,
				BirthDate
				)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		";

		if (!$stmt = $conn->prepare($insert_csvRow_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error . '<br />';
		}
		else if (!$stmt->bind_param("isissssisisisssssddddsssssds",
			$param_int_EmplID,
			$param_str_EffDt,
			$param_int_EmplRcd,
			$param_str_Name,
			$param_str_PayGroup,
			$param_str_SalAdminPlan,
			$param_str_Grade,
			$param_int_PosNum,
			$param_str_PosEntryDate,
			$param_int_FundingDeptID,
			$param_str_FundingDept,
			$param_int_DeptID,
			$param_str_WorkingDept,
			$param_str_JobFamily,
			$param_str_JobCode,
			$param_str_JobTitle,
			$param_str_UnionCode,
			$param_double_Annual_Rt,
			$param_double_BiweeklyRate,
			$param_double_HourlyRate,
			$param_double_FTE,
			$param_str_Sex,
			$param_str_TenureStatus,
			$param_str_EthnicCD,
			$param_str_Race,
			$param_str_FAMU_HireDate,
			$param_double_BudgetedWeeks,
			$param_str_BirthDate
			)) {
			echo 'Binding parameters failed (' . $stmt->errno . ') ' . $stmt->error . '<br />';
		}

		// Get indexes for the expected columns
		$col_idx_EFFDT = array_search("EFFDT", $column_headers);
		$col_idx_EMPLID = array_search("EMPLID", $column_headers);
		$col_idx_EMPL_RCD = array_search("EMPL_RCD", $column_headers);
		$col_idx_NAME = array_search("NAME", $column_headers);
		$col_idx_PAYGROUP = array_search("PAYGROUP", $column_headers);
		$col_idx_SAL_ADMIN_PLAN = array_search("SAL_ADMIN_PLAN", $column_headers);
		$col_idx_GRADE = array_search("GRADE", $column_headers);
		$col_idx_POSITION_NBR = array_search("POSITION_NBR", $column_headers);
		$col_idx_POSITION_ENTRY_DT = array_search("POSITION_ENTRY_DT", $column_headers);
		$col_idx_DEPTID = array_search("DEPTID", $column_headers);
		$col_idx_FUNDING_DEPT = array_search("FUNDING_DEPT", $column_headers);
		$col_idx_FAM_CERT_DEPT = array_search("FAM_CERT_DEPT", $column_headers);
		$col_idx_WORKING_DEPT = array_search("WORKING_DEPT", $column_headers);
		$col_idx_JOB_FAMILY = array_search("JOB_FAMILY", $column_headers);
		$col_idx_JOBCODE = array_search("JOBCODE", $column_headers);
		$col_idx_JOB_TITLE = array_search("JOB_TITLE", $column_headers);
		$col_idx_UNION_CD = array_search("UNION_CD", $column_headers);
		$col_idx_ANNUAL_RT = array_search("ANNUAL_RT", $column_headers);
		$col_idx_FAM_BUDGETED_WEEKS = array_search("FAM_BUDGETED_WEEKS", $column_headers);
		$col_idx_BIWKLY_RT = array_search("BIWKLY_RT", $column_headers);
		$col_idx_HOURLY_RT = array_search("HOURLY_RT", $column_headers);
		$col_idx_FTE = array_search("FTE", $column_headers);
		$col_idx_SEX = array_search("SEX", $column_headers);
		$col_idx_BIRTHDATE = array_search("BIRTHDATE", $column_headers);
		$col_idx_TENURE_STATUS = array_search("TENURE_STATUS", $column_headers);
		$col_idx_ETHNIC_CD = array_search("ETHNIC_CD", $column_headers);
		$col_idx_RACE = array_search("RACE", $column_headers);
		$col_idx_FAM_HIRE_DT = array_search("FAM_HIRE_DT", $column_headers);


		/*** Column Errors and Warnings ***/
		// If a required column is missing, add to errors array
		// If a non-required column is missing, add to warnings array
		$column_errors = array();
		$column_warnings = array();

		if ($col_idx_EFFDT === false)
			array_push($column_errors, "EFFDT");
		if ($col_idx_EMPLID === false)
			array_push($column_errors, "EMPLID");
		if ($col_idx_PAYGROUP === false)
			array_push($column_errors, "PAYGROUP");
		if ($col_idx_SAL_ADMIN_PLAN === false)
			array_push($column_errors, "SAL_ADMIN_PLAN");
		if ($col_idx_DEPTID === false)
			array_push($column_errors, "DEPTID");
		if ($col_idx_FUNDING_DEPT === false)
			array_push($column_errors, "FUNDING_DEPT");
		if ($col_idx_FAM_CERT_DEPT === false)
			array_push($column_errors, "FAM_CERT_DEPT");
		if ($col_idx_WORKING_DEPT === false)
			array_push($column_errors, "WORKING_DEPT");
		if ($col_idx_JOB_FAMILY === false)
			array_push($column_errors, "JOB_FAMILY");
		if ($col_idx_JOBCODE === false)
			array_push($column_errors, "JOBCODE");
		if ($col_idx_JOB_TITLE === false)
			array_push($column_errors, "JOB_TITLE");
		if ($col_idx_ANNUAL_RT === false)
			array_push($column_errors, "ANNUAL_RT");
		if ($col_idx_FTE === false)
			array_push($column_errors, "FTE");
		if ($col_idx_TENURE_STATUS === false)
			array_push($column_errors, "TENURE_STATUS");

		if ($col_idx_EMPL_RCD === false)
			array_push($column_warnings, "EMPL_RCD");
		if ($col_idx_NAME === false)
			array_push($column_warnings, "NAME");
		if ($col_idx_GRADE === false)
			array_push($column_warnings, "GRADE");
		if ($col_idx_POSITION_NBR === false)
			array_push($column_warnings, "POSITION_NBR");
		if ($col_idx_POSITION_ENTRY_DT === false)
			array_push($column_warnings, "POSITION_ENTRY_DT");
		if ($col_idx_UNION_CD === false)
			array_push($column_warnings, "UNION_CD");
		if ($col_idx_FAM_BUDGETED_WEEKS === false)
			array_push($column_warnings, "FAM_BUDGETED_WEEKS");
		if ($col_idx_BIWKLY_RT === false)
			array_push($column_warnings, "BIWKLY_RT");
		if ($col_idx_HOURLY_RT === false)
			array_push($column_warnings, "HOURLY_RT");
		if ($col_idx_SEX === false)
			array_push($column_warnings, "SEX");
		if ($col_idx_BIRTHDATE === false)
			array_push($column_warnings, "BIRTHDATE");
		if ($col_idx_ETHNIC_CD === false)
			array_push($column_warnings, "ETHNIC_CD");
		if ($col_idx_RACE === false)
			array_push($column_warnings, "RACE");
		if ($col_idx_FAM_HIRE_DT === false)
			array_push($column_warnings, "FAM_HIRE_DT");

		// If any columns are missing, display error message
		if (count($column_errors) > 0){
			echo '<span class="text-danger">';
			echo 'UPLOAD FAILURE!<br />';
			echo 'The CSV file is not formatted correctly. The following columns must be present:';
			echo '<ul>';
			foreach ($column_errors as $colName){
				echo '<li>' . $colName . '</li>';
			}
			echo '</ul>';
			
		} else{

			// Iterate through CSV file row by row
			// Read information from each column for this row
			// If a column is not present, add warning and then skip it
			for ($rowNum = 1; $rowNum < count($csv); $rowNum++) {

				$param_str_EffDt = $conn->escape_string(trim($csv[$rowNum][$col_idx_EFFDT]));
				
				// Test to see if row is empty
				// If EffDt is empty, it implies that the row is empty
				if ($param_str_EffDt == '') {
					// continue to next row
					continue;
				}

				$param_str_EffDt = date('Y-m-d', strtotime($param_str_EffDt));
				$param_int_EmplID = trim($csv[$rowNum][$col_idx_EMPLID]);
				
				if ($col_idx_EMPL_RCD !== false)
					$param_int_EmplRcd = trim($csv[$rowNum][$col_idx_EMPL_RCD]);

				if ($col_idx_NAME !== false)
					$param_str_Name = $conn->escape_string(trim($csv[$rowNum][$col_idx_NAME]));
				
				if ($col_idx_PAYGROUP !== false)
					$param_str_PayGroup = $conn->escape_string(trim($csv[$rowNum][$col_idx_PAYGROUP]));

				if ($col_idx_SAL_ADMIN_PLAN !== false)
					$param_str_SalAdminPlan = $conn->escape_string(trim($csv[$rowNum][$col_idx_SAL_ADMIN_PLAN]));

				if ($col_idx_GRADE !== false)
					$param_str_Grade = $conn->escape_string(trim($csv[$rowNum][$col_idx_GRADE]));
				
				if ($col_idx_POSITION_NBR !== false)
					$param_int_PosNum = trim($csv[$rowNum][$col_idx_POSITION_NBR]);
				
				if ($col_idx_POSITION_ENTRY_DT !== false){
					$param_str_PosEntryDate = $conn->escape_string(trim($csv[$rowNum][$col_idx_POSITION_ENTRY_DT]));
					$param_str_PosEntryDate = date('Y-m-d', strtotime($param_str_PosEntryDate));
				}

				$param_int_FundingDeptID = trim($csv[$rowNum][$col_idx_DEPTID]);
				$param_str_FundingDept = $conn->escape_string($csv[$rowNum][$col_idx_FUNDING_DEPT]);
				$param_int_DeptID = trim($csv[$rowNum][$col_idx_FAM_CERT_DEPT]);
				$param_str_WorkingDept = $conn->escape_string(trim($csv[$rowNum][$col_idx_WORKING_DEPT]));
				$param_str_JobFamily = $conn->escape_string(trim($csv[$rowNum][$col_idx_JOB_FAMILY]));
				$param_str_JobCode = $conn->escape_string(trim(str_pad($csv[$rowNum][$col_idx_JOBCODE], 4, '0', STR_PAD_LEFT)));
				$param_str_JobTitle = $conn->escape_string(trim($csv[$rowNum][$col_idx_JOB_TITLE]));
				
				if ($col_idx_UNION_CD !== false)
					$param_str_UnionCode = $conn->escape_string(trim($csv[$rowNum][$col_idx_UNION_CD]));
				
				if ($col_idx_ANNUAL_RT !== false)
					$param_double_Annual_Rt = trim($csv[$rowNum][$col_idx_ANNUAL_RT]);

				if ($col_idx_BIWKLY_RT !== false)
					$param_double_BiweeklyRate = trim($csv[$rowNum][$col_idx_BIWKLY_RT]);

				if ($col_idx_HOURLY_RT !== false)
					$param_double_HourlyRate = trim($csv[$rowNum][$col_idx_HOURLY_RT]);

				$param_double_FTE = trim($csv[$rowNum][$col_idx_FTE]);

				// Annualize salary if FTE < 1.0
				if ($param_double_FTE < 1 && $param_double_FTE > 0) {
					$param_double_Annual_Rt = $param_double_Annual_Rt / $param_double_FTE;
					$param_double_FTE = 1; // Set FTE to 1.0 since salary is annualized
				}
				
				if ($col_idx_SEX !== FALSE)
					$param_str_Sex = $conn->escape_string(trim($csv[$rowNum][$col_idx_SEX]));
				
				$param_str_TenureStatus = $conn->escape_string(trim($csv[$rowNum][$col_idx_TENURE_STATUS]));

				if ($col_idx_ETHNIC_CD !== false)
					$param_str_EthnicCD = $conn->escape_string(trim($csv[$rowNum][$col_idx_ETHNIC_CD]));

				if ($col_idx_RACE !== false)
					$param_str_Race = $conn->escape_string(trim($csv[$rowNum][$col_idx_RACE]));

				if ($col_idx_FAM_HIRE_DT !== false)
				$param_str_FAMU_HireDate = date('Y-m-d', strtotime(trim($csv[$rowNum][$col_idx_FAM_HIRE_DT])));

				if ($col_idx_BIRTHDATE !== false)
				$param_str_BirthDate = date('Y-m-d', strtotime(trim($csv[$rowNum][$col_idx_BIRTHDATE])));

				if ($col_idx_FAM_BUDGETED_WEEKS !== false)
					$param_double_BudgetedWeeks = $conn->escape_string(trim($csv[$rowNum][$col_idx_FAM_BUDGETED_WEEKS]));

				// Execute Query
				if (!$stmt->execute()) {
					
					// Duplicate primary key error
					if ($stmt->errno == 1062) {

						// Push duplicate EmplID onto array
						$duplicates_array[$rowNum + 1] = $param_int_EmplID;
					}
					else {
						echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error . '<br />';
					}
				}
				else {
					// Increment num of rows successfully inserted
					$numRows++;
				}
			} // End loop

			// Stop timer
			$timerStop = microtime(true);

			$stmt->close();

			// Insert History
			$insert_uploadHistory_sql = "
				INSERT INTO hrodt.tms_upload_history (UploadDate, NumUniqueEmps, FileName, UploaderID)
				VALUES (NOW(),?,?,?)
			";
			if (!$stmt = $conn->prepare($insert_uploadHistory_sql)) {
				echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error . '<br />';
			} else if (!$stmt->bind_param("isi", $numRows, $fileName, $_SESSION['user_id'])) {
				echo 'Binding parameters failed (' . $stmt->errno . ') ' . $stmt->error . '<br />';
			} else if (!$stmt->execute()) {
				echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error . '<br />';
			}

			$elapsedTime = ($timerStop - $timerStart); // seconds
		?>
			<div class="alert alert-success">
				<strong>Success!</strong> <?php echo $numRows;?> rows were inserted into database in <?php echo number_format($elapsedTime, 1);?> seconds
			</div>
			<br />

			
			<?php
				// If there are any warnings, display warning message
				if (count($column_warnings) > 0){
			?>
			<div class="alert alert-warning">
				<strong>Warning!</strong> The following expected columns were not present in the CSV:<br />
				<ul>
					<?php
						foreach ($column_warnings as $colName) {
							echo '<li>' . $colName . '</li>';
						}
					?>
				</ul>
			</div>
			<?php } ?>

			<?php
				/* Show duplicate entries if there are any */
				if (count($duplicates_array) > 0) {
			?>
			<br />
			<div class="alert alert-info">
				<strong>Info!</strong> There were <?php echo count($duplicates_array); ?> duplicate entries that were not inserted</strong><br />
				<br />

				<a id="viewDuplicates" href="#">View Duplicates <span class="caret"></span></a>

				<table
					id="duplicates"
					class="table table-condensed"
					style="display:none; width:auto;">

					<thead>
						<tr>	
							<th>Row in CSV File</th>
							<th>EmplID</th>
						</tr>
					</thead>
					<tbody>
				<?php
					foreach ($duplicates_array as $row => $emplID) {
						echo '<tr>';
							echo '<td>' . $row . '</td>';
							echo '<td>' . $emplID . '</td>';
						echo '</tr>';
					}
				?>
						<td></td>
						<td></td>
					</tbody>
				</table>

			<?php
					} // End show duplicates
			?>
			</div>
		
<?php	
			$maxNumFiles = 10; // Max number of uploaded files stored on server

			// select oldest file in TMS upload history table
			$sel_fileCount_sql = "
				SELECT COUNT(*) AS NumFiles
				FROM hrodt.tms_upload_history
			";

			if (!$stmt = $conn->prepare($sel_fileCount_sql)){
				echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error . '<br />';
			} else if (!$stmt->execute()){
				echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error . '<br />';
			}

			$stmt->store_result();
			$stmt->bind_result($numFiles);
			$stmt->fetch();

			// If max num files has been exceeded, delete the oldest file
			if ($numFiles > $maxNumFiles) {

				// Select oldest file in history table
				$sel_oldestFile_sql = "
					SELECT ID, FileName
					FROM hrodt.tms_upload_history
					ORDER BY UploadDate ASC
					LIMIT 1
				";
				if (!$stmt = $conn->prepare($sel_oldestFile_sql)){
					echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error . '<br />';
				} else if (!$stmt->execute()){
					echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error . '<br />';
				}
				$stmt->store_result();
				$stmt->bind_result($fileID, $fileName);
				$stmt->fetch();

				// Delete oldest file from history table
				$del_oldestFile_sql = "
					DELETE
					FROM hrodt.tms_upload_history
					WHERE ID = ?
				";
				if (!$stmt = $conn->prepare($del_oldestFile_sql)){
					echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error . '<br />';
				} else if (!$stmt->bind_param("i", $fileID)){
					echo 'Binding parameters failed (' . $stmt->errno . ') ' . $stmt->error . '<br />';
				} else if (!$stmt->execute()){
					echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error . '<br />';
				}

				// Delete oldest file from server directory
				if (!unlink($uploads_dir . $fileName)){
					echo '<div class="alert alert-danger">Error deleting the oldest uploaded file in the uploads directory.</div>';
				}
			} // End if max num uploaded files exceeded
		} // End if no columns are missing
	} // End insert CSV rows into table
}// End if FormData was posted to this page
?>

<script>
	// Make duplicates table appear when "View Duplicates" link is clicked
	$('#viewDuplicates').click(function() {
		$('#duplicates').slideToggle();
	});
</script>
