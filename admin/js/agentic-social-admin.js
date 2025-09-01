( function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 */

	$( document ).ready( function() {
		
		// Handle "Share Now" button click
		$( '#agentic-social-share-now' ).on( 'click', function( e ) {
			e.preventDefault();
			
			var button = $( this );
			var postId = $( '#post_ID' ).val();
			
			// Disable button and show loading state
			button.prop( 'disabled', true ).text( 'Sharing...' );
			
			// Make AJAX request to share the post
			$.ajax( {
				url: agentic_social_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'agentic_social_share_now',
					post_id: postId,
					nonce: agentic_social_ajax.nonce
				},
				success: function( response ) {
					if ( response.success ) {
						button.text( 'Shared!' );
						
						// Show success message
						var message = $( '<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>' );
						$( '.agentic-social-meta-box' ).before( message );
						
						// Reset button after 3 seconds
						setTimeout( function() {
							button.prop( 'disabled', false ).text( 'Share Now' );
						}, 3000 );
					} else {
						button.prop( 'disabled', false ).text( 'Share Now' );
						
						// Show error message
						var errorMessage = $( '<div class="notice notice-error is-dismissible"><p>' + response.data.message + '</p></div>' );
						$( '.agentic-social-meta-box' ).before( errorMessage );
					}
				},
				error: function() {
					button.prop( 'disabled', false ).text( 'Share Now' );
					
					// Show generic error message
					var errorMessage = $( '<div class="notice notice-error is-dismissible"><p>An error occurred while sharing. Please try again.</p></div>' );
					$( '.agentic-social-meta-box' ).before( errorMessage );
				}
			} );
		} );
		
		// Handle LinkedIn authentication
		$( '#linkedin-auth-button' ).on( 'click', function( e ) {
			e.preventDefault();
			
			var authUrl = $( this ).data( 'auth-url' );
			
			// Open LinkedIn auth in popup window
			var width = 600;
			var height = 700;
			var left = ( screen.width / 2 ) - ( width / 2 );
			var top = ( screen.height / 2 ) - ( height / 2 );
			
			window.open(
				authUrl,
				'linkedin-auth',
				'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left
			);
		} );
		
		// Toggle AI Agent mode settings
		$( '#enable_ai_agent' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.ai-agent-settings' ).slideDown();
				
				// Add AI agent indicator
				if ( ! $( '.ai-agent-indicator' ).length ) {
					$( 'h1' ).append( '<span class="ai-agent-indicator">AI Mode</span>' );
				}
			} else {
				$( '.ai-agent-settings' ).slideUp();
				$( '.ai-agent-indicator' ).remove();
			}
		} );
		
		// Initialize AI agent mode indicator
		if ( $( '#enable_ai_agent' ).is( ':checked' ) ) {
			if ( ! $( '.ai-agent-indicator' ).length ) {
				$( 'h1' ).append( '<span class="ai-agent-indicator">AI Mode</span>' );
			}
		}
		
		// Handle dismissible notices
		$( document ).on( 'click', '.notice-dismiss', function() {
			$( this ).parent( '.notice' ).fadeOut();
		} );
		
		// Auto-save warning for unsaved changes
		var formModified = false;
		
		$( 'form' ).on( 'change', 'input, select, textarea', function() {
			formModified = true;
		} );
		
		$( 'form' ).on( 'submit', function() {
			formModified = false;
		} );
		
		$( window ).on( 'beforeunload', function() {
			if ( formModified ) {
				return 'You have unsaved changes. Are you sure you want to leave?';
			}
		} );
		
	} );

} )( jQuery );
