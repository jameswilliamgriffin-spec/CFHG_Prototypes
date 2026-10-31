<?php
/**
 * Theme image library.
 *
 * Every image the theme ships with is listed here with its real dimensions,
 * alt text and a focal point. Focal points are applied with object-position,
 * so photographs are framed by CSS rather than permanently cropped.
 *
 * Any of these can be replaced from the Customizer with a Media Library image.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Image manifest.
 *
 * @return array<string, array<string, mixed>>
 */
function cfhg_image_manifest() {
	return array(
		'family-album-caribbean-photographs'   => array(
			'sizes' => array( 640 => 800, 1100 => 1375 ),
			'alt'   => __( 'A child and a grandparent turning the pages of a family album of black and white photographs taken in the Caribbean.', 'cfhg' ),
			'focus' => '50% 45%',
		),
		'members-sharing-a-moment'             => array(
			'sizes' => array( 600 => 600 ),
			'alt'   => __( 'Two women, one younger and one older, smiling as they talk.', 'cfhg' ),
			'focus' => '50% 35%',
		),
		'meeting-at-the-library-table'         => array(
			'sizes' => array( 700 => 394, 1200 => 675 ),
			'alt'   => __( 'A group gathered round a library table, looking at an old photograph and taking notes.', 'cfhg' ),
			'focus' => '55% 40%',
		),
		'tea-break-at-a-meeting'               => array(
			'sizes' => array( 480 => 600, 800 => 1000 ),
			'alt'   => __( 'Two people chatting over cups of tea at a library table.', 'cfhg' ),
			'focus' => '50% 35%',
		),
		'courtyard-conversation'               => array(
			'sizes' => array( 520 => 520, 900 => 900 ),
			'alt'   => __( 'Two women deep in conversation outdoors, one holding a notebook.', 'cfhg' ),
			'focus' => '45% 40%',
		),
		'walking-after-a-meeting'              => array(
			'sizes' => array( 700 => 394, 1200 => 675 ),
			'alt'   => __( 'Three people deep in conversation as they walk together outdoors.', 'cfhg' ),
			'focus' => '50% 35%',
		),
		'comparing-notes-outdoors'             => array(
			'sizes' => array( 520 => 520, 900 => 900 ),
			'alt'   => __( 'An older and a younger woman laughing together as one writes in a notebook at an outdoor table.', 'cfhg' ),
			'focus' => '55% 35%',
		),
		'reading-between-library-shelves'      => array(
			'sizes' => array( 600 => 750, 1000 => 1250 ),
			'alt'   => __( 'A young woman and an older man reading a book together between library shelves.', 'cfhg' ),
			'focus' => '55% 35%',
		),
		'caribbean-map-research'               => array(
			'sizes' => array( 800 => 533, 1400 => 933 ),
			'alt'   => __( 'A hand marking a map of the Caribbean with a pencil, next to an open notebook.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'young-researcher-with-old-photograph' => array(
			'sizes' => array( 600 => 750, 1000 => 1250 ),
			'alt'   => __( 'A young woman studying an old family photograph at a library desk, with a notebook beside her.', 'cfhg' ),
			'focus' => '45% 40%',
		),
		'research-notes-and-old-photographs'   => array(
			'sizes' => array( 700 => 467, 1200 => 800 ),
			'alt'   => __( 'Black and white family photographs spread across a wooden table beside a handwritten notebook.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'researcher-taking-notes-from-records' => array(
			'sizes' => array( 520 => 520, 900 => 900 ),
			'alt'   => __( 'A researcher copying details from an old register into her notes.', 'cfhg' ),
			'focus' => '45% 40%',
		),
		'member-helping-with-research'         => array(
			'sizes' => array( 800 => 450, 1400 => 788 ),
			'alt'   => __( 'An older man pointing something out on a laptop to a woman he is helping with her research.', 'cfhg' ),
			'focus' => '55% 40%',
		),
		'three-generations-with-family-album'  => array(
			'sizes' => array( 800 => 450, 1400 => 788 ),
			'alt'   => __( 'A grandmother showing a family album to her son and granddaughter at the kitchen table.', 'cfhg' ),
			'focus' => '60% 40%',
		),
	);
}

/**
 * Which theme image each Customizer image setting falls back to.
 *
 * @return array<string, string>
 */
function cfhg_image_defaults() {
	return array(
		'hero_image'       => 'family-album-caribbean-photographs',
		'intro_tile_image' => 'members-sharing-a-moment',
		'strip_image_1'    => 'meeting-at-the-library-table',
		'strip_image_2'    => 'tea-break-at-a-meeting',
		'strip_image_3'    => 'courtyard-conversation',
		'strip_image_4'    => 'walking-after-a-meeting',
		'strip_image_5'    => 'comparing-notes-outdoors',
		'meeting_image'    => 'reading-between-library-shelves',
		'islands_image'    => 'caribbean-map-research',
		'start_image'      => 'young-researcher-with-old-photograph',
		'help_image'       => 'member-helping-with-research',
		'join_image'       => 'three-generations-with-family-album',
	);
}

/**
 * URL of the largest version of a theme image.
 *
 * @param string $slug Manifest key.
 * @return string
 */
function cfhg_theme_image_url( $slug ) {
	$manifest = cfhg_image_manifest();
	if ( empty( $manifest[ $slug ] ) ) {
		return '';
	}
	$widths = array_keys( $manifest[ $slug ]['sizes'] );
	return CFHG_URI . '/assets/images/' . $slug . '-' . max( $widths ) . '.jpg';
}

/**
 * Render a theme image with srcset, intrinsic size, alt text and focal point.
 *
 * @param string $slug Manifest key.
 * @param array  $args {
 *     @type string $class   Class for the img element.
 *     @type string $sizes   Sizes attribute.
 *     @type string $loading lazy or eager.
 *     @type string $alt     Override alt text. Pass an empty string for decorative use.
 * }
 * @return string
 */
function cfhg_theme_image( $slug, $args = array() ) {
	$manifest = cfhg_image_manifest();
	if ( empty( $manifest[ $slug ] ) ) {
		return '';
	}
	$item = $manifest[ $slug ];
	$args = wp_parse_args(
		$args,
		array(
			'class'   => '',
			'sizes'   => '100vw',
			'loading' => 'lazy',
			'alt'     => null,
		)
	);

	ksort( $item['sizes'] );
	$srcset = array();
	foreach ( $item['sizes'] as $w => $h ) {
		$srcset[] = esc_url( CFHG_URI . '/assets/images/' . $slug . '-' . $w . '.jpg' ) . ' ' . (int) $w . 'w';
	}
	$largest_w = max( array_keys( $item['sizes'] ) );
	$largest_h = $item['sizes'][ $largest_w ];
	$alt       = null === $args['alt'] ? $item['alt'] : $args['alt'];

	return sprintf(
		'<img src="%1$s" srcset="%2$s" sizes="%3$s" width="%4$d" height="%5$d" alt="%6$s" class="%7$s" style="object-position:%8$s" loading="%9$s" decoding="async"%10$s>',
		esc_url( cfhg_theme_image_url( $slug ) ),
		esc_attr( implode( ', ', $srcset ) ),
		esc_attr( $args['sizes'] ),
		(int) $largest_w,
		(int) $largest_h,
		esc_attr( $alt ),
		esc_attr( $args['class'] ),
		esc_attr( $item['focus'] ),
		esc_attr( $args['loading'] ),
		'eager' === $args['loading'] ? ' fetchpriority="high"' : ''
	);
}

/**
 * Render the image for a Customizer image setting: the editor's chosen
 * Media Library image if there is one, otherwise the theme default.
 *
 * @param string $setting Customizer setting key, e.g. hero_image.
 * @param array  $args    See cfhg_theme_image().
 * @return string
 */
function cfhg_setting_image( $setting, $args = array() ) {
	$attachment_id = absint( get_theme_mod( $setting, 0 ) );
	$args          = wp_parse_args(
		$args,
		array(
			'class'   => '',
			'sizes'   => '100vw',
			'loading' => 'lazy',
		)
	);

	if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
		$attrs = array(
			'class'   => $args['class'],
			'sizes'   => $args['sizes'],
			'loading' => $args['loading'],
		);
		if ( isset( $args['alt'] ) ) {
			$attrs['alt'] = $args['alt'];
		}
		return wp_get_attachment_image( $attachment_id, 'large', false, $attrs );
	}

	$defaults = cfhg_image_defaults();
	return isset( $defaults[ $setting ] ) ? cfhg_theme_image( $defaults[ $setting ], $args ) : '';
}

/**
 * Horizontal logo markup: the Site Identity logo if set, else the theme logo.
 *
 * @param string $class Extra class.
 * @return string
 */
function cfhg_logo_markup( $class = '' ) {
	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		return wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class' => trim( 'cfhg-logo ' . $class ),
				'alt'   => get_bloginfo( 'name', 'display' ),
			)
		);
	}
	return sprintf(
		'<img src="%1$s" width="798" height="240" alt="%2$s" class="%3$s">',
		esc_url( CFHG_URI . '/assets/images/cfhg-logo-horizontal.png' ),
		esc_attr__( 'Caribbean Family History Group', 'cfhg' ),
		esc_attr( trim( 'cfhg-logo ' . $class ) )
	);
}
