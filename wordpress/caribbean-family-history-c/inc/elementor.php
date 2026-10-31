<?php
/**
 * Elementor integration.
 *
 * - Theme Builder header and footer locations.
 * - The theme fonts registered as local fonts, Google Fonts loading off.
 * - Design tokens written into the active Elementor kit as global colours
 *   and global fonts (cfhg_c_sync_elementor_kit()).
 * - A homepage built from Elementor containers (cfhg_c_build_elementor_home()).
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is Elementor active?
 *
 * @return bool
 */
function cfhg_c_has_elementor() {
	return did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
}

/**
 * Header and footer locations for Elementor Pro's Theme Builder.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function cfhg_c_elementor_locations( $manager ) {
	$manager->register_core_location( 'header' );
	$manager->register_core_location( 'footer' );
}
add_action( 'elementor/theme/register_locations', 'cfhg_c_elementor_locations' );

/**
 * Print an Elementor location if one is assigned.
 *
 * @param string $location 'header' or 'footer'.
 * @return bool True when Elementor printed it.
 */
function cfhg_c_elementor_location( $location ) {
	return function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location );
}

/**
 * Offer the self-hosted fonts in Elementor's font picker without loading
 * them from Google.
 *
 * @param array $fonts Fonts.
 * @return array
 */
function cfhg_c_elementor_fonts( $fonts ) {
	$fonts['Newsreader']    = 'system';
	$fonts['Source Sans 3'] = 'system';
	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'cfhg_c_elementor_fonts' );
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/**
 * The design tokens, shared by the kit sync and the docs.
 *
 * Primary must be the dark text colour: Elementor's Heading widget uses
 * Primary by default. Accent is the button background, so it needs white
 * text to pass contrast; sea blue does (7.7:1).
 *
 * @return array{system_colors:array,custom_colors:array,system_typography:array}
 */
function cfhg_c_elementor_tokens() {
	$colour = function ( $id, $title, $hex ) {
		return array(
			'_id'   => $id,
			'title' => $title,
			'color' => $hex,
		);
	};
	$type = function ( $id, $title, $family, $weight, $extra = array() ) {
		return array_merge(
			array(
				'_id'                    => $id,
				'title'                  => $title,
				'typography_typography'  => 'custom',
				'typography_font_family' => $family,
				'typography_font_weight' => $weight,
			),
			$extra
		);
	};

	return array(
		'system_colors'     => array(
			$colour( 'primary', 'Primary', '#13202D' ),
			$colour( 'secondary', 'Secondary', '#00568F' ),
			$colour( 'text', 'Text', '#13202D' ),
			$colour( 'accent', 'Accent', '#00568F' ),
		),
		'custom_colors'     => array(
			$colour( 'cfcpaper', 'CFHG C Paper', '#FBFAF6' ),
			$colour( 'cfcgold', 'CFHG C Sun gold', '#F0B424' ),
			$colour( 'cfctint', 'CFHG C Shallows', '#DCEFF1' ),
			$colour( 'cfcteal', 'CFHG C Teal', '#18B4CC' ),
			$colour( 'cfcgreen', 'CFHG C Palm green', '#0B7A3E' ),
			$colour( 'cfcred', 'CFHG C Ring red', '#C8201F' ),
			$colour( 'cfcmuted', 'CFHG C Muted text', '#4A5560' ),
			$colour( 'cfcline', 'CFHG C Rule', '#D8D3C6' ),
		),
		'system_typography' => array(
			$type(
				'primary',
				'Primary',
				'Newsreader',
				'500',
				array(
					'typography_line_height'    => array( 'unit' => 'em', 'size' => 1.1 ),
					'typography_letter_spacing' => array( 'unit' => 'em', 'size' => -0.01 ),
				)
			),
			$type( 'secondary', 'Secondary', 'Source Sans 3', '600' ),
			$type(
				'text',
				'Text',
				'Source Sans 3',
				'400',
				array(
					'typography_font_size'   => array( 'unit' => 'rem', 'size' => 1.125 ),
					'typography_line_height' => array( 'unit' => 'em', 'size' => 1.6 ),
				)
			),
			$type(
				'accent',
				'Accent',
				'Source Sans 3',
				'600',
				array(
					'typography_text_transform' => 'uppercase',
					'typography_letter_spacing' => array( 'unit' => 'em', 'size' => 0.1 ),
					'typography_font_size'      => array( 'unit' => 'rem', 'size' => 0.8125 ),
				)
			),
		),
	);
}

/**
 * Write the tokens into the active Elementor kit (Site Settings > Global
 * Colors and Global Fonts). Other kit settings are left alone, and custom
 * colours added by an editor are kept.
 *
 * @return bool
 */
function cfhg_c_sync_elementor_kit() {
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id || ! get_post( $kit_id ) ) {
		return false;
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	$tokens = cfhg_c_elementor_tokens();

	$settings['system_colors']     = $tokens['system_colors'];
	$settings['system_typography'] = $tokens['system_typography'];

	$ours   = wp_list_pluck( $tokens['custom_colors'], '_id' );
	$theirs = array();
	foreach ( isset( $settings['custom_colors'] ) ? (array) $settings['custom_colors'] : array() as $existing ) {
		if ( isset( $existing['_id'] ) && ! in_array( $existing['_id'], $ours, true ) ) {
			$theirs[] = $existing;
		}
	}
	$settings['custom_colors'] = array_values( array_merge( $tokens['custom_colors'], $theirs ) );

	$settings['container_width'] = array(
		'unit' => 'px',
		'size' => 1200,
	);
	$settings['body_background_background'] = 'classic';
	$settings['body_background_color']      = '#FBFAF6';

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	return true;
}

/**
 * Sync the kit once when the theme is switched on, if Elementor is there.
 */
function cfhg_c_sync_on_activate() {
	if ( cfhg_c_has_elementor() ) {
		cfhg_c_sync_elementor_kit();
	}
}
add_action( 'after_switch_theme', 'cfhg_c_sync_on_activate', 20 );

/**
 * Let Elementor edit Islands.
 */
function cfhg_c_elementor_cpt_support() {
	$types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( is_array( $types ) && ! in_array( 'cfhg_c_island', $types, true ) ) {
		$types[] = 'cfhg_c_island';
		update_option( 'elementor_cpt_support', $types );
	}
}
add_action( 'after_switch_theme', 'cfhg_c_elementor_cpt_support' );

/**
 * Has this page been built with Elementor?
 *
 * @param int $post_id Post id.
 * @return bool
 */
function cfhg_c_is_elementor_page( $post_id ) {
	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/*
 * --------------------------------------------------------------------------
 * Homepage builder
 * --------------------------------------------------------------------------
 * Builds the homepage as Elementor containers and widgets that an editor can
 * change directly. Sections that pull live content (islands, meetings, news)
 * are Shortcode widgets, because they are lists rather than fixed text.
 */

/**
 * Seven-character element id, as Elementor uses.
 *
 * @return string
 */
function cfhg_c_el_id() {
	return substr( md5( wp_generate_password( 12, false ) ), 0, 7 );
}

/**
 * An Elementor container.
 *
 * @param array $settings Settings.
 * @param array $children Elements.
 * @param bool  $inner    Nested container.
 * @return array
 */
function cfhg_c_el_container( $settings, $children, $inner = false ) {
	return array(
		'id'       => cfhg_c_el_id(),
		'elType'   => 'container',
		'isInner'  => $inner,
		'settings' => $settings,
		'elements' => array_values( array_filter( $children ) ),
	);
}

/**
 * An Elementor widget.
 *
 * @param string $type     Widget type.
 * @param array  $settings Settings.
 * @return array
 */
function cfhg_c_el_widget( $type, $settings ) {
	return array(
		'id'         => cfhg_c_el_id(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/**
 * Heading widget.
 *
 * @param string $text  Text.
 * @param string $tag   h1 to h6, p.
 * @param string $class CSS classes.
 * @return array
 */
function cfhg_c_el_heading( $text, $tag = 'h2', $class = '' ) {
	return cfhg_c_el_widget(
		'heading',
		array(
			'title'        => $text,
			'header_size'  => $tag,
			'_css_classes' => $class,
		)
	);
}

/**
 * Text editor widget.
 *
 * @param string $html  HTML.
 * @param string $class CSS classes.
 * @return array
 */
function cfhg_c_el_text( $html, $class = '' ) {
	return cfhg_c_el_widget(
		'text-editor',
		array(
			'editor'       => $html,
			'_css_classes' => $class,
		)
	);
}

/**
 * Button widget, styled by the theme through its CSS class.
 *
 * @param array  $link    label + url.
 * @param string $variant outline, solid, gold, light.
 * @return array
 */
function cfhg_c_el_button( $link, $variant = 'outline' ) {
	return cfhg_c_el_widget(
		'button',
		array(
			'text'         => $link['label'],
			'link'         => array(
				'url'         => cfhg_c_url( $link['url'] ),
				'is_external' => '',
				'nofollow'    => '',
			),
			'_css_classes' => 'cfhg-c-el-btn cfhg-c-el-btn--' . $variant,
		)
	);
}

/**
 * Image widget pointing at a theme image (no media library entry needed).
 *
 * @param string $key   Theme image key.
 * @param string $alt   Alt text.
 * @param string $class CSS classes.
 * @return array
 */
function cfhg_c_el_image( $key, $alt, $class = '' ) {
	return cfhg_c_el_widget(
		'image',
		array(
			'image'        => array(
				'url' => cfhg_c_image_url( $key ),
				'id'  => '',
				'alt' => $alt,
			),
			'image_size'   => 'full',
			'_css_classes' => $class,
		)
	);
}

/**
 * Shortcode widget.
 *
 * @param string $shortcode Shortcode.
 * @return array
 */
function cfhg_c_el_shortcode( $shortcode ) {
	return cfhg_c_el_widget( 'shortcode', array( 'shortcode' => $shortcode ) );
}

/**
 * Outer section container: full width background, boxed content.
 *
 * @param string $class    Section class.
 * @param array  $children Elements.
 * @param array  $extra    Extra settings.
 * @return array
 */
function cfhg_c_el_section( $class, $children, $extra = array() ) {
	return cfhg_c_el_container(
		array_merge(
			array(
				'content_width'  => 'boxed',
				'flex_direction' => 'column',
				'css_classes'    => 'cfhg-c-el-section ' . $class,
				'padding'        => array(
					'unit'     => 'rem',
					'top'      => '5',
					'right'    => '1',
					'bottom'   => '5',
					'left'     => '1',
					'isLinked' => false,
				),
			),
			$extra
		),
		$children
	);
}

/**
 * Column container with a percentage width that stacks on mobile.
 *
 * @param int   $percent  Desktop width.
 * @param array $children Elements.
 * @param array $extra    Extra settings.
 * @return array
 */
function cfhg_c_el_column( $percent, $children, $extra = array() ) {
	return cfhg_c_el_container(
		array_merge(
			array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'width'          => array( 'unit' => '%', 'size' => $percent ),
				'width_tablet'   => array( 'unit' => '%', 'size' => 100 ),
				'width_mobile'   => array( 'unit' => '%', 'size' => 100 ),
			),
			$extra
		),
		$children,
		true
	);
}

/**
 * Row container that wraps on smaller screens.
 *
 * @param array  $children Elements.
 * @param string $class    CSS classes.
 * @param bool   $reverse  Reverse on desktop.
 * @return array
 */
function cfhg_c_el_row( $children, $class = '', $reverse = false ) {
	return cfhg_c_el_container(
		array(
			'content_width'         => 'full',
			'flex_direction'        => $reverse ? 'row-reverse' : 'row',
			'flex_direction_tablet' => 'column',
			'flex_direction_mobile' => 'column',
			'flex_wrap'             => 'nowrap',
			'flex_align_items'      => 'center',
			'flex_gap'              => array( 'unit' => 'rem', 'size' => 3, 'column' => '3', 'row' => '2', 'isLinked' => false ),
			'css_classes'           => $class,
		),
		$children,
		true
	);
}

/**
 * Elementor data for the homepage, built from content.json.
 *
 * @return array
 */
function cfhg_c_elementor_home_data() {
	$c    = cfhg_c_content( 'home' );
	$hero = $c['hero'];
	$ways = $c['ways'];

	$research_links = '<ul class="cfhg-c-index">';
	foreach ( $ways['research']['links'] as $link ) {
		$research_links .= '<li><a href="' . esc_url( cfhg_c_url( $link['url'] ) ) . '">' . esc_html( $link['label'] ) . '</a></li>';
	}
	$research_links .= '</ul>';

	$facts = '<dl class="cfhg-c-facts">';
	foreach ( $ways['meet']['facts'] as $fact ) {
		$facts .= '<div><dt>' . esc_html( $fact['term'] ) . '</dt><dd>' . esc_html( $fact['detail'] ) . '</dd></div>';
	}
	$facts .= '</dl>';

	return array(
		// Hero.
		cfhg_c_el_section(
			'cfhg-c-el-hero',
			array(
				cfhg_c_el_row(
					array(
						cfhg_c_el_column(
							58,
							array(
								cfhg_c_el_heading( $hero['eyebrow'], 'p', 'cfhg-c-eyebrow' ),
								cfhg_c_el_heading( $hero['title'], 'h1', 'cfhg-c-display' ),
								cfhg_c_el_text( '<p>' . esc_html( $hero['lead'] ) . '</p>', 'cfhg-c-lead' ),
								cfhg_c_el_container(
									array(
										'content_width'  => 'full',
										'flex_direction' => 'row',
										'flex_wrap'      => 'wrap',
										'flex_gap'       => array( 'unit' => 'rem', 'size' => 0.75, 'column' => '0.75', 'row' => '0.75', 'isLinked' => true ),
									),
									array(
										cfhg_c_el_button( $hero['primary'], 'gold' ),
										cfhg_c_el_button( $hero['secondary'], 'outline' ),
									),
									true
								),
							)
						),
						cfhg_c_el_column(
							42,
							array(
								cfhg_c_el_image( $hero['image'], $hero['image_alt'], 'cfhg-c-arch' ),
								cfhg_c_el_shortcode( '[cfhg_c_next_meeting]' ),
							),
							array( 'css_classes' => 'cfhg-c-el-hero__media' )
						),
					)
				),
			)
		),
		// Intro.
		cfhg_c_el_section(
			'cfhg-c-el-intro',
			array(
				cfhg_c_el_heading( $c['intro']['eyebrow'], 'p', 'cfhg-c-eyebrow' ),
				cfhg_c_el_heading( $c['intro']['statement'], 'h2', 'cfhg-c-statement' ),
				cfhg_c_el_text( '<p>' . esc_html( $c['intro']['body'] ) . '</p>' ),
				cfhg_c_el_button( $c['intro']['link'], 'text' ),
			),
			array(
				'boxed_width'      => array( 'unit' => 'px', 'size' => 860 ),
				'flex_align_items' => 'center',
			)
		),
		// Islands.
		cfhg_c_el_section(
			'cfhg-c-el-band',
			array(
				cfhg_c_el_heading( $c['islands']['eyebrow'], 'p', 'cfhg-c-eyebrow' ),
				cfhg_c_el_heading( $c['islands']['heading'], 'h2' ),
				cfhg_c_el_text( '<p>' . esc_html( $c['islands']['lead'] ) . '</p>', 'cfhg-c-lead' ),
				cfhg_c_el_shortcode( '[cfhg_c_islands]' ),
			),
			array(
				'background_background' => 'classic',
				'background_color'      => '#DCEFF1',
			)
		),
		// Three ways in.
		cfhg_c_el_section(
			'cfhg-c-el-ways',
			array(
				cfhg_c_el_heading( $ways['eyebrow'], 'p', 'cfhg-c-eyebrow' ),
				cfhg_c_el_heading( $ways['heading'], 'h2' ),
				cfhg_c_el_row(
					array(
						cfhg_c_el_column( 55, array( cfhg_c_el_image( $ways['meet']['image'], $ways['meet']['image_alt'] ) ) ),
						cfhg_c_el_column(
							45,
							array(
								cfhg_c_el_heading( $ways['meet']['label'], 'p', 'cfhg-c-eyebrow' ),
								cfhg_c_el_heading( $ways['meet']['heading'], 'h3' ),
								cfhg_c_el_text( '<p>' . esc_html( $ways['meet']['body'] ) . '</p>' . $facts ),
								cfhg_c_el_button( $ways['meet']['link'], 'solid' ),
							)
						),
					)
				),
				cfhg_c_el_row(
					array(
						cfhg_c_el_column( 45, array( cfhg_c_el_image( $ways['research']['image'], $ways['research']['image_alt'] ) ) ),
						cfhg_c_el_column(
							55,
							array(
								cfhg_c_el_heading( $ways['research']['label'], 'p', 'cfhg-c-eyebrow' ),
								cfhg_c_el_heading( $ways['research']['heading'], 'h3' ),
								cfhg_c_el_text( '<p>' . esc_html( $ways['research']['body'] ) . '</p>' . $research_links ),
							)
						),
					),
					'',
					true
				),
				cfhg_c_el_row(
					array(
						cfhg_c_el_column( 30, array( cfhg_c_el_image( $ways['share']['image'], $ways['share']['image_alt'] ) ) ),
						cfhg_c_el_column(
							70,
							array(
								cfhg_c_el_heading( $ways['share']['label'], 'p', 'cfhg-c-eyebrow' ),
								cfhg_c_el_heading( $ways['share']['heading'], 'h3' ),
								cfhg_c_el_text( '<p>' . esc_html( $ways['share']['body'] ) . '</p>' ),
								cfhg_c_el_button( $ways['share']['link'], 'outline' ),
							)
						),
					),
					'cfhg-c-el-share'
				),
			)
		),
		// What's on: a live list of dates.
		cfhg_c_el_section( 'cfhg-c-el-plain', array( cfhg_c_el_shortcode( '[cfhg_c_section name="whats-on"]' ) ), array( 'padding' => array( 'unit' => 'rem', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ) ) ),
		// Support.
		cfhg_c_el_section( 'cfhg-c-el-plain', array( cfhg_c_el_shortcode( '[cfhg_c_section name="support"]' ) ), array( 'content_width' => 'full', 'padding' => array( 'unit' => 'rem', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ) ) ),
		// News: latest posts.
		cfhg_c_el_section( 'cfhg-c-el-plain', array( cfhg_c_el_shortcode( '[cfhg_c_section name="news"]' ) ), array( 'padding' => array( 'unit' => 'rem', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ) ) ),
	);
}

/**
 * Create (or rebuild) the homepage as an Elementor page.
 *
 * @param bool $set_front Make it the static front page.
 * @return int|WP_Error Page id.
 */
function cfhg_c_build_elementor_home( $set_front = true ) {
	$existing = (int) get_option( 'cfhg_c_elementor_home_id' );
	$page_id  = ( $existing && get_post( $existing ) ) ? $existing : 0;

	$postarr = array(
		'post_title'   => __( 'Home', 'caribbean-family-history-c' ),
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_content' => '',
	);
	if ( $page_id ) {
		$postarr['ID'] = $page_id;
		$page_id       = wp_update_post( $postarr, true );
	} else {
		$page_id = wp_insert_post( $postarr, true );
	}
	if ( is_wp_error( $page_id ) ) {
		return $page_id;
	}

	update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( cfhg_c_elementor_home_data() ) ) );
	delete_post_meta( $page_id, '_elementor_css' );
	update_option( 'cfhg_c_elementor_home_id', $page_id );

	if ( $set_front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );
	}

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	return $page_id;
}
