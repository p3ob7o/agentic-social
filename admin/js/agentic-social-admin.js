( function( $ ) {
	'use strict';

	/**
	 * Agentic Social Admin JavaScript
	 * Handles the guided sharing workflow and AI agent interactions
	 */

	// Global state for sharing workflow
	var AgenticSocial = {
		currentWorkflow: null,
		currentStep: 0,
		sharingData: null,
		isAiMode: false,
		
		init: function() {
			this.bindEvents();
			this.checkAiMode();
			this.initializeWorkflow();
		},
		
		bindEvents: function() {
			// Main share button
			$( document ).on( 'click', '#agentic-social-share-now', this.handleShareNow );
			
			// Generate summary button
			$( document ).on( 'click', '.generate-summary', this.handleGenerateSummary );
			
			// Platform selection
			$( document ).on( 'change', '.platform-selector', this.handlePlatformChange );
			
			// Workflow navigation
			$( document ).on( 'click', '.workflow-next', this.handleWorkflowNext );
			$( document ).on( 'click', '.workflow-prev', this.handleWorkflowPrev );
			$( document ).on( 'click', '.workflow-complete', this.handleWorkflowComplete );
			
			// Copy to clipboard
			$( document ).on( 'click', '.copy-content', this.handleCopyContent );
			
			// AI Mode toggle
			$( '#enable_ai_agent' ).on( 'change', this.handleAiModeToggle );
			
			// Auto-save warning
			this.bindAutoSaveWarning();
		},
		
		checkAiMode: function() {
			this.isAiMode = $( '#enable_ai_agent' ).is( ':checked' ) || 
			               $( 'body' ).hasClass( 'agentic-ai-mode' );
			
			if ( this.isAiMode ) {
				this.enableAiMode();
			}
		},
		
		enableAiMode: function() {
			$( 'body' ).addClass( 'agentic-ai-mode' );
			
			// Add AI indicators
			if ( ! $( '.ai-agent-indicator' ).length ) {
				$( 'h1' ).first().append( '<span class="ai-agent-indicator">🤖 AI Mode</span>' );
			}
			
			// Add data attributes for AI navigation
			this.addAiAttributes();
		},
		
		addAiAttributes: function() {
			// Add data attributes to key elements for AI navigation
			$( '#agentic-social-share-now' ).attr( 'data-agentic-action', 'start-sharing' );
			$( '.platform-selector' ).attr( 'data-agentic-action', 'select-platform' );
			$( '.copy-content' ).attr( 'data-agentic-action', 'copy-content' );
			$( '.workflow-next' ).attr( 'data-agentic-action', 'next-step' );
			$( '.workflow-complete' ).attr( 'data-agentic-action', 'complete-workflow' );
		},
		
		initializeWorkflow: function() {
			// Initialize workflow UI if needed
			if ( $( '.agentic-social-workflow' ).length ) {
				this.renderWorkflowUI();
			}
		},
		
		handleShareNow: function( e ) {
			e.preventDefault();
			
			var button = $( this );
			var postId = $( '#post_ID' ).val() || button.data( 'post-id' );
			var platform = $( '.platform-selector' ).val() || 'linkedin';
			
			if ( ! postId ) {
				AgenticSocial.showError( 'No post ID found. Please save the post first.' );
				return;
			}
			
			AgenticSocial.startSharingWorkflow( postId, platform, button );
		},
		
		startSharingWorkflow: function( postId, platform, button ) {
			var originalText = button.text();
			button.prop( 'disabled', true ).text( 'Preparing...' );
			
			$.ajax( {
				url: agentic_social_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'agentic_social_share_post',
					post_id: postId,
					platform: platform,
					nonce: agentic_social_ajax.nonce
				},
				success: function( response ) {
					button.prop( 'disabled', false ).text( originalText );
					
					if ( response.success ) {
						AgenticSocial.sharingData = response.data.sharing_data;
						AgenticSocial.currentWorkflow = response.data.workflow_steps;
						AgenticSocial.currentStep = 0;
						
						AgenticSocial.showWorkflowModal( platform );
					} else {
						AgenticSocial.showError( response.data || 'Failed to start sharing workflow' );
					}
				},
				error: function( xhr, status, error ) {
					button.prop( 'disabled', false ).text( originalText );
					AgenticSocial.showError( 'Network error: ' + error );
				}
			} );
		},
		
		showWorkflowModal: function( platform ) {
			// Remove existing modal
			$( '.agentic-workflow-modal' ).remove();
			
			var modal = this.createWorkflowModal( platform );
			$( 'body' ).append( modal );
			
			// Show modal with animation
			setTimeout( function() {
				$( '.agentic-workflow-modal' ).addClass( 'show' );
			}, 100 );
			
			this.renderCurrentStep();
		},
		
		createWorkflowModal: function( platform ) {
			var platformName = platform.charAt( 0 ).toUpperCase() + platform.slice( 1 );
			
			return $( `
				<div class="agentic-workflow-modal">
					<div class="workflow-overlay"></div>
					<div class="workflow-content">
						<div class="workflow-header">
							<h2>Share to ${platformName}</h2>
							<button class="workflow-close" aria-label="Close">&times;</button>
						</div>
						<div class="workflow-body">
							<div class="workflow-progress">
								<div class="progress-bar">
									<div class="progress-fill"></div>
								</div>
								<div class="step-indicator">
									Step <span class="current-step">1</span> of <span class="total-steps">${this.currentWorkflow.length}</span>
								</div>
							</div>
							<div class="workflow-step-content"></div>
						</div>
						<div class="workflow-footer">
							<button class="button workflow-prev" style="display: none;">Previous</button>
							<button class="button button-primary workflow-next">Next Step</button>
							<button class="button button-primary workflow-complete" style="display: none;">Complete</button>
						</div>
					</div>
				</div>
			` );
		},
		
		renderCurrentStep: function() {
			if ( ! this.currentWorkflow || this.currentStep >= this.currentWorkflow.length ) {
				return;
			}
			
			var step = this.currentWorkflow[ this.currentStep ];
			var stepNumber = this.currentStep + 1;
			var totalSteps = this.currentWorkflow.length;
			
			// Update progress
			var progress = ( stepNumber / totalSteps ) * 100;
			$( '.progress-fill' ).css( 'width', progress + '%' );
			$( '.current-step' ).text( stepNumber );
			
			// Update step content
			var stepContent = this.createStepContent( step );
			$( '.workflow-step-content' ).html( stepContent );
			
			// Update navigation buttons
			$( '.workflow-prev' ).toggle( this.currentStep > 0 );
			$( '.workflow-next' ).toggle( this.currentStep < totalSteps - 1 );
			$( '.workflow-complete' ).toggle( this.currentStep === totalSteps - 1 );
			
			// Add AI attributes if in AI mode
			if ( this.isAiMode ) {
				this.addStepAiAttributes( step );
			}
		},
		
		createStepContent: function( step ) {
			var content = `
				<div class="step-content" data-step="${step.step}">
					<div class="step-header">
						<h3>${step.title}</h3>
						<p class="step-description">${step.description}</p>
					</div>
					<div class="step-body">
			`;
			
			switch ( step.action ) {
				case 'open_url':
					content += this.createOpenUrlStep( step );
					break;
				case 'click_element':
					content += this.createClickElementStep( step );
					break;
				case 'paste_content':
					content += this.createPasteContentStep( step );
					break;
				case 'add_comment':
					content += this.createAddCommentStep( step );
					break;
				default:
					content += this.createGenericStep( step );
			}
			
			content += `
					</div>
				</div>
			`;
			
			return content;
		},
		
		createOpenUrlStep: function( step ) {
			return `
				<div class="url-step">
					<p><strong>URL to open:</strong></p>
					<div class="url-box">
						<input type="text" value="${step.url}" readonly class="url-input">
						<button class="button copy-url" data-url="${step.url}">Copy URL</button>
					</div>
					<div class="action-buttons">
						<a href="${step.url}" target="_blank" class="button button-primary" ${this.isAiMode ? step.ai_selector : ''}>
							Open ${step.url.includes('linkedin') ? 'LinkedIn' : 'Platform'}
						</a>
					</div>
					<div class="step-note">
						<p><em>Click the button above to open the platform in a new tab, then return here to continue.</em></p>
					</div>
				</div>
			`;
		},
		
		createClickElementStep: function( step ) {
			return `
				<div class="click-step">
					<div class="instruction-box">
						<p><strong>What to do:</strong></p>
						<p>${step.description}</p>
						${step.selector ? `<p><strong>Look for:</strong> Elements matching <code>${step.selector}</code></p>` : ''}
					</div>
					${this.isAiMode ? `
						<div class="ai-selector-info">
							<p><strong>AI Selector:</strong> <code>${step.ai_selector}</code></p>
						</div>
					` : ''}
					<div class="visual-guide">
						<p>💡 <strong>Tip:</strong> Look for the post creation button, usually labeled "Start a post" or "What's on your mind?"</p>
					</div>
				</div>
			`;
		},
		
		createPasteContentStep: function( step ) {
			var content = this.sharingData ? this.sharingData.linkedin_summary || this.sharingData.summary : '';
			
			return `
				<div class="paste-step">
					<div class="content-to-paste">
						<p><strong>Content to paste:</strong></p>
						<textarea readonly class="content-textarea" id="content-to-paste">${content}</textarea>
						<button class="button copy-content" data-content="${this.escapeHtml(content)}">
							📋 Copy Content
						</button>
					</div>
					<div class="paste-instructions">
						<p><strong>Instructions:</strong></p>
						<ol>
							<li>Click the "Copy Content" button above</li>
							<li>Click in the post editor on LinkedIn</li>
							<li>Paste the content (Ctrl+V or Cmd+V)</li>
							<li>Review and edit if needed</li>
						</ol>
					</div>
					${this.isAiMode ? `
						<div class="ai-selector-info">
							<p><strong>AI Selector:</strong> <code>${step.ai_selector}</code></p>
						</div>
					` : ''}
				</div>
			`;
		},
		
		createAddCommentStep: function( step ) {
			var url = this.sharingData ? this.sharingData.url : '';
			
			return `
				<div class="comment-step">
					<div class="link-to-add">
						<p><strong>Link to add as comment:</strong></p>
						<div class="url-box">
							<input type="text" value="${url}" readonly class="url-input">
							<button class="button copy-url" data-url="${url}">Copy Link</button>
						</div>
					</div>
					<div class="comment-instructions">
						<p><strong>Instructions:</strong></p>
						<ol>
							<li>After your post is published, find the comment section</li>
							<li>Click the "Copy Link" button above</li>
							<li>Click in the comment box</li>
							<li>Paste the link (Ctrl+V or Cmd+V)</li>
							<li>Submit the comment</li>
						</ol>
					</div>
					<div class="why-comment">
						<p><strong>Why add as comment?</strong></p>
						<p>Adding links as comments instead of in the main post typically results in 2-3x better reach due to platform algorithm preferences.</p>
					</div>
					${this.isAiMode ? `
						<div class="ai-selector-info">
							<p><strong>AI Selector:</strong> <code>${step.ai_selector}</code></p>
						</div>
					` : ''}
				</div>
			`;
		},
		
		createGenericStep: function( step ) {
			return `
				<div class="generic-step">
					<div class="instruction-box">
						<p>${step.description}</p>
					</div>
				</div>
			`;
		},
		
		addStepAiAttributes: function( step ) {
			// Add AI-specific attributes to the current step elements
			setTimeout( function() {
				if ( step.ai_selector ) {
					$( '.workflow-step-content a, .workflow-step-content button' ).first()
						.attr( 'data-agentic-action', step.action )
						.attr( 'data-agentic-selector', step.ai_selector );
				}
			}, 100 );
		},
		
		handleWorkflowNext: function( e ) {
			e.preventDefault();
			
			if ( AgenticSocial.currentStep < AgenticSocial.currentWorkflow.length - 1 ) {
				AgenticSocial.currentStep++;
				AgenticSocial.renderCurrentStep();
			}
		},
		
		handleWorkflowPrev: function( e ) {
			e.preventDefault();
			
			if ( AgenticSocial.currentStep > 0 ) {
				AgenticSocial.currentStep--;
				AgenticSocial.renderCurrentStep();
			}
		},
		
		handleWorkflowComplete: function( e ) {
			e.preventDefault();
			
			// Mark sharing as completed
			AgenticSocial.markSharingComplete();
			
			// Close modal
			$( '.agentic-workflow-modal' ).removeClass( 'show' );
			setTimeout( function() {
				$( '.agentic-workflow-modal' ).remove();
			}, 300 );
			
			// Show success message
			AgenticSocial.showSuccess( 'Sharing workflow completed! Your post should now be live on the platform.' );
		},
		
		markSharingComplete: function() {
			if ( ! this.sharingData ) return;
			
			$.ajax( {
				url: agentic_social_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'agentic_social_mark_complete',
					post_id: this.sharingData.post_id,
					platform: 'linkedin', // TODO: make dynamic
					nonce: agentic_social_ajax.nonce
				},
				success: function( response ) {
					console.log( 'Sharing marked as complete:', response );
				}
			} );
		},
		
		handleCopyContent: function( e ) {
			e.preventDefault();
			
			var button = $( this );
			var content = button.data( 'content' ) || button.data( 'url' ) || $( '#content-to-paste' ).val();
			
			if ( ! content ) {
				AgenticSocial.showError( 'No content to copy' );
				return;
			}
			
			AgenticSocial.copyToClipboard( content, button );
		},
		
		copyToClipboard: function( text, button ) {
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( function() {
					AgenticSocial.showCopySuccess( button );
				} ).catch( function() {
					AgenticSocial.fallbackCopyToClipboard( text, button );
				} );
			} else {
				AgenticSocial.fallbackCopyToClipboard( text, button );
			}
		},
		
		fallbackCopyToClipboard: function( text, button ) {
			var textArea = document.createElement( 'textarea' );
			textArea.value = text;
			textArea.style.position = 'fixed';
			textArea.style.left = '-999999px';
			textArea.style.top = '-999999px';
			document.body.appendChild( textArea );
			textArea.focus();
			textArea.select();
			
			try {
				document.execCommand( 'copy' );
				AgenticSocial.showCopySuccess( button );
			} catch ( err ) {
				AgenticSocial.showError( 'Failed to copy to clipboard' );
			}
			
			document.body.removeChild( textArea );
		},
		
		showCopySuccess: function( button ) {
			var originalText = button.text();
			button.text( '✓ Copied!' );
			setTimeout( function() {
				button.text( originalText );
			}, 2000 );
		},
		
		handleGenerateSummary: function( e ) {
			e.preventDefault();
			
			var button = $( this );
			var postId = $( '#post_ID' ).val() || button.data( 'post-id' );
			var platform = button.data( 'platform' ) || 'linkedin';
			
			if ( ! postId ) {
				AgenticSocial.showError( 'No post ID found' );
				return;
			}
			
			var originalText = button.text();
			button.prop( 'disabled', true ).text( 'Generating...' );
			
			$.ajax( {
				url: agentic_social_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'agentic_social_generate_summary',
					post_id: postId,
					platform: platform,
					nonce: agentic_social_ajax.nonce
				},
				success: function( response ) {
					button.prop( 'disabled', false ).text( originalText );
					
					if ( response.success ) {
						var targetTextarea = button.siblings( 'textarea' ).first();
						if ( targetTextarea.length ) {
							targetTextarea.val( response.data.summary );
						}
						
						AgenticSocial.showSuccess( 'Summary generated successfully!' );
					} else {
						AgenticSocial.showError( response.data || 'Failed to generate summary' );
					}
				},
				error: function() {
					button.prop( 'disabled', false ).text( originalText );
					AgenticSocial.showError( 'Network error occurred' );
				}
			} );
		},
		
		handlePlatformChange: function( e ) {
			var platform = $( this ).val();
			
			// Update UI based on platform selection
			$( '.platform-specific' ).hide();
			$( '.platform-' + platform ).show();
			
			// Update generate summary buttons
			$( '.generate-summary' ).data( 'platform', platform );
		},
		
		handleAiModeToggle: function( e ) {
			if ( $( this ).is( ':checked' ) ) {
				AgenticSocial.enableAiMode();
			} else {
				AgenticSocial.disableAiMode();
			}
		},
		
		disableAiMode: function() {
			$( 'body' ).removeClass( 'agentic-ai-mode' );
			$( '.ai-agent-indicator' ).remove();
			
			// Remove AI attributes
			$( '[data-agentic-action]' ).removeAttr( 'data-agentic-action' );
			$( '[data-agentic-selector]' ).removeAttr( 'data-agentic-selector' );
			
			this.isAiMode = false;
		},
		
		bindAutoSaveWarning: function() {
			var formModified = false;
			
			$( document ).on( 'change', 'form input, form select, form textarea', function() {
				formModified = true;
			} );
			
			$( document ).on( 'submit', 'form', function() {
				formModified = false;
			} );
			
			$( window ).on( 'beforeunload', function( e ) {
				if ( formModified ) {
					var message = 'You have unsaved changes. Are you sure you want to leave?';
					e.returnValue = message;
					return message;
				}
			} );
		},
		
		showSuccess: function( message ) {
			this.showNotice( message, 'success' );
		},
		
		showError: function( message ) {
			this.showNotice( message, 'error' );
		},
		
		showNotice: function( message, type ) {
			var notice = $( '<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>' );
			
			// Find the best place to insert the notice
			var target = $( '.agentic-social-meta-box' ).first();
			if ( ! target.length ) {
				target = $( '.wrap h1' ).first();
			}
			
			if ( target.length ) {
				target.after( notice );
			} else {
				$( 'body' ).prepend( notice );
			}
			
			// Auto-dismiss after 5 seconds
			setTimeout( function() {
				notice.fadeOut();
			}, 5000 );
		},
		
		escapeHtml: function( text ) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			
			return text.replace( /[&<>"']/g, function( m ) {
				return map[ m ];
			} );
		}
	};

	// Initialize when document is ready
	$( document ).ready( function() {
		AgenticSocial.init();
		
		// Handle modal close
		$( document ).on( 'click', '.workflow-close, .workflow-overlay', function( e ) {
			if ( e.target === this ) {
				$( '.agentic-workflow-modal' ).removeClass( 'show' );
				setTimeout( function() {
					$( '.agentic-workflow-modal' ).remove();
				}, 300 );
			}
		} );
		
		// Handle notice dismiss
		$( document ).on( 'click', '.notice-dismiss', function() {
			$( this ).parent( '.notice' ).fadeOut();
		} );
		
		// Keyboard shortcuts
		$( document ).on( 'keydown', function( e ) {
			// ESC to close modal
			if ( e.key === 'Escape' && $( '.agentic-workflow-modal.show' ).length ) {
				$( '.agentic-workflow-modal' ).removeClass( 'show' );
				setTimeout( function() {
					$( '.agentic-workflow-modal' ).remove();
				}, 300 );
			}
		} );
	} );

} )( jQuery );