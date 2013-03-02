var $lm_post_jquery = jQuery.noConflict();

$lm_post_jquery(document).ready(function($) {

	//When page loads...
	$( '.leenkme_tab_content' ).hide(); //Hide all content
	$( 'ul.leenkme_tabs li:first' ).addClass('active').show(); //Activate first tab
	$( '.leenkme_tab_content:first' ).show(); //Show first tab content

	//On Click Event
	$( 'ul.leenkme_tabs li' ).click(function() {

		$( 'ul.leenkme_tabs li' ).removeClass('active'); //Remove any 'active' class
		$( this ).addClass('active'); //Add 'active' class to selected tab
		$( '.leenkme_tab_content' ).hide(); //Hide all tab content

		var activeTab = $( this ).find( 'a' ).attr( 'href' ); //Find the href attribute value to identify the active tab + content
		$( activeTab ).fadeIn(); //Fade in the active ID content
		return false;
		
	});
		
	$('a.releenk_row_action').live('click', function(e) {
		
		e.preventDefault();
		
		show_leenkme_options( $( this ).attr( 'id' ) );
		
	});
	
	$( 'a.inline-leenkme-cancel' ).live('click', function(e) {
		
		e.preventDefault();
		
		var post_id = $( this ).attr( 'post_id' );
		
		$( 'tr#post-' + post_id ).show();
		$( 'tr#inline-leenkme-' + post_id ).remove();
		
		
	});
	
	$( 'a.inline-leenkme-releenk' ).live('click', function(e) {
		
		e.preventDefault();
		
		var post_id = $( this ).attr( 'post_id' );
		
		networks = new Array();
		$( '.lm_releenk_networks_' + post_id ).each( function() {
			if ( true == $( this ).is(':checked') ) {
				networks[networks.length] = $( this ).val();
			}
		});
		
		var data = {
			'action': 		'releenk',
			'id':  			post_id,
			'networks':		networks,
			'_wpnonce': 	$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
		$( 'tr#post-' + post_id ).show();
		$( 'tr#inline-leenkme-' + post_id ).remove();
		
	});

	function show_leenkme_options( id ) {
			
		//console.log( id );
		
		leenkmeRow = '<tr id="inline-leenkme-' + id + '" class="inline-leenkme"></tr>';
			
		var data = {
			'action':	'leenkme_row_action',
			'id':		id,
			'colspan':	$('.widefat:first thead th:visible').length,
			'_wpnonce':	$( 'input#leenkme_wpnonce' ).val()
		};
			
		// We just need this to refresh the image being used.
		$lm_post_jquery.post( ajaxurl, data, function( response ) {
				
			$( 'tr#post-' + id ).hide().after( leenkmeRow );
		
			if ( $( 'tr#post-' + id ).hasClass('alternate') )
				$( 'tr#inline-leenkme-' + id ).addClass('alternate');
			
			$( 'tr#inline-leenkme-' + id ).append( response );
			
		});
		
	}

});

function ajax_response(data) {
	
	var style = "position: fixed; " +
				"display: none; " +
				"z-index: 1000; " +
				"top: 50%; " +
				"left: 50%; " +
				"background-color: #E8E8E8; " +
				"border: 1px solid #555; " +
				"padding: 15px; " +
				"width: 500px; " +
				"min-height: 80px; " +
				"margin-left: -250px; " + 
				"margin-top: -150px;" +
				"text-align: center;" +
				"vertical-align: middle;";
	jQuery('body').append("<div id='results' style='" + style + "'></div>");
	jQuery('#results').html("<p>Sending data to leenk.me</p>" +
							"<p><img src='/wp-includes/js/thickbox/loadingAnimation.gif' /></p>");
	jQuery('#results').show();
	
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		
		jQuery('#results').html('<p>' + response + '</p>' +
								'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
		jQuery('#results_ok_button').click(remove_results);
		
	});
	
}

function remove_results() {
	
	jQuery("#results_ok_button").unbind("click");
	jQuery("#results").remove();
	
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
	
		jQuery("body","html").css({height: "auto", width: "auto"});
		jQuery("html").css("overflow","");
		
	}
	
	document.onkeydown = "";
	document.onkeyup = "";
	return false;
	
}