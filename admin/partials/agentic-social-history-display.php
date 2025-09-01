<?php
/**
 * Provide a admin area view for share history
 *
 * This file is used to markup the share history page of the plugin.
 *
 * @link       https://paolobelcastro.com
 * @since      1.0.0
 *
 * @package    Agentic_Social
 * @subpackage Agentic_Social/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Get share history
$share_log = get_option( 'agentic_social_share_log', array() );
$share_log = array_reverse( $share_log ); // Show newest first

// Pagination
$per_page = 20;
$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$total_items = count( $share_log );
$total_pages = ceil( $total_items / $per_page );
$offset = ( $current_page - 1 ) * $per_page;
$paged_log = array_slice( $share_log, $offset, $per_page );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<?php if ( empty( $share_log ) ) : ?>
		<div class="notice notice-info">
			<p><?php esc_html_e( 'No sharing history found. Start sharing posts to see them here!', 'agentic-social' ); ?></p>
		</div>
	<?php else : ?>
		
		<div class="tablenav top">
			<div class="alignleft actions">
				<span class="displaying-num">
					<?php
					printf(
						/* translators: %s: Number of items */
						_n( '%s item', '%s items', $total_items, 'agentic-social' ),
						number_format_i18n( $total_items )
					);
					?>
				</span>
			</div>
			<?php if ( $total_pages > 1 ) : ?>
				<div class="tablenav-pages">
					<?php
					echo paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'prev_text' => __( '&laquo;', 'agentic-social' ),
						'next_text' => __( '&raquo;', 'agentic-social' ),
						'total' => $total_pages,
						'current' => $current_page,
					) );
					?>
				</div>
			<?php endif; ?>
		</div>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-title"><?php esc_html_e( 'Post', 'agentic-social' ); ?></th>
					<th scope="col" class="manage-column column-platform"><?php esc_html_e( 'Platform', 'agentic-social' ); ?></th>
					<th scope="col" class="manage-column column-status"><?php esc_html_e( 'Status', 'agentic-social' ); ?></th>
					<th scope="col" class="manage-column column-date"><?php esc_html_e( 'Date', 'agentic-social' ); ?></th>
					<th scope="col" class="manage-column column-actions"><?php esc_html_e( 'Actions', 'agentic-social' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $paged_log as $entry ) : ?>
					<?php
					$post = get_post( $entry['post_id'] );
					$post_title = $post ? $post->post_title : __( 'Post not found', 'agentic-social' );
					$post_url = $post ? get_edit_post_link( $entry['post_id'] ) : '#';
					$status_class = '';
					
					switch ( $entry['status'] ) {
						case 'completed':
							$status_class = 'success';
							break;
						case 'failed':
							$status_class = 'error';
							break;
						case 'initiated':
							$status_class = 'warning';
							break;
						default:
							$status_class = 'info';
					}
					?>
					<tr>
						<td class="column-title">
							<strong>
								<?php if ( $post ) : ?>
									<a href="<?php echo esc_url( $post_url ); ?>"><?php echo esc_html( $post_title ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $post_title ); ?>
								<?php endif; ?>
							</strong>
							<?php if ( ! empty( $entry['sharing_data']['summary'] ) ) : ?>
								<div class="row-actions">
									<span class="view-summary">
										<a href="#" onclick="toggleSummary(<?php echo esc_attr( $entry['post_id'] ); ?>); return false;">
											<?php esc_html_e( 'View Summary', 'agentic-social' ); ?>
										</a>
									</span>
								</div>
								<div id="summary-<?php echo esc_attr( $entry['post_id'] ); ?>" class="summary-content" style="display: none; margin-top: 10px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa;">
									<?php echo wp_kses_post( nl2br( $entry['sharing_data']['summary'] ) ); ?>
								</div>
							<?php endif; ?>
						</td>
						<td class="column-platform">
							<span class="platform-badge platform-<?php echo esc_attr( $entry['platform'] ); ?>">
								<?php echo esc_html( ucfirst( $entry['platform'] ) ); ?>
							</span>
						</td>
						<td class="column-status">
							<span class="status-badge status-<?php echo esc_attr( $status_class ); ?>">
								<?php echo esc_html( ucfirst( $entry['status'] ) ); ?>
							</span>
						</td>
						<td class="column-date">
							<?php echo esc_html( mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $entry['timestamp'] ) ); ?>
						</td>
						<td class="column-actions">
							<?php if ( $post && $entry['status'] === 'initiated' ) : ?>
								<button type="button" class="button button-small retry-share" 
								        data-post-id="<?php echo esc_attr( $entry['post_id'] ); ?>" 
								        data-platform="<?php echo esc_attr( $entry['platform'] ); ?>">
									<?php esc_html_e( 'Retry', 'agentic-social' ); ?>
								</button>
							<?php endif; ?>
							
							<?php if ( ! empty( $entry['sharing_data']['url'] ) ) : ?>
								<a href="<?php echo esc_url( $entry['sharing_data']['url'] ); ?>" 
								   class="button button-small" target="_blank" rel="noopener">
									<?php esc_html_e( 'View Post', 'agentic-social' ); ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<?php if ( $total_pages > 1 ) : ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php
					echo paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'prev_text' => __( '&laquo;', 'agentic-social' ),
						'next_text' => __( '&raquo;', 'agentic-social' ),
						'total' => $total_pages,
						'current' => $current_page,
					) );
					?>
				</div>
			</div>
		<?php endif; ?>
		
	<?php endif; ?>
	
	<div class="postbox" style="margin-top: 20px;">
		<div class="postbox-header">
			<h2 class="hndle"><?php esc_html_e( 'Statistics', 'agentic-social' ); ?></h2>
		</div>
		<div class="inside">
			<?php
			$stats = array(
				'total' => count( $share_log ),
				'completed' => 0,
				'failed' => 0,
				'initiated' => 0,
				'platforms' => array(),
			);
			
			foreach ( $share_log as $entry ) {
				if ( isset( $stats[ $entry['status'] ] ) ) {
					$stats[ $entry['status'] ]++;
				}
				
				if ( ! isset( $stats['platforms'][ $entry['platform'] ] ) ) {
					$stats['platforms'][ $entry['platform'] ] = 0;
				}
				$stats['platforms'][ $entry['platform'] ]++;
			}
			?>
			
			<div class="agentic-social-stats">
				<div class="stat-box">
					<div class="stat-number"><?php echo esc_html( $stats['total'] ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Total Shares', 'agentic-social' ); ?></div>
				</div>
				
				<div class="stat-box">
					<div class="stat-number"><?php echo esc_html( $stats['completed'] ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Completed', 'agentic-social' ); ?></div>
				</div>
				
				<div class="stat-box">
					<div class="stat-number"><?php echo esc_html( $stats['initiated'] ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'In Progress', 'agentic-social' ); ?></div>
				</div>
				
				<div class="stat-box">
					<div class="stat-number"><?php echo esc_html( $stats['failed'] ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Failed', 'agentic-social' ); ?></div>
				</div>
			</div>
			
			<?php if ( ! empty( $stats['platforms'] ) ) : ?>
				<h4><?php esc_html_e( 'By Platform', 'agentic-social' ); ?></h4>
				<ul>
					<?php foreach ( $stats['platforms'] as $platform => $count ) : ?>
						<li><?php echo esc_html( ucfirst( $platform ) ); ?>: <?php echo esc_html( $count ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
function toggleSummary(postId) {
	var summaryDiv = document.getElementById('summary-' + postId);
	if (summaryDiv) {
		summaryDiv.style.display = summaryDiv.style.display === 'none' ? 'block' : 'none';
	}
}

jQuery(document).ready(function($) {
	$('.retry-share').on('click', function() {
		var postId = $(this).data('post-id');
		var platform = $(this).data('platform');
		var button = $(this);
		
		button.prop('disabled', true).text('<?php echo esc_js( __( 'Retrying...', 'agentic-social' ) ); ?>');
		
		$.post(ajaxurl, {
			action: 'agentic_social_share_post',
			post_id: postId,
			platform: platform,
			nonce: '<?php echo wp_create_nonce( 'agentic_social_nonce' ); ?>'
		}, function(response) {
			if (response.success) {
				location.reload();
			} else {
				alert('<?php echo esc_js( __( 'Failed to retry share. Please try again.', 'agentic-social' ) ); ?>');
				button.prop('disabled', false).text('<?php echo esc_js( __( 'Retry', 'agentic-social' ) ); ?>');
			}
		});
	});
});
</script>

<style>
.platform-badge {
	display: inline-block;
	padding: 2px 8px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: bold;
	text-transform: uppercase;
}

.platform-linkedin {
	background-color: #0077b5;
	color: white;
}

.status-badge {
	display: inline-block;
	padding: 2px 8px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: bold;
	text-transform: uppercase;
}

.status-success {
	background-color: #46b450;
	color: white;
}

.status-error {
	background-color: #dc3232;
	color: white;
}

.status-warning {
	background-color: #ffb900;
	color: #333;
}

.status-info {
	background-color: #00a0d2;
	color: white;
}

.agentic-social-stats {
	display: flex;
	gap: 20px;
	margin-bottom: 20px;
}

.stat-box {
	text-align: center;
	padding: 15px;
	background: #f9f9f9;
	border-radius: 4px;
	min-width: 100px;
}

.stat-number {
	font-size: 24px;
	font-weight: bold;
	color: #0073aa;
}

.stat-label {
	font-size: 12px;
	color: #666;
	margin-top: 5px;
}

.summary-content {
	font-size: 13px;
	line-height: 1.4;
}
</style>
