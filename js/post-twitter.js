var $lm_post_twitter_jquery = jQuery.noConflict();

$lm_post_twitter_jquery(document).ready(function($) {
	
	$( 'textarea#leenkme_tweet' ).live('mousedown', function() {
		
		$( 'input[name=lm_tweet_type]' ).val( 1 );
		$( 'span.tw_default_format' ).hide();
		$( 'span.tw_manual_format' ).show();
		$( 'a#set_to_default_tweet' ).show();
		
	});
	
	$( 'a#set_to_default_tweet' ).live('click', function( e ) {
	
		e.preventDefault();
		
		$( 'input[name=lm_tweet_type]' ).val( 0 );
		$( 'span.tw_default_format' ).show();
		$( 'span.tw_manual_format' ).hide();
		$( 'a#set_to_default_tweet' ).hide();
		
	});
	
	$( 'textarea#leenkme_tweet' ).bind('keyup paste', function() {
		
		$( 'span#lm_tweet_count' ).text( lm_tweet_len( $( this ).val() ) );
		
	});
		
	$('input#lm_retweet_button').live('click', function() {
		
		var data = {
			'action': 		'retweet',
			'id':  			$( 'input#post_ID' ).val(),
			'post_author':	$( 'input#post_author' ).val(),
			'tweet':  		$( 'textarea#leenkme_tweet' ).val(),
			'_wpnonce': 	$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
	});
	

});