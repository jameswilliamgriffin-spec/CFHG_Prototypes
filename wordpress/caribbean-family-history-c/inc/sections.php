<?php
/**
 * Homepage sections and their shortcodes.
 *
 * Each section is a template part in template-parts/home/. front-page.php
 * prints them in order. The same parts are available as shortcodes, so in
 * Elementor any section can be placed with a Shortcode widget:
 *
 *   [cfhg_c_section name="islands"]
 *
 * plus three smaller pieces for building sections by hand:
 *
 *   [cfhg_c_islands]        the island scroller on its own
 *   [cfhg_c_next_meeting]   the next-meeting card
 *   [cfhg_c_meetings]       the list of upcoming dates
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage sections in display order.
 *
 * @return string[]
 */
function cfhg_c_home_sections() {
	/**
	 * Filter the homepage sections (template-parts/home/{name}.php).
	 *
	 * @param string[] $sections Section names.
	 */
	return (array) apply_filters( 'cfhg_c_home_sections', array( 'hero', 'intro', 'islands', 'ways', 'whats-on', 'support', 'news' ) );
}

/**
 * Capture a template part as a string.
 *
 * @param string $slug Template part slug.
 * @param array  $args Arguments.
 * @return string
 */
function cfhg_c_capture_part( $slug, $args = array() ) {
	ob_start();
	get_template_part( $slug, null, $args );
	return (string) ob_get_clean();
}

/**
 * [cfhg_c_section name="..."]
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_c_section_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'name' => '' ), $atts, 'cfhg_c_section' );
	$name = sanitize_key( str_replace( '_', '-', $atts['name'] ) );

	if ( ! in_array( $name, array( 'hero', 'intro', 'islands', 'ways', 'whats-on', 'support', 'news' ), true ) ) {
		return '';
	}

	return cfhg_c_capture_part( 'template-parts/home/' . $name );
}
add_shortcode( 'cfhg_c_section', 'cfhg_c_section_shortcode' );

/**
 * [cfhg_c_islands]
 *
 * @return string
 */
function cfhg_c_islands_shortcode() {
	return cfhg_c_capture_part( 'template-parts/components/island-scroller', array( 'heading_id' => '' ) );
}
add_shortcode( 'cfhg_c_islands', 'cfhg_c_islands_shortcode' );

/**
 * [cfhg_c_next_meeting]
 *
 * @return string
 */
function cfhg_c_next_meeting_shortcode() {
	return cfhg_c_capture_part( 'template-parts/components/meeting-card' );
}
add_shortcode( 'cfhg_c_next_meeting', 'cfhg_c_next_meeting_shortcode' );

/**
 * [cfhg_c_meetings]
 *
 * @return string
 */
function cfhg_c_meetings_shortcode() {
	return cfhg_c_capture_part( 'template-parts/components/programme' );
}
add_shortcode( 'cfhg_c_meetings', 'cfhg_c_meetings_shortcode' );
