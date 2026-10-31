<?php
/**
 * Shortcodes.
 *
 * These let Elementor (free) show the theme's live content with its Shortcode
 * widget: the next meeting date is always correct, and the story list pulls
 * from real posts once they exist.
 *
 *   [cfhg_next_meeting style="inline|big|facts"]
 *   [cfhg_prices]
 *   [cfhg_islands]
 *   [cfhg_steps]
 *   [cfhg_stories count="3"]
 *   [cfhg_partners]
 *   [cfhg_email]
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register shortcodes.
 */
function cfhg_register_shortcodes() {
	add_shortcode(
		'cfhg_next_meeting',
		static function ( $atts ) {
			$atts = shortcode_atts( array( 'style' => 'inline' ), $atts, 'cfhg_next_meeting' );
			return cfhg_component_next_meeting( sanitize_key( $atts['style'] ) );
		}
	);
	add_shortcode( 'cfhg_prices', 'cfhg_component_prices' );
	add_shortcode( 'cfhg_islands', 'cfhg_component_islands' );
	add_shortcode( 'cfhg_steps', 'cfhg_component_steps' );
	add_shortcode( 'cfhg_partners', 'cfhg_component_partners' );
	add_shortcode(
		'cfhg_stories',
		static function ( $atts ) {
			$atts = shortcode_atts( array( 'count' => 3 ), $atts, 'cfhg_stories' );
			return cfhg_component_stories( absint( $atts['count'] ) );
		}
	);
	add_shortcode(
		'cfhg_email',
		static function () {
			$href = cfhg_mailto();
			return $href ? '<a href="' . esc_url( $href ) . '">' . cfhg_email_display() . '</a>' : '';
		}
	);
}
add_action( 'init', 'cfhg_register_shortcodes' );
