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

/*********************************
	When page finishes loading
**********************************/
$(document).ready(function(){

	// Activate Textillate
	var $animated = $('.animatedText').textillate({
		autoStart: false,
		loop:false,
		in:{
			effect: 'bounceInLeft',
			sync: true
		}
	});

	// Prepare overlay for modals
	$overlay = $('<div id="overlay"></div>');
	$overlay.hide();
	$('body').append($overlay);

	// Attach event handler to Submit button
	$('#addJobSpec-form').on('submit', function(e){
		e.preventDefault();

		var index = 0; // For textillate function call

		$.ajax({
			type: 'post',
			url: './content/job_spec_act.php',
			data: $('#addJobSpec-form').serialize(),
			success: function(response){
				$('#ajax_response_submit').html(response);
				$animated.textillate('in', index);
			}
		});
	});

	// Attach event handler to competency select box
	$('#competency_1').on('change', duplicateCompObj);


	/** TESTING manual textillate triggering
	$('#in-button').click(function(){
		$.ajax({
			url: './content/ajax_test.php',
			type: 'GET',
			success: function(response){
				$('#in-out').html("response: " + response + "<br />");
				$animated.textillate('in');
			}
		});

		// How do you animate text that comes from AJAX request
	});

	$('#out-button').click(function(){
		$animated.textillate('out');
	});
	**/


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
			url: './content/addNewComp_act.php',
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
			url: './content/addNewEEO_act.php',
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
			url: './content/addNewCBU_act.php',
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
});