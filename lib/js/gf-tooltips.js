jQuery(document).ready(function($) {

//***************************************************************
// get relative location of JS file for icon load
//***************************************************************

	var tooltip_src		= $('meta[name="tooltip-base"]').attr('content');
	var tooltip_icon	= '<img src="' + tooltip_src + 'lib/img/tooltip-icon.png">';

//***************************************************************
// auto find description field and load as tooltip in label
//***************************************************************

	$('div.gform_body li.gf-tooltip-label').each(function() {
		// get description text
		var desc_text = $(this).find('div.gfield_description').html();
		// get positioning
		var position  = $('div.gform_wrapper form').attr('class');
		// add tooltip attribute into label
		$(this).find('label.gfield_label').attr('tooltip', desc_text).addClass('gf-tooltip-load');
		$(this).find('label.gfield_label').attr('data-position', position);
	});

//***************************************************************
// manual find description field and load as tooltip in label
//***************************************************************

	$('div.gform_body li.gf-tooltip-label-manual').each(function() {
		// check for user added class
		if ($(this).hasClass('gf-tooltip-manual') ){
			// get description text
			var desc_text = $(this).find('div.gfield_description').html();
			// get positioning
			var position  = $('div.gform_wrapper form').attr('class');
			// add tooltip attribute into label
			$(this).find('label.gfield_label').attr('tooltip', desc_text).addClass('gf-tooltip-load');
			$(this).find('label.gfield_label').attr('data-position', position);
		}
	});

//***************************************************************
// auto find description field and load as tooltip in icon
//***************************************************************

	$('div.gform_body li.gf-tooltip-icon').each(function() {
		// get description text
		var desc_text = $(this).find('div.gfield_description').html();
		// get positioning
		var position  = $('div.gform_wrapper form').attr('class');
		// add tooltip attribute into label
//		$(this).find('label.gfield_label').append('<span class="gf-tooltip-load">(?)</span>');
		$(this).find('label.gfield_label').append('<span class="gf-tooltip-load">' + tooltip_icon + '</span>');

		$(this).find('span.gf-tooltip-load').attr('tooltip', desc_text);
		$(this).find('span.gf-tooltip-load').attr('data-position', position);
	});

//***************************************************************
// manual find description field and load as tooltip in icon
//***************************************************************

	$('div.gform_body li.gf-tooltip-icon-manual').each(function() {
		// check for user added class
		if ($(this).hasClass('gf-tooltip-manual') ){
			// get description text
			var desc_text = $(this).find('div.gfield_description').html();
			// get positioning
			var position  = $('div.gform_wrapper form').attr('class');
			// add tooltip attribute into label
//			$(this).find('label.gfield_label').append('<span class="gf-tooltip-load">(?)</span>');
			$(this).find('label.gfield_label').append('<span class="gf-tooltip-load">' + tooltip_icon + '</span>');
			$(this).find('span.gf-tooltip-load').attr('tooltip', desc_text);
			$(this).find('span.gf-tooltip-load').attr('data-position', position);
		}
	});

//***************************************************************
// auto hide description field
//***************************************************************

	$('div.gform_body li.gf-desc-hide-auto').each(function() {
		$(this).find('div.gfield_description').hide();
	});

//***************************************************************
// manual hide description field
//***************************************************************

	$('div.gform_body li.gf-desc-hide-manual').each(function() {
		// check for user added class
		if ($(this).hasClass('gf-tooltip-manual') ) {
			$(this).find('div.gfield_description').hide();
		}
	});


//***************************************************************
// load and apply tooltips
//***************************************************************

	$('div.gform_body .gf-tooltip-load').each(function() {
		gf_apply_tooltip(this);
	});

	function gf_apply_tooltip(element){
		//todo: add options for positioning http://craigsworks.com/projects/qtip/docs/tutorials/#position
		var position  = jQuery(element).attr('data-position');

		jQuery(element).qtip({
			content: jQuery(element).attr('tooltip'), // Use the tooltip attribute of the element for the content
			show: {
				delay: 700,
				solo: true
			},
			hide: {
				when: 'mouseout',
				fixed: true,
				delay: 200,
				effect: 'fade'
			},
			position: {
				corner: {
					target: 'topRight',
					tooltip: 'bottomLeft'
				}
			},
			style: {
				width:	300,
				padding: 10,
				color:	'black',
				tip:	'bottomLeft',
				border: {
					width:	4,
					radius:	5,
					color:	'#666'
				},
				name:	'light'
			}
		});
	}

//***************************************************************
// You're still here? It's over. Go home.
//***************************************************************


});	// end init
