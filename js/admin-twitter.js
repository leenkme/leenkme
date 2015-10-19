var $leenkme_admin_twitter_jquery = jQuery.noConflict();
//var LeenkMeTwitterManager = LeenkMeTwitterManager || {};

$leenkme_admin_twitter_jquery( document ).ready( function($) {
	//var twitter_manager = new LeenkMeTwitterManager.ListAccountsView();
    $( '.leenk-me_page_leenkme-twitter' ).on( 'click', '#add_new_twitter_account', function( event ) {
        event.preventDefault();
        //twitter_manager.render();
		var data = {
			action:   'leenkme_get_social_network_account',
			api_key:  $( 'input#leenkme-api-key' ).val(),
			api_action: 'add-account',
			network:  $( 'input#network' ).val(),
			_wpnonce: $('input#leenkme_add_account_wpnonce').val()
		};
		console.log( data );
		ajax_response(data);
	});
});