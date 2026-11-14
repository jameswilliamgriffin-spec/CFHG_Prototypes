<?php
/**
 * Reading events: settings, one event's details, and lists of events.
 *
 * Every event has a date, optional start and finish times, where it happens
 * and whether it is going ahead. Two sortable keys are worked out on save:
 * _cfhg_sort (when it starts) and _cfhg_ends_at (when it finishes), both
 * "Y-m-d H:i" in the site's timezone. An event counts as upcoming until it
 * has finished, so on the day it still shows as the next meeting.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings (Events > Settings), with defaults.
 *
 * @return array<string, string>
 */
function cfhg_site_settings() {
	$theme_venue  = function_exists( 'cfhg_mod' ) ? (string) cfhg_mod( 'meeting_venue' ) : '';
	$theme_online = function_exists( 'cfhg_mod' ) ? (string) cfhg_mod( 'meeting_online' ) : '';
	return wp_parse_args(
		(array) get_option( 'cfhg_site_settings', array() ),
		array(
			'next_group' => 'main',
			'venue'      => $theme_venue ? $theme_venue : __( 'Solihull Core Library', 'cfhg-site' ),
			'online'     => $theme_online ? $theme_online : __( 'On Zoom. Email us and we’ll send you the link.', 'cfhg-site' ),
			'address'    => 'Solihull, B91 3RG',
		)
	);
}

/**
 * The "Where" choices.
 *
 * @return array<string, string>
 */
function cfhg_site_attend_options() {
	return array(
		'both'      => __( 'At the library and on Zoom', 'cfhg-site' ),
		'library'   => __( 'At the library only', 'cfhg-site' ),
		'online'    => __( 'On Zoom only', 'cfhg-site' ),
		'elsewhere' => __( 'Somewhere else (type the venue)', 'cfhg-site' ),
	);
}

/**
 * The current time in the site's timezone, as the sort keys store it.
 *
 * @return string Y-m-d H:i.
 */
function cfhg_site_now() {
	return current_time( 'Y-m-d H:i' );
}

/**
 * "14:00" to "2pm", "14:30" to "2:30pm".
 *
 * @param string $time H:i.
 * @return string
 */
function cfhg_site_format_time( $time ) {
	$t = DateTimeImmutable::createFromFormat( '!H:i', (string) $time, wp_timezone() );
	if ( ! $t ) {
		return '';
	}
	return '00' === $t->format( 'i' ) ? $t->format( 'ga' ) : $t->format( 'g:ia' );
}

/**
 * "2pm to 4pm", "from 2pm", or "" when no time is set.
 *
 * @param string $start H:i.
 * @param string $end   H:i.
 * @return string
 */
function cfhg_site_time_range( $start, $end ) {
	$s = cfhg_site_format_time( $start );
	$e = cfhg_site_format_time( $end );
	if ( $s && $e ) {
		/* translators: 1: start time, 2: finish time. */
		return sprintf( __( '%1$s to %2$s', 'cfhg-site' ), $s, $e );
	}
	/* translators: %s: start time. */
	return $s ? sprintf( __( 'from %s', 'cfhg-site' ), $s ) : '';
}

/**
 * The short form of a venue: everything before the first comma.
 * "Solihull Core Library, first floor computer room" becomes
 * "Solihull Core Library".
 *
 * @param string $venue Venue.
 * @return string
 */
function cfhg_site_venue_short( $venue ) {
	$parts = explode( ',', (string) $venue );
	return trim( $parts[0] );
}

/**
 * Work out and store the two sort keys after an event is saved.
 *
 * @param int $post_id Event ID.
 */
function cfhg_site_update_sort_keys( $post_id ) {
	$date  = (string) get_post_meta( $post_id, '_cfhg_date', true );
	$start = (string) get_post_meta( $post_id, '_cfhg_start', true );
	$end   = (string) get_post_meta( $post_id, '_cfhg_end', true );

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
		delete_post_meta( $post_id, '_cfhg_sort' );
		delete_post_meta( $post_id, '_cfhg_ends_at' );
		return;
	}
	$start_key = $date . ' ' . ( preg_match( '/^\d{2}:\d{2}$/', $start ) ? $start : '00:00' );
	if ( preg_match( '/^\d{2}:\d{2}$/', $end ) ) {
		$end_key = $date . ' ' . $end;
	} elseif ( preg_match( '/^\d{2}:\d{2}$/', $start ) ) {
		/* No finish time: assume two hours, like a normal meeting. */
		$end_key = ( new DateTimeImmutable( $start_key, wp_timezone() ) )->modify( '+2 hours' )->format( 'Y-m-d H:i' );
	} else {
		$end_key = $date . ' 23:59';
	}
	update_post_meta( $post_id, '_cfhg_sort', $start_key );
	update_post_meta( $post_id, '_cfhg_ends_at', $end_key );
}

/**
 * Everything about one event, ready to show.
 *
 * @param int|WP_Post $post Event.
 * @return array<string, mixed>|null
 */
function cfhg_site_event( $post ) {
	$post = get_post( $post );
	if ( ! $post || 'cfhg_event' !== $post->post_type ) {
		return null;
	}
	$id       = $post->ID;
	$settings = cfhg_site_settings();
	$tz       = wp_timezone();

	$date_raw = (string) get_post_meta( $id, '_cfhg_date', true );
	$start    = (string) get_post_meta( $id, '_cfhg_start', true );
	$end      = (string) get_post_meta( $id, '_cfhg_end', true );
	$date     = $date_raw ? DateTimeImmutable::createFromFormat( '!Y-m-d', $date_raw, $tz ) : null;
	$attend   = (string) get_post_meta( $id, '_cfhg_attend', true );
	$attend   = in_array( $attend, array( 'library', 'online', 'both', 'elsewhere' ), true ) ? $attend : 'both';
	$venue    = trim( (string) get_post_meta( $id, '_cfhg_venue', true ) );
	$venue    = '' === $venue && 'online' !== $attend ? $settings['venue'] : $venue;
	$online   = trim( (string) get_post_meta( $id, '_cfhg_online', true ) );
	$online   = '' === $online && in_array( $attend, array( 'online', 'both' ), true ) ? $settings['online'] : $online;

	switch ( $attend ) {
		case 'online':
			$where = __( 'Online, on Zoom', 'cfhg-site' );
			break;
		case 'both':
			/* translators: %s: venue, short form. */
			$where = sprintf( __( '%s and Zoom', 'cfhg-site' ), cfhg_site_venue_short( $venue ) );
			break;
		default:
			$where = cfhg_site_venue_short( $venue );
	}

	$groups  = get_the_terms( $id, 'cfhg_event_group' );
	$types   = get_the_terms( $id, 'cfhg_event_type' );
	$islands = get_the_terms( $id, 'cfhg_island' );

	return array(
		'id'          => $id,
		'title'       => get_the_title( $post ),
		'url'         => get_permalink( $post ),
		'date'        => $date ? $date : null,
		'date_iso'    => $date ? $date->format( 'Y-m-d' ) : '',
		'date_text'   => $date ? wp_date( 'l j F Y', $date->getTimestamp(), $tz ) : '',
		'day'         => $date ? wp_date( 'j', $date->getTimestamp(), $tz ) : '',
		'month'       => $date ? wp_date( 'M', $date->getTimestamp(), $tz ) : '',
		'start'       => $start,
		'end'         => $end,
		'time_text'   => cfhg_site_time_range( $start, $end ),
		'starts_at'   => (string) get_post_meta( $id, '_cfhg_sort', true ),
		'ends_at'     => (string) get_post_meta( $id, '_cfhg_ends_at', true ),
		'attend'      => $attend,
		'venue'       => $venue,
		'online'      => $online,
		'where'       => $where,
		'cancelled'   => 'cancelled' === get_post_meta( $id, '_cfhg_status', true ),
		'group'       => is_array( $groups ) ? $groups[0] : null,
		'type'        => is_array( $types ) ? $types[0] : null,
		'islands'     => is_array( $islands ) ? $islands : array(),
		'summary'     => has_excerpt( $post ) ? get_the_excerpt( $post ) : '',
		'has_writeup' => '' !== trim( wp_strip_all_tags( $post->post_content ) ),
		'is_past'     => (string) get_post_meta( $id, '_cfhg_ends_at', true ) < cfhg_site_now(),
	);
}

/**
 * Find events.
 *
 * @param array $args {
 *     @type string $when      upcoming (default), past or all.
 *     @type int    $limit     How many. Default 10; -1 for all.
 *     @type string $group     Group slug(s), comma-separated.
 *     @type string $island    Island slug(s), comma-separated.
 *     @type string $type      Type slug(s), comma-separated.
 *     @type bool   $cancelled Include cancelled events. Default true.
 * }
 * @return array<int, array<string, mixed>>
 */
function cfhg_site_events( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'when'      => 'upcoming',
			'limit'     => 10,
			'group'     => '',
			'island'    => '',
			'type'      => '',
			'cancelled' => true,
		)
	);

	$now        = cfhg_site_now();
	$meta_query = array(
		array(
			'key'     => '_cfhg_sort',
			'compare' => 'EXISTS',
		),
	);
	if ( 'upcoming' === $args['when'] ) {
		$meta_query[] = array(
			'key'     => '_cfhg_ends_at',
			'value'   => $now,
			'compare' => '>=',
		);
	} elseif ( 'past' === $args['when'] ) {
		$meta_query[] = array(
			'key'     => '_cfhg_ends_at',
			'value'   => $now,
			'compare' => '<',
		);
	}
	if ( ! $args['cancelled'] ) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => '_cfhg_status',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_cfhg_status',
				'value'   => 'cancelled',
				'compare' => '!=',
			),
		);
	}

	$tax_query = array();
	foreach ( array( 'group' => 'cfhg_event_group', 'island' => 'cfhg_island', 'type' => 'cfhg_event_type' ) as $arg => $taxonomy ) {
		$slugs = array_filter( array_map( 'sanitize_title', explode( ',', (string) $args[ $arg ] ) ) );
		if ( $slugs ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $slugs,
			);
		}
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'cfhg_event',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) $args['limit'],
			'meta_key'            => '_cfhg_sort', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- small table.
			'orderby'             => 'meta_value',
			'order'               => 'past' === $args['when'] ? 'DESC' : 'ASC',
			'meta_query'          => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'tax_query'           => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	return array_values( array_filter( array_map( 'cfhg_site_event', $query->posts ) ) );
}

/**
 * The next event for the group shown as "Next meeting" (Events > Settings),
 * skipping cancelled ones.
 *
 * @param string $group Group slug; defaults to the setting.
 * @return array<string, mixed>|null
 */
function cfhg_site_next_event( $group = '' ) {
	$group  = $group ? $group : cfhg_site_settings()['next_group'];
	$events = cfhg_site_events(
		array(
			'when'      => 'upcoming',
			'limit'     => 1,
			'group'     => $group,
			'cancelled' => false,
		)
	);
	return $events ? $events[0] : null;
}

/**
 * Whether a group already has an event on a date (used by "Add a series"
 * so running it twice does not make duplicates).
 *
 * @param string $date  Y-m-d.
 * @param int    $group Group term ID, or 0 for any.
 * @return bool
 */
function cfhg_site_event_exists( $date, $group = 0 ) {
	$args = array(
		'post_type'      => 'cfhg_event',
		'post_status'    => array( 'publish', 'draft', 'future', 'pending', 'private' ),
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => '_cfhg_date',
				'value' => $date,
			),
		),
	);
	if ( $group ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'cfhg_event_group',
				'terms'    => array( (int) $group ),
			),
		);
	}
	return (bool) ( new WP_Query( $args ) )->posts;
}

/**
 * Create one event.
 *
 * @param array $data title, date (Y-m-d), start, end, attend, venue, online,
 *                    group (term ID), type (term ID), island (term ID), status.
 * @return int|WP_Error
 */
function cfhg_site_create_event( $data ) {
	$data = wp_parse_args(
		$data,
		array(
			'title'  => __( 'Meeting', 'cfhg-site' ),
			'date'   => '',
			'start'  => '',
			'end'    => '',
			'attend' => 'both',
			'venue'  => '',
			'online' => '',
			'group'  => 0,
			'type'   => 0,
			'island' => 0,
			'status' => 'scheduled',
		)
	);
	$id = wp_insert_post(
		array(
			'post_type'   => 'cfhg_event',
			'post_status' => 'publish',
			'post_title'  => $data['title'],
			'post_name'   => sanitize_title( $data['title'] . ' ' . $data['date'] ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	foreach ( array( 'date', 'start', 'end', 'attend', 'venue', 'online', 'status' ) as $key ) {
		update_post_meta( $id, '_cfhg_' . $key, $data[ $key ] );
	}
	foreach ( array( 'group' => 'cfhg_event_group', 'type' => 'cfhg_event_type', 'island' => 'cfhg_island' ) as $key => $taxonomy ) {
		if ( $data[ $key ] ) {
			wp_set_object_terms( $id, array( (int) $data[ $key ] ), $taxonomy );
		}
	}
	cfhg_site_update_sort_keys( $id );
	return $id;
}
