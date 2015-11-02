<?php
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
		return $convertedFLSA;
	}
?>