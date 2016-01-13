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
?>
