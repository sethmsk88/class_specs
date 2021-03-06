/**
	Update options in last competencies select box
**/
function updateCompetencies(compID, compDescr) {

	// Select last select box in competencies section
	$lastCompSelectBox = $('#competencies-container select:last');

	// Append new option to lastCompSelectBox
	$lastCompSelectBox.append(
		'<option value="' + compID + '">' + compDescr + '</option>'
	);

	// Select new option in lastCompSelectBox
	$lastCompSelectBox.val(compID);
}


/*
	Query PayLevels tabl to see if there is a record for the specified Job Code.
	If there is a record, get the columns of information that
	appear as input fields on the Edit Page, and insert the information
	into their respective input fields.
*/
function getPayLevelInfo(jobCode) {
	$.ajax({
		type: 'post',
		url: './content/act_selectJobCode.php',
		data: {
			'jobCode': jobCode
		},
		dataType: 'json', // data type for response
		success: function(response) {

			// If Job Code returned information from pay_levels table
			if (response !== null) {
				/*
					Populate fields with queried information
				*/
				$('input[id="jobCode"]').val(jobCode);
				$('input[id="jobTitle"]').val(response['JobTitle']);
				$('select[id="jobFamily"] option[text="' + response['JobFamily'] + '"]').prop('selected', true);
				$('select[id="payPlan"] option[value="' + response['PayPlan'] + '"]').prop('selected', true);
				$('select[id="payLevel"] option[value="' + response['PayLevel'] + '"]').prop('selected', true);
				$('input[id="ipedsCode"]').val(response['IPEDS_SOCs']);
				$('select[id="flsa"] option[value="' + response['FLSA'] + '"]').prop('selected', true);
			}
		}
	});
}


/*********************************
	When page finishes loading
**********************************/
$(document).ready(function() {

	// Activate Textillate
	/* TESTING: 
	var $animated = $('.animatedText').textillate({
		autoStart: false,
		loop:false,
		in:{
			effect: 'bounceInLeft',
			sync: true
		}
	});*/

	// Prepare overlay for modals
	$overlay = $('<div id="overlay"></div>');
	$overlay.hide();
	$('body').append($overlay);

	// Attach event handler to Submit button
	$('#addJobSpec-form').on('submit', function(e){
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_jobSpec.php',
			data: $('#addJobSpec-form').serialize(),
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
		Attach event handler to addNewComp button.
		When button is clicked hide button and show form.
	*/
	$('#addNewComp-button').click(function(){

		// Show overlay
		$('#overlay').show();

		$modal = $('#addNewComp-container');

		// Set width of modal
		$modal.width($('#competencies-container').width());

		// Set position of modal to be the center of the screen
		var top = $(this).offset().top;
		var left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;
		$modal.css({
			"top": top,
			"left":  left
		});

		// Show the new form using the slideDown function
		$modal.slideDown();	
	});


	/*
		Handle AJAX submission of form
	*/
	$('#addNewComp-form').on('submit', function(e){
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_addNewComp.php',
			data: $('#addNewComp-form').serialize(),
			success: function(response){

				// Fill response div with AJAX response
				$('#ajax_response_addNewComp').html(response);

				// Reset text inputs in addNewComp-form
				$('#addNewComp-form :text').each(function() {
					$(this).val('');
				});

				// Hide form
				$('#addNewComp-container').slideUp();

				// Hide overlay
				$('#overlay').hide();

				// Create new blank select box
				duplicateCompObj();
			}
		});
	});

	/*
		Attach event handler to clearCompList button.
		OnClick, destroy all competency select boxes except the first one.
	*/
	$('#clearCompList-button').click(function(){

		/*
			Delete all objects with IDs that start with "competency_",
			except the first one.
		*/
		$('select[id^=competency_').each(function(){
			if ($(this).attr('id') != "competency_1"){
				$(this).remove();
			}
			else{
				// Clear first select box
				$(this).val('');

				// Set focus on first select box
				$(this).focus();

				// Set handler on first select box
				$(this).on('change', duplicateCompObj);
			}
		});
	});


	/*
		Attach event handler to addNewEEO button.
		When button is clicked hide button and show form.
	*/
	$('#addNewEEO-button').click(function(){

		// Show overlay
		$('#overlay').show();

		$modal = $('#addNewEEO-container');

		// Set width of modal
		$modal.width($('#eeoCode-container').width());

		// Set position of modal to be the center of the screen
		var top = $(this).offset().top;
		var left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;
		$modal.css({
			"top": top,
			"left":  left
		});

		// Show the new form using the slideDown function
		$modal.slideDown();	
	});

	/*
		Handle AJAX submission of form
	*/
	$('#addNewEEO-form').on('submit', function(e){
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_addNewEEO.php',
			data: $('#addNewEEO-form').serialize(),
			success: function(response){

				// Fill response div with AJAX response
				$('#ajax_response_addNewEEO').html(response);

				// Reset text inputs in addNewCBU-form
				$('#addNewEEO-form :text').each(function() {
					$(this).val('');
				});

				// Hide form
				$('#addNewEEO-container').slideUp();

				// Hide overlay
				$('#overlay').hide();
			}
		});
	});


	/*
		Attach event handler to addNewCBU button.
		When button is clicked hide button and show form.
	*/
	$('#addNewCBU-button').click(function(){

		// Show overlay
		$('#overlay').show();

		$modal = $('#addNewCBU-container');

		// Set width of modal
		$modal.width($('#cbuCode-container').width());

		// Set position of modal to be the center of the screen
		var top = $(this).offset().top;
		var left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;
		$modal.css({
			"top": top,
			"left":  left
		});

		// Show the new form using the slideDown function
		$modal.slideDown();	
	});

	/*
		Handle AJAX submission of form
	*/
	$('#addNewCBU-form').on('submit', function(e){
		e.preventDefault();

		$.ajax({
			type: 'post',
			url: './content/act_addNewCBU.php',
			data: $('#addNewCBU-form').serialize(),
			success: function(response){

				// Fill response div with AJAX response
				$('#ajax_response_addNewCBU').html(response);

				// Reset text inputs in addNewCBU-form
				$('#addNewCBU-form :text').each(function() {
					$(this).val('');
				});

				// Hide form
				$('#addNewCBU-container').slideUp();

				// Hide overlay
				$('#overlay').hide();
			}
		});
	});

	/*
		Attach event handler to JobCode input
	*/
	$('#jobCode').on('change', function() {
		var jobCode = $(this).val();

		$.ajax({
			type: 'post',
			url: './content/act_selectJobCode.php',
			data: {
				'jobCode': jobCode
			},
			dataType: 'json', // Data type for response
			success: function(response) {
				// If Job Code returned information from class_specs table
				if (response !== null) {
					alert('Job Code (' + jobCode + ') already exists in the table!');
				}
			}
		});
	});

	/*
		Attach event handler to "Go Back to Homepage" button
	*/
	$('#back-btn').click(function() {
		// Redirect to Homepage
		window.location.assign('?page=homepage&pp=' + $(this).attr('payPlan'));
	});

	// Bind department input fields together
	$('select.department').change(function() {
		var deptIdSelected = $(this).children(':selected').attr('dept-id');
		var deptLetter = $(this).children(':selected').attr('dept-letter');
		$('#deptName option[dept-id="'+ deptIdSelected +'"]').prop("selected", true);
		$('#deptId option[dept-id="'+ deptIdSelected +'"]').prop("selected", true);
		$('#deptLetter').val(deptLetter);
	});

	// Change event handler for "Assign Department to Classification Code" checkbox
	$('#assignDept').change(function() {
		// Show/hide Department designation fields
		if ($(this).prop('checked')) {
			$('#deptInputs').toggleClass('hidden');
		} else {
			$('#deptInputs').toggleClass('hidden');
		}
	});
});
