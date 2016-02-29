/*
	Prepare modals and overlay
*/
$(document).ready(function() {

	/* Attach onClick handler to Login button */
	/*$('#login-link').click(function() {
		// show overlay
		$('#overlay').show();

		$modal = $('#login-container');

		// Set position of modal to be the center of the screen
		var top = $(this).offset().top;
		var left = Math.max($(window).width() - $modal.outerWidth(), 0) / 2;
		$modal.css({
			"top": top,
			"left":  left
		});

		// Show the new form using the slideDown function
		$modal.slideDown();
	});*/


	/* Prepare overlay for modals */
	/*$overlay = $('<div id="overlay"></div>');
	$overlay.hide();
	$('body').append($overlay);*/


	/*
		Attach event handler to the overlay.
		When it is clicked, close the modal that is currently open,
		and hide the overlay.
	*/
	/*$('#overlay').click(function(){

		// Select all divs with class modal that are currently visible
		$('.modalForm:visible').each(function(){

			// Clear all inputs in modal
			$('input[type="text"]').val('');
			$('input[type="password"]').val('');

			// Hide modal
			$(this).slideUp(function(){
				// Hide overlay
				$('#overlay').hide();
			});
		});
	});*/
});
