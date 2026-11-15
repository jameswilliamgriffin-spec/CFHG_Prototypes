<?php
/**
 * Events on the site: the [cfhg_events] list, the theme's "Next meeting",
 * and event details for search engines.
 *
 * The plugin prints plain, classed markup; the CFHG themes style it
 * (.cfhg-events, .cfhg-event). With any other theme it still reads as a
 * sensible list.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * One event in a list.
 *
 * @param array $e          Event (from cfhg_site_event()).
 * @param bool  $show_group Say which group, when it isn't the main one.
 * @return string
 */
function cfhg_site_event_item( $e, $show_group = true ) {
	/* Two plain lines: when, then where. No separators to read around. */
	$when = '<time datetime="' . esc_attr( $e['date_iso'] . ( $e['start'] ? 'T' . $e['start'] : '' ) ) . '">' . esc_html( $e['date_text'] ) . '</time>';
	if ( $e['time_text'] ) {
		$when .= ', ' . esc_html( $e['time_text'] );
	}
	$meta = '<span class="cfhg-event__when">' . $when . '</span>';
	if ( $e['where'] ) {
		$meta .= '<span class="cfhg-event__where">' . esc_html( $e['where'] ) . '</span>';
	}
	$group = '';
	if ( $show_group && $e['group'] && cfhg_site_settings()['next_group'] !== $e['group']->slug ) {
		$group = '<span class="cfhg-event__group">' . esc_html( $e['group']->name ) . '</span>';
	}

	$flag = '';
	if ( $e['cancelled'] ) {
		$flag = '<span class="cfhg-event__flag is-cancelled">' . esc_html__( 'Cancelled', 'cfhg-site' ) . '</span>';
	} elseif ( $e['is_past'] && $e['has_writeup'] ) {
		$flag = '<span class="cfhg-event__flag">' . esc_html__( 'Read what happened', 'cfhg-site' ) . '</span>';
	}

	$classes = 'cfhg-event' . ( $e['cancelled'] ? ' is-cancelled' : '' ) . ( $e['is_past'] ? ' is-past' : '' );
	return sprintf(
		'<li class="%1$s"><a class="cfhg-event__link" href="%2$s"><span class="cfhg-event__badge" aria-hidden="true"><span class="cfhg-event__day">%3$s</span><span class="cfhg-event__month">%4$s</span></span><span class="cfhg-event__body"><span class="cfhg-event__title">%5$s</span><span class="cfhg-event__meta">%6$s</span>%7$s%8$s</span></a></li>',
		esc_attr( $classes ),
		esc_url( $e['url'] ),
		esc_html( $e['day'] ),
		esc_html( $e['month'] ),
		esc_html( $e['title'] ),
		$meta,
		$e['summary'] && ! $e['is_past'] ? '<span class="cfhg-event__summary">' . esc_html( $e['summary'] ) . '</span>' : '',
		$group . $flag
	);
}

/**
 * [cfhg_events when="upcoming" limit="10" group="" island="" type="" empty="…"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_events_shortcode( $atts ) {
	$atts   = shortcode_atts(
		array(
			'when'       => 'upcoming',
			'limit'      => 10,
			'group'      => '',
			'island'     => '',
			'type'       => '',
			'show_group' => 'yes',
			'empty'      => '',
		),
		$atts,
		'cfhg_events'
	);
	$when   = in_array( $atts['when'], array( 'upcoming', 'past', 'all' ), true ) ? $atts['when'] : 'upcoming';
	$events = cfhg_site_events(
		array(
			'when'   => $when,
			'limit'  => (int) $atts['limit'],
			'group'  => $atts['group'],
			'island' => $atts['island'],
			'type'   => $atts['type'],
		)
	);
	if ( ! $events && 'none' === $atts['empty'] ) {
		return '';
	}
	if ( ! $events ) {
		$empty = '' !== $atts['empty'] ? $atts['empty'] : ( 'past' === $when ? __( 'No past events yet.', 'cfhg-site' ) : __( 'No events are planned yet. Check back soon, or email us.', 'cfhg-site' ) );
		return '<p class="cfhg-events__empty">' . esc_html( $empty ) . '</p>';
	}
	$items = '';
	foreach ( $events as $e ) {
		$items .= cfhg_site_event_item( $e, 'no' !== $atts['show_group'] );
	}
	return '<ul class="cfhg-events cfhg-events--' . esc_attr( $when ) . '">' . $items . '</ul>';
}

/**
 * [cfhg_calendar_links]: "Subscribe to our calendar" for phones and
 * calendar apps.
 *
 * @return string
 */
function cfhg_site_calendar_links_shortcode() {
	$feed = cfhg_site_ics_url( 'feed' );
	return '<p class="cfhg-events__subscribe"><a href="' . esc_url( preg_replace( '#^https?://#', 'webcal://', $feed ), array( 'webcal' ) ) . '">' . esc_html__( 'Add all our meetings to your calendar', 'cfhg-site' ) . '</a> <span>' . esc_html__( '(they update by themselves)', 'cfhg-site' ) . '</span></p>';
}

/**
 * Register the shortcodes.
 */
function cfhg_site_shortcodes() {
	add_shortcode( 'cfhg_events', 'cfhg_site_events_shortcode' );
	add_shortcode( 'cfhg_calendar_links', 'cfhg_site_calendar_links_shortcode' );
}
add_action( 'init', 'cfhg_site_shortcodes' );

/**
 * Give the theme's "Next meeting" the real next event. The CFHG themes call
 * the cfhg_next_meeting_info filter; when the group shown as "Next meeting"
 * has no upcoming events, the theme's own pattern is left as it was.
 *
 * @param array $info date (DateTimeImmutable), time, where, url.
 * @return array
 */
function cfhg_site_next_meeting_info( $info ) {
	$e = cfhg_site_next_event();
	if ( ! $e || ! $e['date'] ) {
		return $info;
	}
	return array_merge(
		(array) $info,
		array(
			'date'  => $e['date'],
			'time'  => $e['time_text'],
			'where' => $e['where'],
			'url'   => $e['url'],
			'title' => $e['title'],
		)
	);
}
add_filter( 'cfhg_next_meeting_info', 'cfhg_site_next_meeting_info' );

/**
 * Event details for search engines (schema.org Event), on an event's page.
 */
function cfhg_site_event_schema() {
	if ( ! is_singular( 'cfhg_event' ) ) {
		return;
	}
	$e = cfhg_site_event( get_queried_object_id() );
	if ( ! $e || ! $e['starts_at'] ) {
		return;
	}
	$tz       = wp_timezone();
	$settings = cfhg_site_settings();
	$start    = new DateTimeImmutable( $e['starts_at'], $tz );
	$end      = new DateTimeImmutable( $e['ends_at'], $tz );
	$place    = array(
		'@type'   => 'Place',
		'name'    => $e['venue'],
		'address' => $settings['address'],
	);
	$online   = array(
		'@type' => 'VirtualLocation',
		'url'   => $e['url'],
	);
	$modes    = array(
		'library'   => 'OfflineEventAttendanceMode',
		'elsewhere' => 'OfflineEventAttendanceMode',
		'online'    => 'OnlineEventAttendanceMode',
		'both'      => 'MixedEventAttendanceMode',
	);
	$data     = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'Event',
		'name'                => $e['title'],
		'startDate'           => $start->format( 'c' ),
		'endDate'             => $end->format( 'c' ),
		'eventStatus'         => 'https://schema.org/' . ( $e['cancelled'] ? 'EventCancelled' : 'EventScheduled' ),
		'eventAttendanceMode' => 'https://schema.org/' . $modes[ $e['attend'] ],
		'location'            => 'online' === $e['attend'] ? $online : ( 'both' === $e['attend'] ? array( $place, $online ) : $place ),
		'organizer'           => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'url'                 => $e['url'],
	);
	if ( $e['summary'] ) {
		$data['description'] = $e['summary'];
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}
add_action( 'wp_head', 'cfhg_site_event_schema' );
