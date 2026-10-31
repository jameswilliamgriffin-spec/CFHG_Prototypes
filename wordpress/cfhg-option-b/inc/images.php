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
		'three-generations-with-an-album-on-the-sofa' => array(
			'sizes' => array( 900 => 507, 1600 => 900 ),
			'alt'   => __( 'A grandmother, her daughter and her grandson looking through a photo album together on the sofa.', 'cfhg' ),
			'focus' => '60% 45%',
		),
		'sharing-an-old-photograph-on-a-bench'        => array(
			'sizes' => array( 480 => 600, 800 => 1000 ),
			'alt'   => __( 'An older man in a flat cap showing an old photograph to a woman beside him on a park bench.', 'cfhg' ),
			'focus' => '50% 40%',
		),
		'hands-holding-a-family-album'                => array(
			'sizes' => array( 600 => 400, 1000 => 667 ),
			'alt'   => __( 'Hands holding open a family album of black and white photographs.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'group-at-a-library-table'                    => array(
			'sizes' => array( 700 => 394, 1200 => 675 ),
			'alt'   => __( 'A group gathered round a library table, looking at an old photograph and taking notes.', 'cfhg' ),
			'focus' => '55% 40%',
		),
		'marking-a-caribbean-map'                     => array(
			'sizes' => array( 600 => 400, 1000 => 667 ),
			'alt'   => __( 'A hand marking a map of the Caribbean with a pencil, next to an open notebook.', 'cfhg' ),
			'focus' => '55% 50%',
		),
		'helping-with-research-at-a-laptop'           => array(
			'sizes' => array( 700 => 394, 1200 => 675 ),
			'alt'   => __( 'An older man pointing something out on a laptop to a woman he is helping with her research.', 'cfhg' ),
			'focus' => '55% 40%',
		),
		'local-history-walk'                          => array(
			'sizes' => array( 900 => 507, 1600 => 900 ),
			'alt'   => __( 'A small group on a local history walk, one woman pointing at something along the street.', 'cfhg' ),
			'focus' => '55% 40%',
		),
		'conversation-at-dusk'                        => array(
			'sizes' => array( 900 => 450, 1600 => 800 ),
			'alt'   => __( 'An older man smiling as he talks with a woman in a softly lit room.', 'cfhg' ),
			'focus' => '60% 45%',
		),
		'copying-details-from-a-register'             => array(
			'sizes' => array( 520 => 520, 900 => 900 ),
			'alt'   => __( 'A researcher copying details from an old register into her notes.', 'cfhg' ),
			'focus' => '45% 40%',
		),
		'avatar-flat-cap'                             => array(
			'sizes' => array( 160 => 160 ),
			'alt'   => __( 'An older man in a flat cap, smiling.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'avatar-album'                                => array(
			'sizes' => array( 160 => 160 ),
			'alt'   => __( 'A woman with curly hair looking down at a photo album.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'avatar-smiling'                              => array(
			'sizes' => array( 160 => 160 ),
			'alt'   => __( 'An older man with a grey beard laughing.', 'cfhg' ),
			'focus' => '50% 50%',
		),
		'notebook-and-old-photographs'                => array(
			'sizes' => array( 700 => 467, 1200 => 800 ),
			'alt'   => __( 'Black and white family photographs spread across a wooden table beside a handwritten notebook.', 'cfhg' ),
			'focus' => '50% 50%',
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
		'hero_image'      => 'three-generations-with-an-album-on-the-sofa',
		'intro_photo_1'   => 'sharing-an-old-photograph-on-a-bench',
		'intro_photo_2'   => 'hands-holding-a-family-album',
		'meeting_image'   => 'group-at-a-library-table',
		'islands_image'   => 'marking-a-caribbean-map',
		'help_image'      => 'helping-with-research-at-a-laptop',
		'join_image'      => 'local-history-walk',
		'footer_image'    => 'conversation-at-dusk',
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

/**
 * Botanical cut-out shapes, inlined so CSS can colour them with currentColor.
 * Decorative only: hidden from assistive technology.
 *
 * @param string $name  hibiscus, palm-frond or wave.
 * @param string $class Extra classes.
 * @return string
 */
function cfhg_shape( $name, $class = '' ) {
	static $cache = array();
	$name = sanitize_key( $name );
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = CFHG_DIR . '/assets/images/shapes/' . $name . '.svg';
		$cache[ $name ] = file_exists( $file ) ? (string) file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	}
	if ( '' === $cache[ $name ] ) {
		return '';
	}
	return '<span class="' . esc_attr( trim( 'cfhg-shape cfhg-shape--' . $name . ' ' . $class ) ) . '" aria-hidden="true">' . $cache[ $name ] . '</span>';
}
