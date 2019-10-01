var $lm_post_twitter_jquery = jQuery.noConflict();

$lm_post_twitter_jquery(document).ready(function($) {
	
	$( '.post-php, .post-new-php' ).on( 'mousedown', 'textarea#leenkme_tweet', function() {
		
		$( 'input[name=lm_tweet_type]' ).val( 1 );
		$( 'span.tw_default_format' ).hide();
		$( 'span.tw_manual_format' ).show();
		$( 'a#set_to_default_tweet' ).show();
		
	});
	
	$( '.post-php, .post-new-php' ).on( 'click', 'a#set_to_default_tweet', function( e ) {
	
		e.preventDefault();
		
		$( 'input[name=lm_tweet_type]' ).val( 0 );
		$( 'span.tw_default_format' ).show();
		$( 'span.tw_manual_format' ).hide();
		$( 'a#set_to_default_tweet' ).hide();
		
	});
	
	$( '.post-php, .post-new-php' ).on('keyup paste', 'textarea#leenkme_tweet', function() {
		
		$( 'span#lm_tweet_count' ).text( lm_tweet_len( $( this ).val() ) );
		
	});
		
	$('.post-php, .post-new-php').on( 'click', 'input#lm_retweet_button', function() {
		
		var data = {
			'action': 		'retweet',
			'id':  			$( 'input#post_ID' ).val(),
			'tweet':  		$( 'textarea#leenkme_tweet' ).val(),
			'_wpnonce': 	$( 'input#leenkme_wpnonce' ).val()
		};
		
		ajax_response( data );
		
	});
	

});