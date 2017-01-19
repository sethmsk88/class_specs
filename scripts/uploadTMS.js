/*
	When file is selected in file input box, trigger fileselect
	event handler.
*/
$(document).on('change', '.btn-file :file', function(){
	var input = $(this);
	var numFiles = input.get(0).files ? input.get(0).files.length : 1;
	var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function(){
	/*
		When a file is selected in a file input box, fill
		input box with filename.
		If more than one file is selected, fill input box with
		the number of the files that were selected.
	*/
	$('.btn-file :file').on('fileselect', function(event, numFiles, label){
		// Get the selected file text
		var input = $(this).parents('.input-group').find(':text');
		var log = numFiles > 1 ? numFiles + ' files selected' : label;

		if (input.length) {
			input.val(log);
		}
		else{
			if (log){
				alert(log);
			}
		}
	});

	/*
		Handle AJAX form submission
	*/
	$('#uploadFile-form').on('submit', function(e){
		e.preventDefault();

		// Display loading gif
		$('#ajax_uploadResponse').html('<img src="./img/loading_rounded_blocks.gif" alt="Loading..." class="loading-img">')

		var formData = new FormData($('#uploadFile-form')[0]);

		$.ajax({
			url: './content/act_uploadTMS.php',
			type: 'post',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				$('#ajax_uploadResponse').html(response);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				var errorMsg = 'Error Status: ' + textStatus + '<br />' +  '<code>' + errorThrown + '</code>';
				$('#ajax_uploadResponse').html(errorMsg);
			}
		});
	});
})
