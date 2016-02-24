$(document).ready(function() {
	/* Handle change password submit action */
	$('#changePassword-form').submit(function(e) {
		e.preventDefault();

		$form = $(this);

		// If password is valid and confirmed
		if (checkPassword()) {

			formhash($form);

			/* Submit the login form */
			$.ajax({
				type: 'post',
				url: './content/act_settings.php',
				data: $form.serialize(),
				success: function(response) {
					$('#ajaxResponse_changePassword').html(response);
				}
			});
		}
	});
});

/*

*/
function checkPassword() {
	var testsPassed = true;

	/*
		Check that the password is sufficiently long (min 6 chars)
		The check is duplicated below, but this is included to give
		more specific guidance to the user
	*/
	if ($('#newPassword').val().length < 6) {
		alert("Passwords must be at least 6 characters long. Please try again.");
		$('#newPassword').focus();
		testsPassed = false;
	}

	/*
		At least one number, one lowercase and one uppercase letter.
		At least 6 characters
	*/
	var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
	if (!re.test($('#newPassword').val())) {
		alert('Passwords must contain at least one number, one lowercase and one uppercase letter. Please try again.');
		testsPassed = false;
	}

	// Check password and confirmation are the same
	if ($('#newPassword').val() != $('#confirmPassword').val()) {
		alert('Your password and confirmation do not match. Please try again.');
		$('#confirmPassword').focus();
		testsPassed = false;
	}

	return testsPassed;
}

/*
	Hash the password before sending to server. This is
	necessary if there is no SSL.
*/
function formhash(form) {

	var oldPasswordHashed = hex_sha512($('#oldPassword').val());
	var newPasswordHashed = hex_sha512($('#newPassword').val());

	/* Append hidden input fields for hashed passwords to form */
	$("<input>")
		.attr("type", "hidden")
		.attr("id", "oldPwHashed")
		.attr("name", "oldPwHashed")
		.attr("value", oldPasswordHashed)
		.appendTo(form);

	$("<input>")
		.attr("type", "hidden")
		.attr("id", "newPwHashed")
		.attr("name", "newPwHashed")
		.attr("value", newPasswordHashed)
		.appendTo(form);

	// Clear the new password
	$('#newPassword').val('');
	$('#confirmPassword').val('');
}
