(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
	$( window ).load(function() {
		$( '.manage_course_adp_link' ).click( function( e ) 
		{
			// Prevent the default behavior for the link
			e.preventDefault();
			var link = this;
			var id   = jQuery( link ).attr( 'data-id' );
			var nonce = jQuery( link ).attr( 'data-nonce' );

			// This is what we are sending the server
			var data = 
			{
				action: 'luManageCoursesDelete',
				course_id: id,
				nonce: nonce
			}
			// Change the anchor text of the link
			// To provide the user some immediate feedback
			$( link ).text( 'Deleting course...' );
			// Post to the server
			$.post( admin_url, data, function( data ) 
			{
				// Parse the XML response with jQuery
				// Get the Status
				var status = $( data ).find( 'response_data' ).text();
				// Get the Message
				var message = $( data ).find( 'supplemental message' ).text();
				// If we are successful, add the success message and remove the link
				if( status == 'success' ) 
				{
					alert( message );
					$( link ).text( 'Delete course' );
				} else 
				{
					alert( message );
				}
			});
		});
	})
})( jQuery );
