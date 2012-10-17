jQuery(document).ready(function($) {

//***************************************************************
// display note about manual class
//***************************************************************

	jQuery('input.gf-tooltip-display').each(function(index, element) {
		// check selection
		var check = $('input.gf-tooltip-display:checked').val();

		if (check == 'manual')
			$('p.gf-manual-desc').show();

		if (check !== 'manual')
			$('p.gf-manual-desc').hide('slow');
	
	});

	$('input.gf-tooltip-display').change( function() {
		// check selection
		var check = $('input.gf-tooltip-display:checked').val();

		if (check == 'manual')
			$('p.gf-manual-desc').show('slow');

		if (check !== 'manual')
			$('p.gf-manual-desc').hide('slow');
	
	});


//***************************************************************
// You're still here? It's over. Go home.
//***************************************************************
	

});	// end init
