<?php
/**
 * Get the string Yes/No represenation of a boolean value
 *
 * @param val  Boolean value (or integers 0 or 1)
 * @return  String "Yes" or "No"
 */
function convertYesNo($val) {
	if ($val == 0)
		return "No";
	else if ($val == 1)
		return "Yes";
	else
		return "";
}

/******
	Function: convertPayPlan
	Created by: Seth Kerr
	Date Created: 11/2/2015
	Parameter(s): $payPlan = string representing the pay plan
				  $format = format to which the user would like to
				  	convert the Pay Plan
	Returns: The converted Pay Plan string
	Description: Return a converted form of the Pay Plan that was passed
		into the function.
	Updates:
******/
function convertPayPlan($payPlan, $format) {

	$convertedPayPlan = ''; // Return value

	if ($format == 'class_specs') {
		switch ($payPlan) {
			case 'USPS':
				$convertedPayPlan = 'usps';
				break;
			case 'A&P':
				$convertedPayPlan = 'ap';
				break;
			case 'Faculty':
				$convertedPayPlan = 'fac';
				break;
			case 'EXC':
				$convertedPayPlan = 'exec';
				break;
		}
	}
	else if ($format == 'pay_levels') {
		switch ($payPlan) {
			case 'usps':
				$convertedPayPlan = 'USPS';
				break;
			case 'ap':
				$convertedPayPlan = 'A&P';
				break;
			case 'exec':
				$convertedPayPlan = 'EXC';
				break;
			case 'fac':
				$convertedPayPlan = 'Faculty';
				break;
		}
	}
	else if ($format == 'long') {
		switch ($payPlan) {
			case 'usps':
				$convertedPayPlan = 'USPS';
				break;
			case 'ap':
				$convertedPayPlan = 'A&amp;P';
				break;
			case 'exec':
				$convertedPayPlan = 'Executive';
				break;
			case 'fac':
				$convertedPayPlan = 'Faculty';
				break;
		}
	}
	return $convertedPayPlan;
}


/******
	Function: convertFLSA
	Created by: Seth Kerr
	Date Created: 11/2/2015
	Parameter(s): $flsa = string or int representing the pay plan
				  $format = format to which the user would like to
				  	convert the FLSA value
	Returns: The converted form of the FLSA value
	Description: Convert the FLSA value into the format specified by the
		format parameter.
	Updates:
******/
function convertFLSA($flsa, $format) {

	$convertedFLSA = ''; // Return value

	if ($format == 'numeric') {
		switch ($flsa) {
			case 'N':
			case 'NE':
				$convertedFLSA = 0;
				break;
			case 'X':
			case 'E':
				$convertedFLSA = 1;
				break;
			case '1X N':
			case 'both':
				$convertedFLSA = 2;
				break;
		}
	}
	else if ($format == 'symbolic') {
		switch ($flsa) {
			case 0:
				$convertedFLSA = 'N';
				break;
			case 1:
				$convertedFLSA = 'X';
				break;
			case 2:
				$convertedFLSA = 'both';
				break;
		}
	}
	else if ($format == 'string') {
		switch ($flsa) {
			case 0:
				$convertedFLSA = 'Non-Exempt';
				break;
			case 1:
				$convertedFLSA = 'Exempt';
				break;
			case 2:
				$convertedFLSA = 'Both';
				break;
		}
	}
	return $convertedFLSA;
}

function getFLSA(&$conn, $jobCode, $payPlan, $flsa_status) {
	// If pay plan is A&P, do the calculations below, otherwise just return the FLSA status
	if ($payPlan == "ap") {

		// select the most recent threshold
		$sel_threshold_sql = "
			SELECT threshold
			FROM hrodt.flsa_threshold
			ORDER BY dateUpdated DESC
			LIMIT 1
		";
		if (!$stmt = $conn->prepare($sel_threshold_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		} else if (!$stmt->execute()) {
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		$stmt->bind_result($threshold);
		$stmt->fetch();
		$stmt->close();

		// select all salaries for employees in this position
		$sel_salaries_sql = "
			SELECT Annual_Rt
			FROM hrodt.all_active_fac_staff
			WHERE JobCode = ?
		";
		if (!$stmt = $conn->prepare($sel_salaries_sql)) {
			echo 'Prepare failed: (' . $conn->errno . ') ' . $conn->error;
		} else if (!$stmt->bind_param('s', $jobCode)) {
			echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;	
		} else if (!$stmt->execute()) {
			echo 'Execute failed: (' . $stmt->errno . ') ' . $stmt->error;
		}
		$stmt->bind_result($salary);

		$flsa_exempt = 0;
		$flsa_nonexempt = 0;
		while ($stmt->fetch()) {
			if ($salary < $threshold)
				$flsa_nonexempt++;
			else
				$flsa_exempt++;
		}

		// Create FLSA status string
		$new_flsa_status = "";
		if ($flsa_exempt > 0 && $flsa_nonexempt > 0) {
			$new_flsa_status = "Exempt (" . $flsa_exempt . ") / " .
				"Non-Exempt (" . $flsa_nonexempt . ")";
		} else {
			$new_flsa_status = convertFLSA($flsa_status, 'string');
		}

		return $new_flsa_status;
	} else {
		// Return FLSA status
 		return convertFLSA($flsa_status, 'string');
	}
}

function esc_url($url) {
	if ('' == $url) {
		return $url;
	}

	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = (string) $url;

	$count = 1;
	while ($count) {
		$url = str_replace($strip, '', $url, $count);
	}

	$url = str_replace(';//', '://', $url);
	$url = htmlentities($url);
	$url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
    	// We're only interested in relative links from $_SERVER['PHP_SELF']
    	return '';
    }
    else {
    	return $url;
    }
}

/**
 *	Convert a string representation of money into a float
 *	representation. Remove all characters except decimals
 *	and integers.
 *	
 *	@param money 	String representation of money
 *	@return Float representation of money
 */
function parseMoney($money) {
	return preg_replace("/[^0-9.]/", "", $money);
}

// Increment the number on the end of the filename
function increment_fileNumber($fileName)
{		
	// Split string by '.'
	$fileName_exploded = explode('.', $fileName);

	// Pop the file extension off the end
	$extension = array_pop($fileName_exploded);

	// Join array into string using '.' as a separator
	$fileName = implode('.', $fileName_exploded);

	// Split the string by '_'
	$fileName_exploded = explode('_', $fileName);

	// Remove the number that was previously appended to the filename
	$prevNumber = array_pop($fileName_exploded);

	// Add a new number to the filename
	array_push($fileName_exploded, ++$prevNumber);

	// Join array into string using '_' as a separator
	$fileName = implode('_', $fileName_exploded) . '.' . $extension;

	return $fileName;
}

// Return a filename that does not already exist in the uploads directory
function make_unique_filename($fileName, $uploadsDir)
{
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/bootstrap/apps/class_specs/" . $uploadsDir . $fileName)) {
		$fileName_exploded = explode('.', $fileName);
		$extension = array_pop($fileName_exploded);
		$fileName_exploded[0] .= '_1'; // Append number to filename
		array_push($fileName_exploded, $extension);
		$fileName = implode('.', $fileName_exploded);

		// Make sure filename is unique
		while (file_exists($_SERVER['DOCUMENT_ROOT'] . "/bootstrap/apps/class_specs/" . $uploadsDir . $fileName)) {
			$fileName = increment_fileNumber($fileName);
		}
	}
	return $fileName;
}

?>
