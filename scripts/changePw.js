$(function() {
	// submit event handler for when the change password form is submitted
	$('#changePassword-form').submit(function(e) {
		var form = $(this);

		var newPassword = form.find('#newPassword').val();
		var confirmPassword = form.find('#confirmPassword').val();
		
		var errors = [];

		// Password validation
		if (newPassword !== confirmPassword) {
			errors.push("New Password and Confirm Password must match");
		}
		if (newPassword.length < 8) {
			errors.push("Password must be at least 8 characters in length");
		}

		// If any errors exist, display error messages and stop form submission
		if (errors.length > 0) {
			var alertMsg = "";
			errors.forEach(function(error, idx) {
				alertMsg += error;
				if (idx < errors.length - 1) alertMsg += "\n";
			});
			alert(alertMsg);
			
			e.preventDefault(); // stop form submission
			return;
		}

		// Append hashed password to form
		$("<input />").attr({
			'name' : 'hashedNewPassword',
			'type' : 'hidden',
			'value' : hex_sha512(newPassword)
		}).appendTo(form);

		// Clear the newPassword and confirmPassword so we don't send cleartext passwords
		form.find('#newPassword').val('');
		form.find('#confirmPassword').val('');
	});
});
