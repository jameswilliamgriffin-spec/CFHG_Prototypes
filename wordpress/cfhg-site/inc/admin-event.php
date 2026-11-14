<?php
/**
 * Editing events: the "Event details" box, the events list and Settings.
 *
 * Events use the classic editing screen rather than the block editor: a
 * title, the details box, a short description and an optional write-up.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Use the classic screen for events.
 *
 * @param bool   $use       Whether to use the block editor.
 * @param string $post_type Post type.
 * @return bool
 */
function cfhg_site_no_block_editor( $use, $post_type ) {
	return 'cfhg_event' === $post_type ? false : $use;
}
add_filter( 'use_block_editor_for_post_type', 'cfhg_site_no_block_editor', 10, 2 );

/**
 * Plain-English labels on the event screen.
 */
function cfhg_site_event_screen_text() {
	add_filter(
		'enter_title_here',
		static function ( $text, $post ) {
			return 'cfhg_event' === $post->post_type ? __( 'Event name, e.g. Monthly meeting', 'cfhg-site' ) : $text;
		},
		10,
		2
	);
}
add_action( 'admin_init', 'cfhg_site_event_screen_text' );

/**
 * Add the details box, and rename the excerpt box.
 */
function cfhg_site_event_boxes() {
	add_meta_box( 'cfhg_event_details', __( 'Event details', 'cfhg-site' ), 'cfhg_site_event_box', 'cfhg_event', 'normal', 'high' );
	remove_meta_box( 'postexcerpt', 'cfhg_event', 'normal' );
	add_meta_box( 'postexcerpt', __( 'Short description (shown in lists)', 'cfhg-site' ), 'post_excerpt_meta_box', 'cfhg_event', 'normal', 'high' );
}
add_action( 'add_meta_boxes_cfhg_event', 'cfhg_site_event_boxes' );

/**
 * Put the details box above the editor, and say what the editor is for.
 *
 * @param WP_Post $post Post.
 */
function cfhg_site_event_above_editor( $post ) {
	if ( 'cfhg_event' !== $post->post_type ) {
		return;
	}
	global $wp_meta_boxes;
	do_meta_boxes( get_current_screen(), 'normal', $post );
	unset( $wp_meta_boxes['cfhg_event']['normal'] );
	echo '<h2 style="padding:12px 0 4px;font-size:14px">' . esc_html__( 'Write-up (optional)', 'cfhg-site' ) . '</h2>';
	echo '<p class="description" style="margin:0 0 8px">' . esc_html__( 'Leave this empty before the event. Afterwards, add a few paragraphs and photos about what happened: it then shows on What’s On as a past event worth reading.', 'cfhg-site' ) . '</p>';
}
add_action( 'edit_form_after_title', 'cfhg_site_event_above_editor' );

/**
 * The details box.
 *
 * @param WP_Post $post Event.
 */
function cfhg_site_event_box( $post ) {
	$settings = cfhg_site_settings();
	$get      = static function ( $key ) use ( $post ) {
		return (string) get_post_meta( $post->ID, '_cfhg_' . $key, true );
	};
	$attend   = $get( 'attend' ) ? $get( 'attend' ) : 'both';
	$status   = $get( 'status' ) ? $get( 'status' ) : 'scheduled';
	wp_nonce_field( 'cfhg_event_save', 'cfhg_event_nonce' );
	?>
	<style>
		.cfhg-ev-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px 20px;margin:8px 0 4px}
		.cfhg-ev-grid label{display:block;font-weight:600;margin-bottom:4px}
		.cfhg-ev-grid input,.cfhg-ev-grid select{width:100%;max-width:100%}
		.cfhg-ev-wide{grid-column:1/-1}
		.cfhg-ev-cancel{padding:8px 12px;border-left:4px solid #d63638;background:#fcf0f1}
	</style>
	<div class="cfhg-ev-grid">
		<p><label for="cfhg_date"><?php esc_html_e( 'Date', 'cfhg-site' ); ?></label><input type="date" id="cfhg_date" name="cfhg[date]" value="<?php echo esc_attr( $get( 'date' ) ); ?>" required></p>
		<p><label for="cfhg_start"><?php esc_html_e( 'Starts', 'cfhg-site' ); ?></label><input type="time" id="cfhg_start" name="cfhg[start]" value="<?php echo esc_attr( $get( 'start' ) ); ?>"></p>
		<p><label for="cfhg_end"><?php esc_html_e( 'Finishes', 'cfhg-site' ); ?></label><input type="time" id="cfhg_end" name="cfhg[end]" value="<?php echo esc_attr( $get( 'end' ) ); ?>"></p>
		<p><label for="cfhg_attend"><?php esc_html_e( 'Where', 'cfhg-site' ); ?></label>
			<select id="cfhg_attend" name="cfhg[attend]">
				<?php
				foreach ( cfhg_site_attend_options() as $value => $label ) {
					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $value ), selected( $attend, $value, false ), esc_html( $label ) );
				}
				?>
			</select>
		</p>
		<p class="cfhg-ev-wide"><label for="cfhg_venue"><?php esc_html_e( 'Venue', 'cfhg-site' ); ?></label><input type="text" id="cfhg_venue" name="cfhg[venue]" value="<?php echo esc_attr( $get( 'venue' ) ); ?>" placeholder="<?php echo esc_attr( $settings['venue'] ); ?>"><span class="description"><?php esc_html_e( 'Leave blank for the usual venue.', 'cfhg-site' ); ?></span></p>
		<p class="cfhg-ev-wide"><label for="cfhg_online"><?php esc_html_e( 'Joining online', 'cfhg-site' ); ?></label><input type="text" id="cfhg_online" name="cfhg[online]" value="<?php echo esc_attr( $get( 'online' ) ); ?>" placeholder="<?php echo esc_attr( $settings['online'] ); ?>"><span class="description"><?php esc_html_e( 'Leave blank for the usual wording. Please don’t paste the Zoom link here: this is shown publicly.', 'cfhg-site' ); ?></span></p>
		<p class="cfhg-ev-wide <?php echo 'cancelled' === $status ? 'cfhg-ev-cancel' : ''; ?>"><label><input type="checkbox" name="cfhg[cancelled]" value="1" <?php checked( 'cancelled', $status ); ?> style="width:auto"> <?php esc_html_e( 'This event is cancelled', 'cfhg-site' ); ?></label><span class="description"><?php esc_html_e( 'It stays on What’s On marked “Cancelled”, and the homepage moves on to the next meeting.', 'cfhg-site' ); ?></span></p>
	</div>
	<?php
}

/**
 * Save the details box.
 *
 * @param int $post_id Event ID.
 */
function cfhg_site_event_save( $post_id ) {
	if ( ! isset( $_POST['cfhg_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cfhg_event_nonce'] ) ), 'cfhg_event_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$in = isset( $_POST['cfhg'] ) && is_array( $_POST['cfhg'] ) ? wp_unslash( $_POST['cfhg'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each field sanitised below.

	$date  = isset( $in['date'] ) ? sanitize_text_field( $in['date'] ) : '';
	$start = isset( $in['start'] ) ? sanitize_text_field( $in['start'] ) : '';
	$end   = isset( $in['end'] ) ? sanitize_text_field( $in['end'] ) : '';
	update_post_meta( $post_id, '_cfhg_date', preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ? $date : '' );
	update_post_meta( $post_id, '_cfhg_start', preg_match( '/^\d{2}:\d{2}$/', $start ) ? $start : '' );
	update_post_meta( $post_id, '_cfhg_end', preg_match( '/^\d{2}:\d{2}$/', $end ) ? $end : '' );

	$attend = isset( $in['attend'] ) ? sanitize_key( $in['attend'] ) : 'both';
	update_post_meta( $post_id, '_cfhg_attend', array_key_exists( $attend, cfhg_site_attend_options() ) ? $attend : 'both' );
	update_post_meta( $post_id, '_cfhg_venue', isset( $in['venue'] ) ? sanitize_text_field( $in['venue'] ) : '' );
	update_post_meta( $post_id, '_cfhg_online', isset( $in['online'] ) ? sanitize_text_field( $in['online'] ) : '' );
	update_post_meta( $post_id, '_cfhg_status', empty( $in['cancelled'] ) ? 'scheduled' : 'cancelled' );

	cfhg_site_update_sort_keys( $post_id );
}
add_action( 'save_post_cfhg_event', 'cfhg_site_event_save' );

/**
 * Warn when an event has no date, because it won't appear anywhere.
 */
function cfhg_site_event_date_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'cfhg_event' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$post = get_post();
	if ( $post && 'auto-draft' !== $post->post_status && ! get_post_meta( $post->ID, '_cfhg_date', true ) ) {
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'This event has no date yet, so it won’t appear on the site. Add one in Event details.', 'cfhg-site' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'cfhg_site_event_date_notice' );

/**
 * List columns: when, and whether it is going ahead.
 *
 * @param array $columns Columns.
 * @return array
 */
function cfhg_site_event_columns( $columns ) {
	$out = array();
	foreach ( $columns as $key => $label ) {
		if ( 'date' === $key ) {
			continue; // The publish date is meaningless for events.
		}
		$out[ $key ] = $label;
		if ( 'title' === $key ) {
			$out['cfhg_when']   = __( 'When', 'cfhg-site' );
			$out['cfhg_status'] = __( 'Status', 'cfhg-site' );
		}
	}
	return $out;
}
add_filter( 'manage_cfhg_event_posts_columns', 'cfhg_site_event_columns' );

/**
 * Fill the list columns.
 *
 * @param string $column  Column.
 * @param int    $post_id Event ID.
 */
function cfhg_site_event_column( $column, $post_id ) {
	$e = cfhg_site_event( $post_id );
	if ( ! $e ) {
		return;
	}
	if ( 'cfhg_when' === $column ) {
		echo $e['date_text'] ? esc_html( $e['date_text'] . ( $e['time_text'] ? ', ' . $e['time_text'] : '' ) ) : '<em>' . esc_html__( 'No date', 'cfhg-site' ) . '</em>';
	}
	if ( 'cfhg_status' === $column ) {
		if ( $e['cancelled'] ) {
			echo '<span style="color:#d63638;font-weight:600">' . esc_html__( 'Cancelled', 'cfhg-site' ) . '</span>';
		} elseif ( $e['is_past'] ) {
			echo esc_html( $e['has_writeup'] ? __( 'Past, has a write-up', 'cfhg-site' ) : __( 'Past', 'cfhg-site' ) );
		} else {
			esc_html_e( 'Upcoming', 'cfhg-site' );
		}
	}
}
add_action( 'manage_cfhg_event_posts_custom_column', 'cfhg_site_event_column', 10, 2 );

/**
 * Sortable "When" column.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function cfhg_site_event_sortable( $columns ) {
	$columns['cfhg_when'] = 'cfhg_when';
	return $columns;
}
add_filter( 'manage_edit-cfhg_event_sortable_columns', 'cfhg_site_event_sortable' );

/**
 * "Upcoming" and "Past" views above the list.
 *
 * @param array $views Views.
 * @return array
 */
function cfhg_site_event_views( $views ) {
	$current = isset( $_GET['cfhg_when'] ) ? sanitize_key( wp_unslash( $_GET['cfhg_when'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$base    = admin_url( 'edit.php?post_type=cfhg_event' );
	$out     = array(
		'cfhg_upcoming' => sprintf( '<a href="%1$s"%2$s>%3$s</a>', esc_url( add_query_arg( 'cfhg_when', 'upcoming', $base ) ), 'upcoming' === $current ? ' class="current" aria-current="page"' : '', esc_html__( 'Upcoming', 'cfhg-site' ) ),
		'cfhg_past'     => sprintf( '<a href="%1$s"%2$s>%3$s</a>', esc_url( add_query_arg( 'cfhg_when', 'past', $base ) ), 'past' === $current ? ' class="current" aria-current="page"' : '', esc_html__( 'Past', 'cfhg-site' ) ),
	);
	return $out + $views;
}
add_filter( 'views_edit-cfhg_event', 'cfhg_site_event_views' );

/**
 * Order the events list by date, and filter it for the two views.
 *
 * @param WP_Query $query Query.
 */
function cfhg_site_event_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'cfhg_event' !== $query->get( 'post_type' ) ) {
		return;
	}
	$orderby = $query->get( 'orderby' );
	if ( ! $orderby || 'cfhg_when' === $orderby ) {
		$query->set( 'meta_key', '_cfhg_sort' );
		$query->set( 'orderby', 'meta_value' );
		if ( ! $query->get( 'order' ) || ! isset( $_GET['order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query->set( 'order', 'ASC' );
		}
	}
	$when = isset( $_GET['cfhg_when'] ) ? sanitize_key( wp_unslash( $_GET['cfhg_when'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $when, array( 'upcoming', 'past' ), true ) ) {
		$query->set(
			'meta_query',
			array(
				array(
					'key'     => '_cfhg_ends_at',
					'value'   => cfhg_site_now(),
					'compare' => 'upcoming' === $when ? '>=' : '<',
				),
			)
		);
		if ( 'past' === $when && ! isset( $_GET['order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query->set( 'order', 'DESC' );
		}
	}
}
add_action( 'pre_get_posts', 'cfhg_site_event_admin_query' );

/**
 * Events > Settings.
 */
function cfhg_site_settings_menu() {
	add_submenu_page( 'edit.php?post_type=cfhg_event', __( 'Event settings', 'cfhg-site' ), __( 'Settings', 'cfhg-site' ), 'manage_options', 'cfhg-event-settings', 'cfhg_site_settings_page' );
}
add_action( 'admin_menu', 'cfhg_site_settings_menu', 20 );

/**
 * Register the settings.
 */
function cfhg_site_register_settings() {
	register_setting(
		'cfhg_site_settings',
		'cfhg_site_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => static function ( $value ) {
				$value = (array) $value;
				return array(
					'next_group' => isset( $value['next_group'] ) ? sanitize_title( $value['next_group'] ) : 'main',
					'venue'      => isset( $value['venue'] ) ? sanitize_text_field( $value['venue'] ) : '',
					'online'     => isset( $value['online'] ) ? sanitize_text_field( $value['online'] ) : '',
					'address'    => isset( $value['address'] ) ? sanitize_text_field( $value['address'] ) : '',
				);
			},
		)
	);
}
add_action( 'admin_init', 'cfhg_site_register_settings' );

/**
 * The settings page.
 */
function cfhg_site_settings_page() {
	$s      = cfhg_site_settings();
	$groups = get_terms(
		array(
			'taxonomy'   => 'cfhg_event_group',
			'hide_empty' => false,
		)
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Event settings', 'cfhg-site' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'cfhg_site_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="cfhg_next_group"><?php esc_html_e( 'Shown as “Next meeting”', 'cfhg-site' ); ?></label></th>
					<td>
						<select id="cfhg_next_group" name="cfhg_site_settings[next_group]">
							<?php
							foreach ( (array) $groups as $group ) {
								printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $group->slug ), selected( $s['next_group'], $group->slug, false ), esc_html( $group->name ) );
							}
							?>
						</select>
						<p class="description"><?php esc_html_e( 'The homepage, menu and footer show this group’s next event. If it has no upcoming events, they fall back to the usual pattern set in Appearance > Customize > Meetings and prices.', 'cfhg-site' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cfhg_venue_default"><?php esc_html_e( 'Usual venue', 'cfhg-site' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cfhg_venue_default" name="cfhg_site_settings[venue]" value="<?php echo esc_attr( $s['venue'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="cfhg_address"><?php esc_html_e( 'Venue address', 'cfhg-site' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cfhg_address" name="cfhg_site_settings[address]" value="<?php echo esc_attr( $s['address'] ); ?>"><p class="description"><?php esc_html_e( 'Used in calendar files and for search engines.', 'cfhg-site' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="cfhg_online_default"><?php esc_html_e( 'Usual wording for joining online', 'cfhg-site' ); ?></label></th>
					<td><input type="text" class="regular-text" id="cfhg_online_default" name="cfhg_site_settings[online]" value="<?php echo esc_attr( $s['online'] ); ?>"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
