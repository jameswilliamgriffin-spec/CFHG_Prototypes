<?php
/**
 * Calendar files (iCalendar, .ics): "Add to my calendar" for one event, and
 * a feed of all upcoming events that phones and calendar apps can subscribe
 * to, so members get new and changed dates automatically.
 *
 *   /?cfhg_ics=123    one event
 *   /?cfhg_ics=feed   every upcoming event, plus the last three months
 *
 * Times are written in UTC, which every calendar app understands without a
 * timezone table. No Zoom links are included: the joining wording is.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Address of a calendar file.
 *
 * @param int|string $what Event ID, or "feed".
 * @return string
 */
function cfhg_site_ics_url( $what ) {
	return add_query_arg( 'cfhg_ics', 'feed' === $what ? 'feed' : absint( $what ), home_url( '/' ) );
}

/**
 * Let WordPress see the ?cfhg_ics= parameter.
 *
 * @param array $vars Query vars.
 * @return array
 */
function cfhg_site_ics_query_var( $vars ) {
	$vars[] = 'cfhg_ics';
	return $vars;
}
add_filter( 'query_vars', 'cfhg_site_ics_query_var' );

/**
 * Escape text for iCalendar.
 *
 * @param string $text Text.
 * @return string
 */
function cfhg_site_ics_text( $text ) {
	$text = wp_strip_all_tags( html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' ) );
	return str_replace( array( '\\', ';', ',', "\r\n", "\n" ), array( '\\\\', '\;', '\,', '\n', '\n' ), $text );
}

/**
 * Fold a line at 75 bytes, as iCalendar requires, without splitting a
 * multi-byte character.
 *
 * @param string $line Line.
 * @return string
 */
function cfhg_site_ics_fold( $line ) {
	$out = '';
	while ( strlen( $line ) > 75 ) {
		$cut = 75;
		while ( $cut > 0 && ( ord( $line[ $cut ] ) & 0xC0 ) === 0x80 ) {
			--$cut;
		}
		$out .= substr( $line, 0, $cut ) . "\r\n ";
		$line = substr( $line, $cut );
	}
	return $out . $line . "\r\n";
}

/**
 * One VEVENT.
 *
 * @param array $e Event.
 * @return string
 */
function cfhg_site_ics_event( $e ) {
	$tz       = wp_timezone();
	$utc      = new DateTimeZone( 'UTC' );
	$settings = cfhg_site_settings();
	$start    = ( new DateTimeImmutable( $e['starts_at'], $tz ) )->setTimezone( $utc );
	$end      = ( new DateTimeImmutable( $e['ends_at'], $tz ) )->setTimezone( $utc );
	$where    = 'online' === $e['attend'] ? __( 'Online, on Zoom', 'cfhg-site' ) : trim( $e['venue'] . ( $settings['address'] ? ', ' . $settings['address'] : '' ) );
	$notes    = trim( $e['summary'] . "\n\n" . ( $e['online'] ? $e['online'] . "\n" : '' ) . $e['url'] );
	$modified = get_post_modified_time( 'Ymd\THis\Z', true, $e['id'] );

	$lines = array(
		'BEGIN:VEVENT',
		'UID:cfhg-event-' . $e['id'] . '@' . wp_parse_url( home_url(), PHP_URL_HOST ),
		'DTSTAMP:' . $modified,
		'LAST-MODIFIED:' . $modified,
		'DTSTART:' . $start->format( 'Ymd\THis\Z' ),
		'DTEND:' . $end->format( 'Ymd\THis\Z' ),
		'SUMMARY:' . cfhg_site_ics_text( ( $e['cancelled'] ? __( 'CANCELLED: ', 'cfhg-site' ) : '' ) . $e['title'] ),
		'LOCATION:' . cfhg_site_ics_text( $where ),
		'DESCRIPTION:' . cfhg_site_ics_text( $notes ),
		'URL:' . esc_url_raw( $e['url'] ),
		'STATUS:' . ( $e['cancelled'] ? 'CANCELLED' : 'CONFIRMED' ),
		'END:VEVENT',
	);
	return implode( '', array_map( 'cfhg_site_ics_fold', $lines ) );
}

/**
 * Serve a calendar file.
 */
function cfhg_site_ics_serve() {
	$what = get_query_var( 'cfhg_ics' );
	if ( '' === $what || null === $what ) {
		return;
	}
	$name = get_bloginfo( 'name' );
	if ( 'feed' === $what ) {
		$events = array_merge(
			array_reverse( cfhg_site_events( array( 'when' => 'past', 'limit' => 6 ) ) ),
			cfhg_site_events( array( 'when' => 'upcoming', 'limit' => 100 ) )
		);
		$file   = 'cfhg-meetings.ics';
	} else {
		$e      = cfhg_site_event( absint( $what ) );
		$events = $e && 'publish' === get_post_status( $e['id'] ) && $e['starts_at'] ? array( $e ) : array();
		$file   = $events ? sanitize_title( $e['title'] . '-' . $e['date_iso'] ) . '.ics' : '';
	}
	if ( 'feed' !== $what && ! $events ) {
		status_header( 404 );
		nocache_headers();
		exit;
	}

	$body = cfhg_site_ics_fold( 'BEGIN:VCALENDAR' )
		. cfhg_site_ics_fold( 'VERSION:2.0' )
		. cfhg_site_ics_fold( 'PRODID:-//' . cfhg_site_ics_text( $name ) . '//CFHG Site//EN' )
		. cfhg_site_ics_fold( 'CALSCALE:GREGORIAN' )
		. cfhg_site_ics_fold( 'METHOD:PUBLISH' )
		. cfhg_site_ics_fold( 'X-WR-CALNAME:' . cfhg_site_ics_text( $name ) )
		. cfhg_site_ics_fold( 'X-PUBLISHED-TTL:PT12H' );
	foreach ( $events as $e ) {
		if ( $e['starts_at'] ) {
			$body .= cfhg_site_ics_event( $e );
		}
	}
	$body .= cfhg_site_ics_fold( 'END:VCALENDAR' );

	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: ' . ( 'feed' === $what ? 'inline' : 'attachment' ) . '; filename="' . $file . '"' );
	header( 'Cache-Control: max-age=3600' );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- iCalendar, escaped per field.
	exit;
}
add_action( 'template_redirect', 'cfhg_site_ics_serve', 1 );
