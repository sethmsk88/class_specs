
$(document).ready(function() {

	// Prepare overlay for modals
	$overlay = $('<div id="overlay"></div>');
	$overlay.hide();
	$('body').append($overlay);

	// Attach event handler to Submit button
	$('#editJobSpec-form').on('submit', function(e){
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/job_spec_edit_act.php',
			data: $('#editJobSpec-form').serialize(),
			success: function(response){
				$('#ajax_response_submit').html(response);
			}
		});
	});

	// Attach event handler to competency select box
	$('#competency_1').on('change', duplicateCompObj);


	/*
		Attach event handler to the overlay.
		When it is clicked, close the modal that is currently open,
		and hide the overlay.
	*/
	$('#overlay').click(function(){

		// Select all divs with class modal that are currently visible
		$('.modalForm:visible').each(function(){

			// Hide modal
			$(this).slideUp(function(){
				// Hide overlay
				$('#overlay').hide();
			});
		});

	});

	/*
		Attach event handler to the JobCode input field.
		Query PayLevels table to see if there is a record for the specified
		Job Code.
		If there is a record, get the columns of information that
		appear as input fields on the Edit Page, and insert the information
		into their respective input fields.
	*/
	$('input#jobCode').on('change', function() {
		$.ajax({
			type: 'post',
			url: './content/selectJobCode_act.php',
			data: {
				'jobCode': $(this).val()
			},
			dataType: 'json', // data type for response
			success: function(response) {

				// If Job Code returned information from pay_levels table
				if (response !== null) {

					/*
						Populate fields with queried information
					*/
					$('input[id="jobTitle"]').val(response['JobTitle']);
					$('select[id="jobFamily"] option[value="' + response['JobFamilyID'] + '"]').prop('selected', true);
					$('select[id="payPlan"] option[value="' + response['PayPlan'] + '"]').prop('selected', true);
					$('select[id="payLevel"] option[value="' + response['PayLevel'] + '"]').prop('selected', true);
					$('input[id="ipedsCode"]').val(response['IPEDS_SOCs']);
					$('select[id="flsa"] option[value="' + response['FLSA'] + '"]').prop('selected', true);
				}
			}
		});
	});

	/*
		Attach event handlers to all competency delete buttons
	*/
	$('.del-comp').on('click', function() {

		var classSpecID = $('input[name="classSpecID"]').val();
		var competencyID = $(this).attr('id');

		// AJAX request to delete (competencyID, classSpecID) pair from table
		$.ajax({
			type: 'post',
			url: './content/delComp_act.php',
			data: {
				'classSpecID': classSpecID,
				'competencyID': competencyID
			},
			success: function(response) {
				
				// If successfully deleted
				if (response == 1) {
					
					/*
						Delete the closest parent <tr> of the button that has
						the id of the competency that was deleted.
					*/
					$('button.del-comp[id="' + competencyID + '"]').closest('tr').remove();
				}
			}
		});
	});

});