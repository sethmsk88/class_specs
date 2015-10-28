<?php
	/******
		Function: quartiles
		Created by: Seth Kerr
		Date Created: 4/13/2015
		Parameter(s): $val_array = array of numbers,
					  $quartile = the quartile number (1-3)
		Returns: The value in the array at the position of Q1, Q2, or Q3
		Description: Return the value in an array of numbers that represents the
			beginning of the specified quartile.
		Updates:
			4/15/15: Sorted array ascending. No longer assuming that the array is
				in ascending order when passed in.
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
?>