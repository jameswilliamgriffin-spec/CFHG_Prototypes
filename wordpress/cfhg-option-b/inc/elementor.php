<?php
/**
 * Elementor integration.
 *
 * - Header and footer can be replaced with Elementor Pro's Theme Builder.
 * - The theme fonts are self-hosted and registered with Elementor, so nothing
 *   is fetched from Google's servers (better for UK GDPR).
 * - The design tokens can be written into Elementor's Global Colours and
 *   Global Fonts. tokens.css reads those globals back, so changing a global
 *   colour in Elementor also changes the theme's own templates.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * The design tokens, in the shape Elementor stores them.
 * Keep these in sync with assets/css/tokens.css (the fallback values there).
 *
 * @return array
 */
function cfhg_design_tokens() {
	return array(
		'colors'     => array(
			/* id => array( title, hex ). The id becomes --e-global-color-{id}. Prefixed cfb (Option B). */
			'cfbcrem' => array( __( 'CFHG B Cream', 'cfhg' ), '#F6F1EA' ),
			'cfbsand' => array( __( 'CFHG B Sand', 'cfhg' ), '#ECE4D9' ),
			'cfbwhit' => array( __( 'CFHG B White', 'cfhg' ), '#FFFFFF' ),
			'cfbinkk' => array( __( 'CFHG B Ink', 'cfhg' ), '#10213B' ),
			'cfbsoft' => array( __( 'CFHG B Ink soft', 'cfhg' ), '#4B5567' ),
			'cfbcorl' => array( __( 'CFHG B Coral', 'cfhg' ), '#F26B57' ),
			'cfbgold' => array( __( 'CFHG B Gold', 'cfhg' ), '#F6C343' ),
			'cfbhigh' => array( __( 'CFHG B Highlighter', 'cfhg' ), '#FFE873' ),
			'cfbgren' => array( __( 'CFHG B Green', 'cfhg' ), '#2FA36B' ),
			'cfbdeep' => array( __( 'CFHG B Green deep', 'cfhg' ), '#137A4D' ),
			'cfbseaa' => array( __( 'CFHG B Sea', 'cfhg' ), '#45BCD0' ),
			'cfbblue' => array( __( 'CFHG B Blue', 'cfhg' ), '#5B8DEF' ),
			'cfbline' => array( __( 'CFHG B Line', 'cfhg' ), '#D9CFC1' ),
		),
		/*
		 * Elementor's own widgets default to these: Heading uses Primary,
		 * Text Editor uses Text, Button uses Accent.
		 */
		'system'     => array(
			'primary'   => array( __( 'Primary (headings)', 'cfhg' ), '#10213B' ),
			'secondary' => array( __( 'Secondary (green)', 'cfhg' ), '#137A4D' ),
			'text'      => array( __( 'Text', 'cfhg' ), '#10213B' ),
			'accent'    => array( __( 'Accent (buttons)', 'cfhg' ), '#137A4D' ),
		),
		/* id => array( title, family, weight, desktop px, tablet px, mobile px, line-height em, letter-spacing em, transform ) */
		'typography' => array(
			'primary'   => array( __( 'Heading', 'cfhg' ), 'Jost', '400', 64, 48, 34, 0.95, 0, 'uppercase' ),
			'secondary' => array( __( 'Display', 'cfhg' ), 'Jost', '400', 104, 76, 46, 0.9, -0.01, 'uppercase' ),
			'text'      => array( __( 'Body', 'cfhg' ), 'Outfit', '300', 17, 17, 17, 1.5, 0.005, '' ),
			'accent'    => array( __( 'Label', 'cfhg' ), 'Jost', '500', 15, 15, 14, 1.1, 0.02, 'uppercase' ),
			'cfblead'   => array( __( 'Lead', 'cfhg' ), 'Outfit', '300', 22, 20, 19, 1.35, 0, '' ),
			'cfbnumb'   => array( __( 'Numbers', 'cfhg' ), 'Jost', '400', 96, 80, 64, 0.9, -0.02, '' ),
		),
	);
}

/**
 * Register header and footer locations for Elementor Pro's Theme Builder.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function cfhg_elementor_locations( $manager ) {
	$manager->register_core_location( 'header' );
	$manager->register_core_location( 'footer' );
}
add_action( 'elementor/theme/register_locations', 'cfhg_elementor_locations' );

/**
 * List the self-hosted fonts in Elementor's font picker as system fonts,
 * so Elementor does not try to load them from Google.
 *
 * @param array $fonts Additional fonts.
 * @return array
 */
function cfhg_elementor_fonts( $fonts ) {
	$fonts['Jost']   = 'system';
	$fonts['Outfit'] = 'system';
	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'cfhg_elementor_fonts' );

/**
 * Stop Elementor printing Google Fonts links. The theme fonts are served from
 * the theme folder. Remove this filter if editors want other Google fonts.
 */
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/**
 * Elementor's default content width, matched to the theme container.
 *
 * @return bool
 */
function cfhg_elementor_is_active() {
	return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
}

/**
 * ID of Elementor's active kit (where global colours and fonts live).
 *
 * @return int
 */
function cfhg_elementor_kit_id() {
	return (int) get_option( 'elementor_active_kit' );
}

/**
 * Has the active kit already got the CFHG colours?
 *
 * @return bool
 */
function cfhg_elementor_kit_is_synced() {
	$kit_id = cfhg_elementor_kit_id();
	if ( ! $kit_id ) {
		return false;
	}
	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	if ( empty( $settings['custom_colors'] ) || ! is_array( $settings['custom_colors'] ) ) {
		return false;
	}
	foreach ( $settings['custom_colors'] as $color ) {
		if ( isset( $color['_id'] ) && 'cfbcrem' === $color['_id'] ) {
			return true;
		}
	}
	return false;
}

/**
 * Build one Elementor typography entry.
 *
 * @param string $id  Global id.
 * @param array  $def Definition from cfhg_design_tokens().
 * @return array
 */
function cfhg_elementor_typography_entry( $id, $def ) {
	list( $title, $family, $weight, $desk, $tab, $mob, $lh, $ls, $transform ) = $def;
	$entry = array(
		'_id'                         => $id,
		'title'                       => $title,
		'typography_typography'       => 'custom',
		'typography_font_family'      => $family,
		'typography_font_weight'      => $weight,
		'typography_font_size'        => array( 'unit' => 'px', 'size' => $desk ),
		'typography_font_size_tablet' => array( 'unit' => 'px', 'size' => $tab ),
		'typography_font_size_mobile' => array( 'unit' => 'px', 'size' => $mob ),
		'typography_line_height'      => array( 'unit' => 'em', 'size' => $lh ),
		'typography_letter_spacing'   => array( 'unit' => 'em', 'size' => $ls ),
	);
	if ( $transform ) {
		$entry['typography_text_transform'] = $transform;
	}
	return $entry;
}

/**
 * Write the design tokens into Elementor's active kit.
 *
 * Existing custom colours and fonts that are not CFHG ones are kept.
 *
 * @return bool True on success.
 */
function cfhg_elementor_apply_tokens() {
	$kit_id = cfhg_elementor_kit_id();
	if ( ! $kit_id || ! cfhg_elementor_is_active() ) {
		return false;
	}

	$tokens   = cfhg_design_tokens();
	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	$settings['system_colors'] = array();
	foreach ( $tokens['system'] as $id => $def ) {
		$settings['system_colors'][] = array(
			'_id'   => $id,
			'title' => $def[0],
			'color' => $def[1],
		);
	}

	$custom = array();
	foreach ( (array) ( isset( $settings['custom_colors'] ) ? $settings['custom_colors'] : array() ) as $color ) {
		if ( isset( $color['_id'] ) && ! isset( $tokens['colors'][ $color['_id'] ] ) ) {
			$custom[] = $color;
		}
	}
	foreach ( $tokens['colors'] as $id => $def ) {
		$custom[] = array(
			'_id'   => $id,
			'title' => $def[0],
			'color' => $def[1],
		);
	}
	$settings['custom_colors'] = $custom;

	$system_type = array();
	$custom_type = array();
	foreach ( (array) ( isset( $settings['custom_typography'] ) ? $settings['custom_typography'] : array() ) as $type ) {
		if ( isset( $type['_id'] ) && ! isset( $tokens['typography'][ $type['_id'] ] ) ) {
			$custom_type[] = $type;
		}
	}
	foreach ( $tokens['typography'] as $id => $def ) {
		$entry = cfhg_elementor_typography_entry( $id, $def );
		if ( in_array( $id, array( 'primary', 'secondary', 'text', 'accent' ), true ) ) {
			$system_type[] = $entry;
		} else {
			$custom_type[] = $entry;
		}
	}
	$settings['system_typography'] = $system_type;
	$settings['custom_typography'] = $custom_type;

	/* Body text and links, so Elementor's own widgets match the theme. */
	$settings['body_typography_typography']  = 'custom';
	$settings['body_typography_font_family'] = 'Outfit';
	$settings['body_typography_font_weight'] = '300';
	$settings['body_color']                  = '#10213B';
	$settings['body_background_background'] = 'classic';
	$settings['body_background_color']       = '#F6F1EA';
	$settings['__globals__']                 = array_merge(
		isset( $settings['__globals__'] ) && is_array( $settings['__globals__'] ) ? $settings['__globals__'] : array(),
		array(
			'body_color'            => 'globals/colors?id=cfbinkk',
			'body_background_color' => 'globals/colors?id=cfbcrem',
			'link_normal_color'     => 'globals/colors?id=cfbinkk',
			'link_hover_color'      => 'globals/colors?id=cfbdeep',
		)
	);
	$settings['container_width'] = array(
		'unit' => 'px',
		'size' => 1600,
	);

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );

	if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	return true;
}

/**
 * Apply the tokens automatically the first time the theme is switched on,
 * but only if the kit has not been set up yet. Never overwrites later edits.
 */
function cfhg_elementor_apply_tokens_on_activation() {
	if ( cfhg_elementor_is_active() && ! cfhg_elementor_kit_is_synced() ) {
		cfhg_elementor_apply_tokens();
	}
}
add_action( 'after_switch_theme', 'cfhg_elementor_apply_tokens_on_activation' );
