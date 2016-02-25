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
		url: './content/act_jobSpec_del.php',
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

	/* Event handler for the editJobSpec-form submit action */
	$('#editJobSpec-form').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_jobSpec_edit.php',
			data: $('#editJobSpec-form').serialize(),
			success: function(response) {
				$('#ajax_response_submit').html(response);
			}
		});
	});

	// Attach event handler to competency select box
	$('#competency_1').on('change', duplicateCompObj);

	/* Attach event handlers to all competency delete buttons */
	$('.del-comp').on('click', function() {

		var classSpecID = $('input[name="classSpecID"]').val();
		var competencyID = $(this).attr('id');

		// AJAX request to delete (competencyID, classSpecID) pair from table
		$.ajax({
			type: 'post',
			url: './content/act_comp_del.php',
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


	/* Attach event handlers to all competency edit buttons */
	$('.edit-comp').on('click', function() {
		// Show overlay
		$('#overlay').show();

		$modal = $('#editComp-container');

		// Set width of modal
		$modal.width($('#competenciesTable').width());

		// Set position of modal to be the center of the screen
		var top = $(this).offset().top;
		var left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;
		$modal.css({
			"top": top,
			"left":  left
		});

		// Fill the text area with the text of the comp being edited
		var compText = $(this).parent().prev().text();
		$('#updatedComp').text(compText);

		// Store compID in modal form's hidden input field
		$('#_compID').val($(this).attr('id'));

		// Show the new form using the slideDown function
		$modal.slideDown();	
	});


	/*
		Attach event handler to "Submit Changes" button in
		"Edit Competencies" modal
	*/
	$('#editComp-form').on('submit', function(e) {
		e.preventDefault();

		// AJAX request to update table entry
		$.ajax({
			type: 'post',
			url: './content/act_comp_edit.php',
			data: $('#editComp-form').serialize(),
			success: function(response) {			
				
				var updatedCompID = $('#_compID').val();

				// Update the text of the competency that was just updated
				$('button.edit-comp[id="' + updatedCompID + '"]').parent().prev().text(response);

				// Clear all inputs in editCompForm
				$('#editCompForm input[type="hidden"]').each(function() {
					$(this).val('');
				});
				$('#editCompForm input[type="text"]').each(function() {
					$(this).val('');
				});
				$('#editCompForm textarea').each(function() {
					$(this).text('');
				});

				// Hide form
				$('#editComp-container').slideUp();

				// Hide overlay
				$('#overlay').hide();
				
			}
		});

	});


	/* Attach event handler to delete class spec button */
	$('#deleteClassSpec').on('click', confirmDeleteClassSpec);


	/* Attach event handler to "Go Back to Homepage" button */
	$('#back-btn').click(function() {
		// Redirect to Homepage
		window.location.assign('?page=homepage&pp=' + $(this).attr('payPlan'));
	});
});
