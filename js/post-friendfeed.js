var $lm_post_friendfeed_jquery = jQuery.noConflict();

$lm_post_friendfeed_jquery(document).ready(function($) {
	
	$( 'textarea#lm_ff_body, input#lm_ff_linkname, input#lm_ff_caption, textarea#lm_ff_description' ).live('mousedown', function() {
		
		$( 'input[name=lm_friendfeed_type]' ).val( 1 );
		$( 'span.ff_default_format' ).hide();
		$( 'span.ff_manual_format' ).show();
		$( 'a#set_to_default_ff_post' ).show();
		
	});
	
	$( 'a#set_to_default_ff_post' ).live('click', function( e ) {
	
		e.preventDefault();
		
		$( 'input[name=lm_friendfeed_type]' ).val( 0 );
		$( 'span.ff_default_format' ).show();
		$( 'span.ff_manual_format' ).hide();
		$( 'a#set_to_default_ff_post' ).hide();
		
	});
		
	$('input#lm_refeed_button').live('click', function() {
		
		friendfeed_array = {
			'body':				$( 'textarea#lm_ff_body' ).val(),
			'picture':			$( 'input[name=friendfeed_image]' ).val()
		};
		
		var data = {
			'action': 			'refeed',
			'id':  				$( 'input#post_ID' ).val(),
			'post_author':		$( 'input#post_author' ).val(),
			'friendfeed_array': friendfeed_array,
			'_wpnonce': 		$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
	});
	
});