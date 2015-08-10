jQuery(document).ready(function($) {	
	/**
	* Top level tabbed interface. If defined in the view:
	* - tabs are set to display, as JS is enabled
	* - the selected tab's panel is displayed, with all others hidden
	* - clicking a tab will switch which panel is displayed
	*/
	if ($('h2.nav-tab-wrapper.needs-js').length > 0) {
		// Show tabbed bar
		$('h2.nav-tab-wrapper.needs-js').fadeIn('fast', function() {
			$(this).removeClass('needs-js');
		});
		
		// Hide all panels except the active one
		$('#normal-sortables div.panel').hide();
		var activeTab = $('h2.nav-tab-wrapper a.nav-tab-active').attr('href')+'-panel';
		$(activeTab).show();
		
		// Change active panel on tab change
		$('h2.nav-tab-wrapper a').click(function(e) {
			e.preventDefault();
			
			// Deactivate all tabs, hide all panels
			$('h2.nav-tab-wrapper a').removeClass('nav-tab-active');
			$('#normal-sortables div.panel').hide();
			
			// Set clicked tab to active, show panel
			$(this).addClass('nav-tab-active');
			var activeTab = $(this).attr('href')+'-panel';
			$(activeTab).show();
		});
	}
	
	/**
	* Second level tabbed interface. If defined in the view:
	* - tabs are set to display, as JS is enabled
	* - the selected tab's panel is displayed, with all others hidden
	* - clicking a tab will switch which panel is displayed
	*/
	if ($('h3.nav-tab-wrapper.needs-js').length > 0) {
		// Iterate through each sub tab bar
		$('h3.nav-tab-wrapper.needs-js').each(function() {
			// Show tabbed bar
			$(this).fadeIn('fast', function() {
				$(this).removeClass('needs-js');
			});
			
			// Hide all sub panels except the active one
			$('div.sub-panel', $(this).parent()).hide();
			var activeTab = $('a.nav-tab-active', $(this)).attr('href')+'-panel';
			$(activeTab).show();
			
			// Change active panel on tab change
			$('a', $(this)).click(function(e) {
				e.preventDefault();
				
				// Deactivate all tabs, hide all panels
				$('a', $(this).parent()).removeClass('nav-tab-active');
				$('div.sub-panel', $(this).parent().parent()).hide();
				
				// Set clicked tab to active, show panel
				$(this).addClass('nav-tab-active');
				var activeTab = $(this).attr('href')+'-panel';
				$(activeTab).show();
			});
		});
	}

	/**
	* Conditional Fields
	*/
	$('input,select').conditional();
});

/**
 * jQuery Conditionals 1.0.0
 *
 * Copyright 2015 n7 Studios
 * Released under the MIT license.
 * http://jquery.org/license
 */
(function( $ ) {
	"use strict";

	/**
	* Create .conditional() function
	*
	* @param object options Override Default Settings
	*/
	$.fn.conditional = function(options) {
		// Default Settings
		var settings = $.extend({
			data: 'conditional',
		}, options);

		// Setup conditionals on each DOM element
		this.each(function() {
			// Check for conditional elements
			if ( typeof $( this ).data( settings.data ) === 'undefined' ) {
				return true;
			}

			// Setup vars
			var conditionalElements,
				displayOnEnabled,
				displayElements;
			
			// Toggle + toggle on change
			$( this ).on( 'change', function() {
				// List the DOM elements to toggle
				conditionalElements = $( this ).data( settings.data ).split(',');
				
				// Determine whether to display DOM elements when the input is 'enabled'
				displayOnEnabled = $( this ).data( 'condition-display' );
				if ( typeof displayOnEnabled === 'undefined' ) {
					displayOnEnabled = true;
				}

				// By default, don't display elements
				displayElements = false;

				// Determine whether to display relational elements or not
				switch ( $( this ).attr( 'type' ) ) {
					case 'checkbox':
						if ( displayOnEnabled ) {
							displayElements = $( this ).is( ':checked' );
						} else {
							displayElements = ( $( this ).is( ':checked' ) ? false : true );
						}	
						break;
					
					default:
						if ( displayOnEnabled ) {
							displayElements = ( ( $( this ).val() === '' || $( this ).val() === '0' ) ? false : true );
						} else {
							displayElements = ( ( $( this ).val() === '' || $( this ).val() === '0' ) ? true : false );
						}
						break;
				}



				// Show/hide elements
				for (var i = 0; i < conditionalElements.length; i++) {
			    	if ( displayElements ) {
				    	$( '#' + conditionalElements[i] ).fadeIn( 300 );
				    } else {
					    $( '#' + conditionalElements[i] ).fadeOut( 300 );
				    }
				}
			});

			// Trigger a change on init so we run the above routine
			$ ( this).trigger( 'change' );
		});

		// Return DOM elements 
		return this;
   	};

})(jQuery);