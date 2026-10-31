<?php
/**
 * Small helpers used across templates.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a theme setting, falling back to the default copy.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function cfhg_mod( $key ) {
	$defaults = cfhg_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( $key, $default );
}

/**
 * Turn a stored link into a full URL. Site-relative paths ("/join/") are
 * resolved against the home URL, so links survive a move between domains.
 * Anchors, mailto: and full URLs are passed through.
 *
 * @param string $url Stored link.
 * @return string
 */
function cfhg_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( '#' === $url[0] || preg_match( '#^(https?:|mailto:|tel:)#i', $url ) ) {
		return $url;
	}
	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * mailto: link for the group's email address.
 *
 * @return string
 */
function cfhg_mailto() {
	$email = sanitize_email( cfhg_mod( 'contact_email' ) );
	return $email ? 'mailto:' . $email : '';
}

/**
 * The group's email address for display, lightly obfuscated against scrapers.
 * antispambot() returns HTML entities only, so it is safe to print directly.
 *
 * @return string
 */
function cfhg_email_display() {
	$email = sanitize_email( cfhg_mod( 'contact_email' ) );
	return $email ? antispambot( $email ) : '';
}

/**
 * Split a list setting into rows of parts. One row per line, parts separated by |.
 *
 * @param string $key   Setting key.
 * @param int    $parts Number of parts expected per row.
 * @return array<int, string[]>
 */
function cfhg_lines( $key, $parts ) {
	$rows  = array();
	$lines = preg_split( '/\r\n|\r|\n/', (string) cfhg_mod( $key ) );
	foreach ( $lines as $line ) {
		if ( '' === trim( $line ) ) {
			continue;
		}
		$bits   = array_map( 'trim', explode( '|', $line ) );
		$rows[] = array_pad( array_slice( $bits, 0, $parts ), $parts, '' );
	}
	return $rows;
}

/**
 * Inline HTML allowed in headings and short text.
 *
 * @return array
 */
function cfhg_inline_kses() {
	return array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array( 'class' => true ),
		'a'      => array(
			'href'  => true,
			'class' => true,
		),
	);
}

/**
 * Escape a short text setting, keeping simple inline formatting and line breaks.
 *
 * @param string $text Text.
 * @return string
 */
function cfhg_text( $text ) {
	return nl2br( wp_kses( (string) $text, cfhg_inline_kses() ), false );
}

/**
 * Work out the next group meeting.
 *
 * Meetings are on the last Saturday of the month, except in the months listed
 * in the "Months with no meeting" setting (May, August and December by
 * default). An override date in the Customizer wins if it is today or later.
 * On the day itself, that day is still shown as the next meeting.
 *
 * @return DateTimeImmutable|null
 */
function cfhg_next_meeting() {
	$tz    = wp_timezone();
	$today = new DateTimeImmutable( 'today', $tz );

	$override = trim( (string) cfhg_mod( 'meeting_override_date' ) );
	if ( $override ) {
		$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $override, $tz );
		if ( $date && $date >= $today ) {
			return $date;
		}
	}

	$skip  = array_filter( array_map( 'intval', explode( ',', (string) cfhg_mod( 'meeting_skip_months' ) ) ) );
	$first = $today->modify( 'first day of this month' );

	for ( $i = 0; $i < 14; $i++ ) {
		$month = $first->modify( '+' . $i . ' months' );
		if ( in_array( (int) $month->format( 'n' ), $skip, true ) ) {
			continue;
		}
		$meeting = $month->modify( 'last saturday of this month' );
		if ( $meeting >= $today ) {
			return $meeting;
		}
	}

	return null;
}

/**
 * Format the next meeting date.
 *
 * @param string $format PHP date format.
 * @return string
 */
function cfhg_next_meeting_date( $format = 'l j F' ) {
	$date = cfhg_next_meeting();
	return $date ? wp_date( $format, $date->getTimestamp(), wp_timezone() ) : '';
}

/**
 * Is this post built with Elementor?
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function cfhg_is_built_with_elementor( $post_id ) {
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Homepage sections that are switched on, in order.
 *
 * @return string[]
 */
function cfhg_home_sections() {
	$sections = array();
	foreach ( cfhg_home_section_keys() as $key ) {
		if ( cfhg_mod( 'show_' . $key ) ) {
			$sections[] = $key;
		}
	}
	return apply_filters( 'cfhg_home_sections', $sections );
}

/**
 * Right-pointing arrow used in buttons and links.
 *
 * @return string
 */
function cfhg_arrow() {
	return '<svg class="cfhg-arrow" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false"><path d="M3 10h13M11 4.5 16.5 10 11 15.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Button markup.
 *
 * Variants:
 * - "solid": the main call to action. A navy pill with a gold disc.
 * - "disc": a label with a round arrow disc, for secondary actions.
 * - "text": an underlined text link.
 *
 * @param string $label   Visible label.
 * @param string $url     Link (relative paths allowed).
 * @param string $variant solid, disc or text.
 * @param string $class   Extra classes.
 * @return string
 */
function cfhg_button( $label, $url, $variant = 'disc', $class = '' ) {
	$href = cfhg_url( $url );
	if ( '' === trim( (string) $label ) || '' === $href ) {
		return '';
	}

	$classes = trim( 'cfhg-btn cfhg-btn--' . $variant . ' ' . $class );

	if ( 'text' === $variant ) {
		return sprintf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			esc_attr( $classes ),
			esc_url( $href ),
			esc_html( $label )
		);
	}

	return sprintf(
		'<a class="%1$s" href="%2$s"><span class="cfhg-btn__disc" aria-hidden="true">%3$s</span><span class="cfhg-btn__label">%4$s</span></a>',
		esc_attr( $classes ),
		esc_url( $href ),
		cfhg_arrow(),
		esc_html( $label )
	);
}

/**
 * Print the section-reveal attribute. Kept in one place so it can be removed easily.
 */
function cfhg_reveal() {
	echo ' data-reveal';
}
