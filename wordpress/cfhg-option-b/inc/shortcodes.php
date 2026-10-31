<?php
/**
 * Shortcodes (Option B).
 *
 * These let Elementor (free) show the theme's live and designed content with
 * its Shortcode widget.
 *
 *   [cfhg_next_meeting style="inline|title|facts"]
 *   [cfhg_hero_proof]
 *   [cfhg_prices]
 *   [cfhg_numbers]
 *   [cfhg_steps]
 *   [cfhg_islands]
 *   [cfhg_stories count="3"]
 *   [cfhg_partners]
 *   [cfhg_snapshot image="intro_photo_1" tone="coral" shape="portrait"]
 *   [cfhg_shape name="hibiscus|palm-frond|wave"]
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
	add_shortcode( 'cfhg_hero_proof', 'cfhg_component_hero_proof' );
	add_shortcode( 'cfhg_prices', 'cfhg_component_prices' );
	add_shortcode( 'cfhg_numbers', 'cfhg_component_numbers' );
	add_shortcode( 'cfhg_steps', 'cfhg_component_steps' );
	add_shortcode( 'cfhg_islands', 'cfhg_component_islands' );
	add_shortcode( 'cfhg_partners', 'cfhg_component_partners' );
	add_shortcode(
		'cfhg_stories',
		static function ( $atts ) {
			$atts = shortcode_atts( array( 'count' => 3 ), $atts, 'cfhg_stories' );
			return cfhg_component_stories( absint( $atts['count'] ) );
		}
	);
	add_shortcode(
		'cfhg_snapshot',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'image' => 'intro_photo_1',
					'tone'  => 'coral',
					'shape' => 'portrait',
				),
				$atts,
				'cfhg_snapshot'
			);
			$allowed = array_keys( cfhg_image_defaults() );
			$image   = in_array( $atts['image'], $allowed, true ) ? $atts['image'] : 'intro_photo_1';
			return cfhg_component_snapshot( $image, sanitize_key( $atts['tone'] ), sanitize_key( $atts['shape'] ) );
		}
	);
	add_shortcode(
		'cfhg_shape',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'name'  => 'hibiscus',
					'class' => '',
				),
				$atts,
				'cfhg_shape'
			);
			return cfhg_shape( $atts['name'], sanitize_html_class( $atts['class'] ) );
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
