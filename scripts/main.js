/*
	Get URL params
*/
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

/*** Duplicate competency object ***/
function duplicateCompObj() {

	// Get the last select box with an ID starting with competency_
	var $sel = $('select[id^="competency_"]:last');

	// Get the number from the last select box's ID and increment it
	var num = parseInt($sel.prop('id').match(/\d+/g), 10) + 1;

	// Clone the select box and assign the new ID to it
	var $newSel = $sel.clone(true).prop('id', 'competency_' + num);

	// Update name of select box with new ID number
	$newSel.prop('name', 'competency_' + num);

	// Add CSS property to select box
	$newSel.css('margin-bottom', 10);

	// Append new select box to container
	$newSel.appendTo('#competencies-container');

	// Focus on new select box
	$newSel.focus();

	// Add space below new select box
	//$('#competencies-container').append('<div style="line-height:15px;">&nbsp;</div>');

	// Remove event handler from previous competency select box
	$('#competency_' + (num - 1)).off('change');
}


/**
	Append new option to select box, and select it
	Params:
		id = Select box id
		val = New option value
		descr = The description for the new option
**/
function addOption(id, val, descr) {

	// Append new option to select box
	$('#' + id).append(
		'<option value="' + val + '">' + descr + '</option>'
	);

	// Select new option in select box
	$('#' + id).val(val);
}

$(document).ready(function(){

	// Activate data tables
	$('#classSpecs_ap').DataTable({});
	$('#classSpecs_usps').DataTable({});
	$('#classSpecs_exec').DataTable({});
	$('#classSpecs_fac').DataTable({});

	// Activate Textillate on deleted message
	var $animated = $('.deleted').textillate({
		autoStart: true,
		loop:false,
		in:{
			effect: 'bounceInLeft',
			sync: true
		}
	});

});