<?php
/**
 * Content processing functionality for the plugin.
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 */

/**
 * Content processing functionality for the plugin.
 *
 * This class defines all content processing functionality including
 * summary generation, content optimization, and platform-specific formatting.
 *
 * @since      1.0.0
 * @package    Agentic_Social
 * @subpackage Agentic_Social/includes
 * @author     Paolo Belcastro <paolo@paolobelcastro.com>
 */
class Agentic_Social_Content_Processor {

	/**
	 * Generate a summary of post content for social media sharing.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    The ID of the post to summarize.
	 * @param    string $platform   The target platform (linkedin, twitter, etc.).
	 * @param    int    $max_length Maximum length of the summary.
	 * @return   string             The generated summary.
	 */
	public static function generate_summary( $post_id, $platform = 'linkedin', $max_length = 300 ) {
		$post = get_post( $post_id );
		
		if ( ! $post ) {
			return '';
		}
		
		// Get custom message if set
		$custom_message = get_post_meta( $post_id, '_agentic_social_custom_message', true );
		if ( ! empty( $custom_message ) ) {
			return self::format_summary( $custom_message, $platform, $max_length );
		}
		
		// Extract content for summarization
		$content = $post->post_content;
		$title = $post->post_title;
		$excerpt = $post->post_excerpt;
		
		// Use excerpt if available and suitable
		if ( ! empty( $excerpt ) && strlen( $excerpt ) <= $max_length ) {
			$summary = $excerpt;
		} else {
			// Generate summary from content
			$summary = self::extract_summary_from_content( $content, $title, $max_length );
		}
		
		return self::format_summary( $summary, $platform, $max_length );
	}
	
	/**
	 * Extract summary from post content.
	 *
	 * @since    1.0.0
	 * @param    string $content     The post content.
	 * @param    string $title       The post title.
	 * @param    int    $max_length  Maximum length of the summary.
	 * @return   string              The extracted summary.
	 */
	private static function extract_summary_from_content( $content, $title, $max_length ) {
		// Strip HTML and shortcodes
		$clean_content = wp_strip_all_tags( do_shortcode( $content ) );
		
		// Remove extra whitespace
		$clean_content = preg_replace( '/\s+/', ' ', trim( $clean_content ) );
		
		// If content is short enough, use first paragraph
		$paragraphs = explode( "\n\n", $clean_content );
		$first_paragraph = trim( $paragraphs[0] );
		
		if ( strlen( $first_paragraph ) <= $max_length - 50 ) { // Leave room for title
			$summary = $first_paragraph;
		} else {
			// Truncate to sentences
			$summary = self::truncate_to_sentences( $clean_content, $max_length - 50 );
		}
		
		// Add engaging hook based on title
		$hook = self::generate_engaging_hook( $title );
		
		if ( ! empty( $hook ) && strlen( $hook . ' ' . $summary ) <= $max_length ) {
			$summary = $hook . ' ' . $summary;
		}
		
		return $summary;
	}
	
	/**
	 * Truncate text to complete sentences within length limit.
	 *
	 * @since    1.0.0
	 * @param    string $text       The text to truncate.
	 * @param    int    $max_length Maximum length.
	 * @return   string             The truncated text.
	 */
	private static function truncate_to_sentences( $text, $max_length ) {
		if ( strlen( $text ) <= $max_length ) {
			return $text;
		}
		
		// Find sentence boundaries
		$sentences = preg_split( '/(?<=[.!?])\s+/', $text );
		$result = '';
		
		foreach ( $sentences as $sentence ) {
			$test_length = strlen( $result . $sentence );
			if ( $test_length <= $max_length ) {
				$result .= ( empty( $result ) ? '' : ' ' ) . $sentence;
			} else {
				break;
			}
		}
		
		// If no complete sentences fit, truncate words
		if ( empty( $result ) ) {
			$words = explode( ' ', $text );
			$result = '';
			foreach ( $words as $word ) {
				$test_length = strlen( $result . ' ' . $word );
				if ( $test_length <= $max_length - 3 ) { // Leave room for "..."
					$result .= ( empty( $result ) ? '' : ' ' ) . $word;
				} else {
					$result .= '...';
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Generate an engaging hook based on post title.
	 *
	 * @since    1.0.0
	 * @param    string $title The post title.
	 * @return   string        The engaging hook.
	 */
	private static function generate_engaging_hook( $title ) {
		$hooks = array(
			'💡 New insight:',
			'🚀 Just published:',
			'📝 Latest article:',
			'🔍 Deep dive:',
			'💭 Thoughts on:',
			'🎯 Key takeaway:',
		);
		
		// Simple logic to choose hook based on title content
		$title_lower = strtolower( $title );
		
		if ( strpos( $title_lower, 'how to' ) !== false || strpos( $title_lower, 'guide' ) !== false ) {
			return '📚 Step-by-step guide:';
		}
		
		if ( strpos( $title_lower, 'why' ) !== false || strpos( $title_lower, 'reason' ) !== false ) {
			return '🤔 Ever wondered:';
		}
		
		if ( strpos( $title_lower, 'tip' ) !== false || strpos( $title_lower, 'hack' ) !== false ) {
			return '💡 Pro tip:';
		}
		
		// Default to random engaging hook
		return $hooks[ array_rand( $hooks ) ];
	}
	
	/**
	 * Format summary for specific platform.
	 *
	 * @since    1.0.0
	 * @param    string $summary    The raw summary.
	 * @param    string $platform   The target platform.
	 * @param    int    $max_length Maximum length.
	 * @return   string             The formatted summary.
	 */
	private static function format_summary( $summary, $platform, $max_length ) {
		$summary = trim( $summary );
		
		switch ( $platform ) {
			case 'linkedin':
				// LinkedIn allows longer posts, add professional touch
				$summary = self::add_linkedin_formatting( $summary );
				break;
				
			case 'twitter':
				// Twitter has character limits
				$max_length = min( $max_length, 280 );
				break;
		}
		
		// Ensure we don't exceed max length
		if ( strlen( $summary ) > $max_length ) {
			$summary = self::truncate_to_sentences( $summary, $max_length );
		}
		
		return $summary;
	}
	
	/**
	 * Add LinkedIn-specific formatting to summary.
	 *
	 * @since    1.0.0
	 * @param    string $summary The summary to format.
	 * @return   string          The formatted summary.
	 */
	private static function add_linkedin_formatting( $summary ) {
		// Add call-to-action endings for LinkedIn
		$cta_endings = array(
			"\n\nWhat's your take on this?",
			"\n\nThoughts? Let me know in the comments!",
			"\n\nHave you experienced something similar?",
			"\n\nWhat would you add to this?",
		);
		
		// Don't add CTA if summary already ends with a question
		if ( ! preg_match( '/\?$/', $summary ) ) {
			$summary .= $cta_endings[ array_rand( $cta_endings ) ];
		}
		
		return $summary;
	}
	
	/**
	 * Get sharing data for a post.
	 *
	 * @since    1.0.0
	 * @param    int $post_id The post ID.
	 * @return   array        The sharing data.
	 */
	public static function get_sharing_data( $post_id ) {
		$post = get_post( $post_id );
		
		if ( ! $post ) {
			return false;
		}
		
		$data = array(
			'post_id'     => $post_id,
			'title'       => $post->post_title,
			'url'         => get_permalink( $post_id ),
			'summary'     => self::generate_summary( $post_id ),
			'author'      => get_the_author_meta( 'display_name', $post->post_author ),
			'post_date'   => $post->post_date,
			'featured_image' => get_the_post_thumbnail_url( $post_id, 'large' ),
			'categories'  => wp_get_post_categories( $post_id, array( 'fields' => 'names' ) ),
			'tags'        => wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ),
		);
		
		// Add platform-specific summaries
		$data['linkedin_summary'] = self::generate_summary( $post_id, 'linkedin', 1300 );
		$data['twitter_summary'] = self::generate_summary( $post_id, 'twitter', 250 );
		
		return apply_filters( 'agentic_social_sharing_data', $data, $post_id );
	}
	
	/**
	 * Validate and sanitize sharing data.
	 *
	 * @since    1.0.0
	 * @param    array $data The sharing data to validate.
	 * @return   array       The validated data.
	 */
	public static function validate_sharing_data( $data ) {
		$validated = array();
		
		// Required fields
		$validated['post_id'] = isset( $data['post_id'] ) ? absint( $data['post_id'] ) : 0;
		$validated['title'] = isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '';
		$validated['url'] = isset( $data['url'] ) ? esc_url_raw( $data['url'] ) : '';
		$validated['summary'] = isset( $data['summary'] ) ? sanitize_textarea_field( $data['summary'] ) : '';
		
		// Optional fields
		$validated['author'] = isset( $data['author'] ) ? sanitize_text_field( $data['author'] ) : '';
		$validated['featured_image'] = isset( $data['featured_image'] ) ? esc_url_raw( $data['featured_image'] ) : '';
		
		// Platform-specific summaries
		$validated['linkedin_summary'] = isset( $data['linkedin_summary'] ) ? sanitize_textarea_field( $data['linkedin_summary'] ) : '';
		$validated['twitter_summary'] = isset( $data['twitter_summary'] ) ? sanitize_textarea_field( $data['twitter_summary'] ) : '';
		
		return $validated;
	}
}
