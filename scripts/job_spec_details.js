function confirmDeleteClassSpec() {
	var jobCode = $(this).attr('jobCode');

	$('<div></div>').appendTo('body')
		.html('<div>Are you sure you want to delete this Class Spec?<h6></div>')
		.dialog({
			modal: true,
			title: 'Delete Class Spec',
			zIndex: 10000,
			autoOpen: true,
			width: 'auto',
			resizable: 'false',
			buttons: {
				Yes: function() {
					yesDeleteClassSpec(jobCode);
					$(this).dialog('close');
				},
				No: function() {
					$(this).dialog('close');
				}
			}
		})
}

function yesDeleteClassSpec(jobCode) {
	/*
		Make AJAX request to delete the oldest class spec
		entry in the class_specs table with this job code.
	*/
		
	$.ajax({
		type: 'post',
		url: './content/job_spec_del.php',
		data: {
			'jobCode': jobCode
		},
		success: function(response) {
			// Redirect to Homepage
			window.location.replace('?page=homepage' + response);
		}
	});
}

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


	/*
		Attach event handler to delete class spec button
	*/
	$('#deleteClassSpec').on('click', confirmDeleteClassSpec);


	/*
		Attach event handler to "Go Back to Homepage" button
	*/
	$('#back-btn').click(function() {
		// Redirect to Homepage
		window.location.assign('?page=homepage&pp=' + $(this).attr('payPlan'));
	});
});
