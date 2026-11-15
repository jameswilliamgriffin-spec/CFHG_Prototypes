<?php
/**
 * Content types: Events, and the groups, types and islands they belong to.
 *
 * - Events (cfhg_event) live at /whats-on/event-name/. There is no automatic
 *   archive page: "What's On" is an ordinary page that lists events with the
 *   [cfhg_events] shortcode, so it can be designed freely (or in Elementor).
 * - Group (cfhg_event_group): the main group or a sub-group. The group
 *   chosen in Events > Settings is the one shown as "Next meeting".
 * - Type (cfhg_event_type): meeting, talk, exhibition, social, fair.
 * - Island (cfhg_island): shared by events and posts, so an island page can
 *   show everything about that island. Has public pages at /island/jamaica/.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Event fields, stored as post meta. Keys start with an underscore so they
 * stay out of WordPress's generic Custom Fields box.
 *
 * @return array<string, array{type: string, default: string}>
 */
function cfhg_site_event_fields() {
	return array(
		'_cfhg_date'    => array( 'type' => 'string', 'default' => '' ),          // Y-m-d.
		'_cfhg_start'   => array( 'type' => 'string', 'default' => '' ),          // H:i.
		'_cfhg_end'     => array( 'type' => 'string', 'default' => '' ),          // H:i.
		'_cfhg_attend'  => array( 'type' => 'string', 'default' => 'both' ),      // library, online, both or elsewhere.
		'_cfhg_venue'   => array( 'type' => 'string', 'default' => '' ),
		'_cfhg_online'  => array( 'type' => 'string', 'default' => '' ),
		'_cfhg_status'  => array( 'type' => 'string', 'default' => 'scheduled' ), // scheduled or cancelled.
		'_cfhg_sort'    => array( 'type' => 'string', 'default' => '' ),          // Y-m-d H:i, start. Set on save.
		'_cfhg_ends_at' => array( 'type' => 'string', 'default' => '' ),          // Y-m-d H:i, end. Set on save.
	);
}

/**
 * Register Events, the three taxonomies and the event fields.
 */
function cfhg_site_register_content() {
	register_post_type(
		'cfhg_event',
		array(
			'labels'        => array(
				'name'               => __( 'Events', 'cfhg-site' ),
				'singular_name'      => __( 'Event', 'cfhg-site' ),
				'add_new'            => __( 'Add event', 'cfhg-site' ),
				'add_new_item'       => __( 'Add an event', 'cfhg-site' ),
				'edit_item'          => __( 'Edit event', 'cfhg-site' ),
				'new_item'           => __( 'New event', 'cfhg-site' ),
				'view_item'          => __( 'View event', 'cfhg-site' ),
				'search_items'       => __( 'Search events', 'cfhg-site' ),
				'not_found'          => __( 'No events found.', 'cfhg-site' ),
				'not_found_in_trash' => __( 'No events in the bin.', 'cfhg-site' ),
				'all_items'          => __( 'All events', 'cfhg-site' ),
				'menu_name'          => __( 'Events', 'cfhg-site' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-calendar-alt',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'rewrite'       => array(
				'slug'       => 'whats-on',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'cfhg_event_group',
		array( 'cfhg_event' ),
		array(
			'labels'            => array(
				'name'          => __( 'Groups', 'cfhg-site' ),
				'singular_name' => __( 'Group', 'cfhg-site' ),
				'add_new_item'  => __( 'Add a group', 'cfhg-site' ),
				'edit_item'     => __( 'Edit group', 'cfhg-site' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
		)
	);

	register_taxonomy(
		'cfhg_event_type',
		array( 'cfhg_event' ),
		array(
			'labels'            => array(
				'name'          => __( 'Event types', 'cfhg-site' ),
				'singular_name' => __( 'Event type', 'cfhg-site' ),
				'add_new_item'  => __( 'Add an event type', 'cfhg-site' ),
				'edit_item'     => __( 'Edit event type', 'cfhg-site' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
		)
	);

	register_taxonomy(
		'cfhg_island',
		array( 'cfhg_event', 'post', 'cfhg_surname' ),
		array(
			'labels'            => array(
				'name'          => __( 'Islands', 'cfhg-site' ),
				'singular_name' => __( 'Island', 'cfhg-site' ),
				'add_new_item'  => __( 'Add an island', 'cfhg-site' ),
				'edit_item'     => __( 'Edit island', 'cfhg-site' ),
				'search_items'  => __( 'Search islands', 'cfhg-site' ),
			),
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug'       => 'island',
				'with_front' => false,
			),
		)
	);

	foreach ( cfhg_site_event_fields() as $key => $field ) {
		register_post_meta(
			'cfhg_event',
			$key,
			array(
				'type'          => $field['type'],
				'single'        => true,
				'default'       => $field['default'],
				'show_in_rest'  => true,
				'auth_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'cfhg_site_register_content' );

/**
 * Starting groups, types and islands. Only adds what is missing, so it is
 * safe to run again; the group can rename or add to them in Events.
 */
function cfhg_site_seed_terms() {
	$terms = array(
		'cfhg_event_group' => array(
			'main'                => __( 'Main group', 'cfhg-site' ),
			'jamaica'             => __( 'Jamaica sub-group', 'cfhg-site' ),
			'barbados'            => __( 'Barbados sub-group', 'cfhg-site' ),
			'st-kitts-and-nevis'  => __( 'St Kitts and Nevis sub-group', 'cfhg-site' ),
		),
		'cfhg_event_type'  => array(
			'meeting'    => __( 'Meeting', 'cfhg-site' ),
			'talk'       => __( 'Talk', 'cfhg-site' ),
			'exhibition' => __( 'Exhibition', 'cfhg-site' ),
			'fair'       => __( 'Family history fair', 'cfhg-site' ),
			'social'     => __( 'Social', 'cfhg-site' ),
		),
		/* The islands the old site already tagged posts with. */
		'cfhg_island'      => array(
			'jamaica'             => __( 'Jamaica', 'cfhg-site' ),
			'barbados'            => __( 'Barbados', 'cfhg-site' ),
			'st-kitts-and-nevis'  => __( 'St Kitts and Nevis', 'cfhg-site' ),
			'trinidad-and-tobago' => __( 'Trinidad and Tobago', 'cfhg-site' ),
			'grenada'             => __( 'Grenada', 'cfhg-site' ),
			'guyana'              => __( 'Guyana', 'cfhg-site' ),
			'cuba'                => __( 'Cuba', 'cfhg-site' ),
		),
	);
	foreach ( $terms as $taxonomy => $list ) {
		foreach ( $list as $slug => $name ) {
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			}
		}
	}
}
