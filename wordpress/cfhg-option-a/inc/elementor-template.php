<?php
/**
 * The homepage as Elementor containers and widgets.
 *
 * This builds the same homepage the theme renders, but as native Elementor
 * elements, using the current Customizer copy. Styling comes from the theme's
 * CSS classes (see docs/ELEMENTOR.md), and colours are linked to the CFHG
 * Global Colours, so every section stays editable in Elementor.
 *
 * Used by Appearance > CFHG Setup to:
 * - create an Elementor page and optionally set it as the homepage, or
 * - download a template file for Elementor > Templates > Import.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Unique 7 character element id, as Elementor uses.
 *
 * @return string
 */
function cfhg_el_id() {
	return substr( md5( uniqid( '', true ) . wp_rand() ), 0, 7 );
}

/**
 * Container element.
 *
 * @param array $settings Settings.
 * @param array $children Child elements.
 * @param bool  $inner    Nested container.
 * @return array
 */
function cfhg_el_container( $settings, $children = array(), $inner = true ) {
	return array(
		'id'       => cfhg_el_id(),
		'elType'   => 'container',
		'settings' => $settings,
		'elements' => $children,
		'isInner'  => $inner,
	);
}

/**
 * Widget element.
 *
 * @param string $type     Widget type.
 * @param array  $settings Settings.
 * @return array
 */
function cfhg_el_widget( $type, $settings ) {
	return array(
		'id'         => cfhg_el_id(),
		'elType'     => 'widget',
		'settings'   => $settings,
		'elements'   => array(),
		'widgetType' => $type,
	);
}

/**
 * Box model helper for padding, margin and radius controls.
 *
 * @param string $unit   Unit.
 * @param mixed  $top    Top.
 * @param mixed  $right  Right.
 * @param mixed  $bottom Bottom.
 * @param mixed  $left   Left.
 * @return array
 */
function cfhg_el_box( $unit, $top, $right, $bottom, $left ) {
	return array(
		'unit'     => $unit,
		'top'      => (string) $top,
		'right'    => (string) $right,
		'bottom'   => (string) $bottom,
		'left'     => (string) $left,
		'isLinked' => false,
	);
}

/**
 * Slider value helper.
 *
 * @param mixed  $size Size.
 * @param string $unit Unit.
 * @return array
 */
function cfhg_el_size( $size, $unit = 'px' ) {
	return array(
		'unit'  => $unit,
		'size'  => $size,
		'sizes' => array(),
	);
}

/**
 * Gap helper.
 *
 * @param int $px Gap in pixels.
 * @return array
 */
function cfhg_el_gap( $px ) {
	return array(
		'unit'     => 'px',
		'size'     => $px,
		'column'   => (string) $px,
		'row'      => (string) $px,
		'isLinked' => true,
	);
}

/**
 * Heading widget.
 *
 * @param string $text  Text (may contain inline HTML).
 * @param string $tag   Tag.
 * @param string $class Theme utility class.
 * @return array
 */
function cfhg_el_heading( $text, $tag, $class ) {
	return cfhg_el_widget(
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
 * @param string $class Class.
 * @return array
 */
function cfhg_el_text( $html, $class = 'cfhg-body' ) {
	return cfhg_el_widget(
		'text-editor',
		array(
			'editor'       => $html,
			'_css_classes' => $class,
		)
	);
}

/**
 * Button widget.
 *
 * @param string $label   Label.
 * @param string $url     Link.
 * @param string $variant solid, disc or text.
 * @return array
 */
function cfhg_el_button( $label, $url, $variant = 'disc' ) {
	return cfhg_el_widget(
		'button',
		array(
			'text'         => $label,
			'link'         => array(
				'url'               => cfhg_url( $url ),
				'is_external'       => '',
				'nofollow'          => '',
				'custom_attributes' => '',
			),
			'_css_classes' => 'cfhg-el-btn cfhg-el-btn--' . $variant,
		)
	);
}

/**
 * Shortcode widget.
 *
 * @param string $shortcode Shortcode.
 * @param string $class     Class.
 * @return array
 */
function cfhg_el_shortcode( $shortcode, $class = '' ) {
	return cfhg_el_widget(
		'shortcode',
		array(
			'shortcode'    => $shortcode,
			'_css_classes' => $class,
		)
	);
}

/**
 * Image widget for a Customizer image setting.
 *
 * @param string $setting   Customizer image setting.
 * @param string $class     Class.
 * @param array  $image_map Map of theme slug => attachment ID (from the Media Library import).
 * @param array  $extra     Extra widget settings.
 * @return array
 */
function cfhg_el_image( $setting, $class, $image_map = array(), $extra = array() ) {
	$attachment_id = absint( get_theme_mod( $setting, 0 ) );
	$defaults      = cfhg_image_defaults();
	$slug          = isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : '';
	$manifest      = cfhg_image_manifest();

	if ( ! $attachment_id && $slug && ! empty( $image_map[ $slug ] ) ) {
		$attachment_id = (int) $image_map[ $slug ];
	}

	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'full' );
		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
	} else {
		$url = cfhg_theme_image_url( $slug );
		$alt = $slug && isset( $manifest[ $slug ] ) ? $manifest[ $slug ]['alt'] : '';
	}

	return cfhg_el_widget(
		'image',
		array_merge(
			array(
				'image'        => array(
					'url'    => $url,
					'id'     => $attachment_id ? $attachment_id : '',
					'alt'    => $alt,
					'source' => 'library',
				),
				'image_size'   => 'full',
				'_css_classes' => 'cfhg-el-img ' . $class,
			),
			$extra
		)
	);
}

/**
 * Standard section container.
 *
 * @param string $class    Section class.
 * @param array  $children Children.
 * @param array  $extra    Extra settings.
 * @return array
 */
function cfhg_el_section( $class, $children, $extra = array() ) {
	return cfhg_el_container(
		array_merge(
			array(
				'html_tag'       => 'section',
				'content_width'  => 'boxed',
				'boxed_width'    => cfhg_el_size( 1680 ),
				'flex_direction' => 'column',
				'flex_gap'       => cfhg_el_gap( 40 ),
				'padding'        => cfhg_el_box( 'px', 160, 60, 0, 60 ),
				'padding_tablet' => cfhg_el_box( 'px', 120, 40, 0, 40 ),
				'padding_mobile' => cfhg_el_box( 'px', 88, 20, 0, 20 ),
				'css_classes'    => 'cfhg-el-section ' . $class,
			),
			$extra
		),
		$children,
		false
	);
}

/**
 * Column container.
 *
 * @param int    $width    Desktop width in %.
 * @param array  $children Children.
 * @param array  $extra    Extra settings.
 * @return array
 */
function cfhg_el_column( $width, $children, $extra = array() ) {
	return cfhg_el_container(
		array_merge(
			array(
				'content_width'  => 'full',
				'width'          => cfhg_el_size( $width, '%' ),
				'width_tablet'   => cfhg_el_size( 100, '%' ),
				'width_mobile'   => cfhg_el_size( 100, '%' ),
				'flex_direction' => 'column',
				'flex_gap'       => cfhg_el_gap( 24 ),
				'padding'        => cfhg_el_box( 'px', 0, 0, 0, 0 ),
			),
			$extra
		),
		$children
	);
}

/**
 * Row container.
 *
 * @param array $children Children.
 * @param array $extra    Extra settings.
 * @return array
 */
function cfhg_el_row( $children, $extra = array() ) {
	return cfhg_el_container(
		array_merge(
			array(
				'content_width'         => 'full',
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_wrap'             => 'nowrap',
				'flex_gap'              => cfhg_el_gap( 60 ),
				'padding'               => cfhg_el_box( 'px', 0, 0, 0, 0 ),
			),
			$extra
		),
		$children
	);
}

/**
 * Link to a CFHG Global Colour.
 *
 * @param string $id Colour id.
 * @return string
 */
function cfhg_el_global( $id ) {
	return 'globals/colors?id=' . $id;
}

/**
 * Build the homepage as Elementor element data.
 *
 * @param array $image_map Optional map of theme image slug => attachment ID.
 * @return array
 */
function cfhg_elementor_home_data( $image_map = array() ) {
	$m = 'cfhg_mod';

	/* Kicker text keeps its line breaks. */
	$kicker = static function ( $key ) {
		return nl2br( esc_html( cfhg_mod( $key ) ), false );
	};
	$para   = static function ( $key ) {
		return '<p>' . esc_html( cfhg_mod( $key ) ) . '</p>';
	};

	/* Inline photo tile inside the "Who we are" statement. */
	$tile_id  = absint( get_theme_mod( 'intro_tile_image', 0 ) );
	$tile_url = $tile_id ? wp_get_attachment_image_url( $tile_id, 'medium' ) : cfhg_theme_image_url( 'members-sharing-a-moment' );
	$tile     = '<span class="cfhg-inline-tile"><img src="' . esc_url( $tile_url ) . '" alt="" width="120" height="120"></span>';
	$intro    = str_replace( '{tile}', $tile, wp_kses( $m( 'intro_heading' ), cfhg_inline_kses() ) );

	$mail = cfhg_mailto();

	$sections = array();

	/* 1. Opening panel. */
	$sections[] = cfhg_el_container(
		array(
			'html_tag'              => 'section',
			'content_width'         => 'full',
			'flex_direction'        => 'row',
			'flex_direction_tablet' => 'column',
			'flex_gap'              => cfhg_el_gap( 60 ),
			'flex_align_items'      => 'stretch',
			'min_height'            => cfhg_el_size( 92, 'vh' ),
			'min_height_mobile'     => cfhg_el_size( 0, 'vh' ),
			'padding'               => cfhg_el_box( 'px', 150, 60, 60, 60 ),
			'padding_tablet'        => cfhg_el_box( 'px', 130, 40, 40, 40 ),
			'padding_mobile'        => cfhg_el_box( 'px', 112, 20, 28, 20 ),
			'background_background' => 'classic',
			'background_color'      => '#F2B42A',
			'border_radius'         => cfhg_el_box( 'px', 0, 0, 40, 40 ),
			'border_radius_mobile'  => cfhg_el_box( 'px', 0, 0, 24, 24 ),
			'css_classes'           => 'cfhg-el-section cfhg-el-hero',
			'_element_id'           => 'top',
			'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfhgold' ) ),
		),
		array(
			cfhg_el_column(
				58,
				array(
					cfhg_el_heading( $kicker( 'hero_kicker' ), 'p', 'cfhg-kicker cfhg-kicker--plain' ),
					cfhg_el_heading( wp_kses( $m( 'hero_heading' ), cfhg_inline_kses() ), 'h1', 'cfhg-display' ),
					cfhg_el_text( $para( 'hero_text' ), 'cfhg-lead' ),
					cfhg_el_row(
						array(
							cfhg_el_button( $m( 'hero_primary_label' ), $m( 'hero_primary_url' ), 'solid' ),
							cfhg_el_button( $m( 'hero_secondary_label' ), $m( 'hero_secondary_url' ), 'text' ),
						),
						array(
							'flex_direction_tablet' => 'row',
							'flex_wrap'             => 'wrap',
							'flex_align_items'      => 'center',
							'flex_gap'              => cfhg_el_gap( 28 ),
						)
					),
					cfhg_el_shortcode( '[cfhg_next_meeting style="inline"]' ),
				),
				array( 'flex_justify_content' => 'space-between' )
			),
			cfhg_el_column(
				42,
				array( cfhg_el_image( 'hero_image', 'cfhg-el-img--arch', $image_map ) ),
				array( 'flex_justify_content' => 'flex-end' )
			),
		),
		false
	);

	/* 2. Who we are. */
	$sections[] = cfhg_el_section(
		'cfhg-el-intro',
		array(
			cfhg_el_heading( esc_html( $m( 'intro_kicker' ) ), 'p', 'cfhg-kicker' ),
			cfhg_el_heading( $intro, 'h2', 'cfhg-h1 cfhg-statement' ),
			cfhg_el_row(
				array(
					cfhg_el_column(
						34,
						array(
							cfhg_el_text( $para( 'intro_text' ) ),
							cfhg_el_button( $m( 'intro_link_label' ), $m( 'intro_link_url' ), 'disc' ),
						)
					),
				),
				array( 'flex_justify_content' => 'flex-end' )
			),
		)
	);

	/* 3. Photo strip: widths and offsets set natively so editors can tweak them. */
	$strip   = array();
	$layouts = array(
		1 => array( 30, 0 ),
		2 => array( 20, 7 ),
		3 => array( 23, 2 ),
		4 => array( 32, 10 ),
		5 => array( 22, 4 ),
	);
	foreach ( $layouts as $n => $layout ) {
		$strip[] = cfhg_el_image(
			'strip_image_' . $n,
			'cfhg-el-strip__item cfhg-el-strip__item--' . $n,
			$image_map,
			array(
				'_element_width'              => 'initial',
				'_element_custom_width'       => cfhg_el_size( $layout[0], 'vw' ),
				'_element_custom_width_mobile' => cfhg_el_size( 72, 'vw' ),
				'_flex_size'                  => 'none',
				'_margin'                     => cfhg_el_box( 'rem', $layout[1], 0, 0, 0 ),
				'_margin_mobile'              => cfhg_el_box( 'rem', 0, 0, 0, 0 ),
			)
		);
	}
	$sections[] = cfhg_el_container(
		array(
			'html_tag'              => 'section',
			'content_width'         => 'full',
			'flex_direction'        => 'row',
			'flex_direction_tablet' => 'row',
			'flex_wrap'             => 'nowrap',
			'flex_align_items'      => 'flex-start',
			'flex_gap'              => cfhg_el_gap( 40 ),
			'padding'               => cfhg_el_box( 'px', 100, 60, 0, 60 ),
			'padding_mobile'        => cfhg_el_box( 'px', 64, 20, 0, 20 ),
			'overflow'              => 'auto',
			'css_classes'           => 'cfhg-el-section cfhg-el-strip',
		),
		$strip,
		false
	);

	/* 4. Next meeting. */
	$sections[] = cfhg_el_section(
		'cfhg-el-meeting-wrap',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						28,
						array(
							cfhg_el_heading( esc_html( $m( 'meeting_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_shortcode( '[cfhg_next_meeting style="big"]' ),
						)
					),
					cfhg_el_column(
						42,
						array(
							cfhg_el_heading( wp_kses( $m( 'meeting_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
							cfhg_el_shortcode( '[cfhg_next_meeting style="facts"]' ),
							cfhg_el_shortcode( '[cfhg_prices]' ),
							cfhg_el_row(
								array(
									cfhg_el_button( $m( 'meeting_cta_label' ), $m( 'meeting_cta_url' ), 'solid' ),
									$mail ? cfhg_el_button( $m( 'meeting_secondary_label' ), $mail, 'text' ) : null,
								),
								array(
									'flex_direction_tablet' => 'row',
									'flex_wrap'             => 'wrap',
									'flex_align_items'      => 'center',
									'flex_gap'              => cfhg_el_gap( 28 ),
								)
							),
						)
					),
					cfhg_el_column( 30, array( cfhg_el_image( 'meeting_image', 'cfhg-el-img--rounded cfhg-el-img--portrait', $image_map ) ) ),
				),
				array(
					'background_background' => 'classic',
					'background_color'      => '#0E1F38',
					'border_radius'         => cfhg_el_box( 'px', 40, 40, 40, 40 ),
					'border_radius_mobile'  => cfhg_el_box( 'px', 24, 24, 24, 24 ),
					'padding'               => cfhg_el_box( 'px', 60, 60, 60, 60 ),
					'padding_mobile'        => cfhg_el_box( 'px', 32, 24, 32, 24 ),
					'css_classes'           => 'cfhg-el-meeting cfhg-on-dark',
					'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfhnavy' ) ),
				)
			),
		),
		array(
			'_element_id'    => 'next-meeting',
			'padding'        => cfhg_el_box( 'px', 120, 60, 0, 60 ),
		)
	);

	/* 5. Research by island. */
	$sections[] = cfhg_el_section(
		'cfhg-el-islands',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						56,
						array(
							cfhg_el_heading( esc_html( $m( 'islands_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_heading( wp_kses( $m( 'islands_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
						)
					),
					cfhg_el_column( 32, array( cfhg_el_text( $para( 'islands_text' ) ) ) ),
				),
				array(
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'flex-end',
				)
			),
			cfhg_el_row(
				array(
					cfhg_el_column( 42, array( cfhg_el_text( cfhg_component_islands(), 'cfhg-body cfhg-el-list' ) ) ),
					cfhg_el_column( 58, array( cfhg_el_image( 'islands_image', 'cfhg-el-img--rounded cfhg-el-img--fill', $image_map ) ) ),
				),
				array( 'flex_align_items' => 'stretch' )
			),
		)
	);

	/* 6. Start here. */
	$sections[] = cfhg_el_section(
		'cfhg-el-start',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						40,
						array(
							cfhg_el_heading( esc_html( $m( 'start_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_heading( wp_kses( $m( 'start_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
							cfhg_el_text( $para( 'start_text' ), 'cfhg-lead' ),
							cfhg_el_image( 'start_image', 'cfhg-el-img--rounded cfhg-el-img--portrait', $image_map ),
						),
						array( 'css_classes' => 'cfhg-el-sticky' )
					),
					cfhg_el_column(
						52,
						array(
							cfhg_el_text( cfhg_component_steps(), 'cfhg-body cfhg-el-list' ),
							cfhg_el_button( $m( 'start_link_label' ), $m( 'start_link_url' ), 'disc' ),
						)
					),
				),
				array( 'flex_justify_content' => 'space-between' )
			),
		),
		array( '_element_id' => 'start-here' )
	);

	/* 7. Stories: live posts via shortcode. */
	$sections[] = cfhg_el_section(
		'cfhg-el-stories',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						60,
						array(
							cfhg_el_heading( esc_html( $m( 'stories_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_heading( wp_kses( $m( 'stories_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h1' ),
						)
					),
					cfhg_el_button( $m( 'stories_link_label' ), $m( 'stories_link_url' ), 'disc' ),
				),
				array(
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'flex-end',
				)
			),
			cfhg_el_shortcode( '[cfhg_stories count="3"]' ),
		)
	);

	/* 8. Research help: card overlaps the photograph. */
	$sections[] = cfhg_el_section(
		'cfhg-el-help',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column( 62, array( cfhg_el_image( 'help_image', 'cfhg-el-img--rounded cfhg-el-img--wide', $image_map ) ) ),
					cfhg_el_column(
						44,
						array(
							cfhg_el_heading( esc_html( $m( 'help_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_heading( wp_kses( $m( 'help_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
							cfhg_el_text( $para( 'help_text' ) ),
							cfhg_el_button( $m( 'help_cta_label' ), $m( 'help_cta_url' ), 'solid' ),
						),
						array(
							'background_background' => 'classic',
							'background_color'      => '#FCE6AF',
							'border_radius'         => cfhg_el_box( 'px', 20, 20, 20, 20 ),
							'padding'               => cfhg_el_box( 'px', 48, 48, 48, 48 ),
							'padding_mobile'        => cfhg_el_box( 'px', 28, 24, 28, 24 ),
							'margin'                => cfhg_el_box( '%', 0, 0, -3, -6 ),
							'margin_tablet'         => cfhg_el_box( 'px', -60, 24, 0, 24 ),
							'margin_mobile'         => cfhg_el_box( 'px', -40, 12, 0, 12 ),
							'css_classes'           => 'cfhg-el-help__card',
							'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfhglow' ) ),
						)
					),
				),
				array(
					'flex_gap'         => cfhg_el_gap( 0 ),
					'flex_align_items' => 'flex-end',
				)
			),
		)
	);

	/* 9. Membership. */
	$sections[] = cfhg_el_section(
		'cfhg-el-join-wrap',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						48,
						array(
							cfhg_el_heading( esc_html( $m( 'join_kicker' ) ), 'p', 'cfhg-kicker' ),
							cfhg_el_heading( wp_kses( $m( 'join_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h1' ),
							cfhg_el_text( $para( 'join_text' ), 'cfhg-lead' ),
							cfhg_el_row(
								array(
									cfhg_el_button( $m( 'join_cta_label' ), $m( 'join_cta_url' ), 'solid' ),
									$mail ? cfhg_el_button( $m( 'join_secondary_label' ), $mail, 'text' ) : null,
								),
								array(
									'flex_direction_tablet' => 'row',
									'flex_wrap'             => 'wrap',
									'flex_align_items'      => 'center',
									'flex_gap'              => cfhg_el_gap( 28 ),
								)
							),
						)
					),
					cfhg_el_column( 52, array( cfhg_el_image( 'join_image', 'cfhg-el-img--rounded cfhg-el-img--wide', $image_map ) ) ),
				),
				array(
					'flex_align_items'      => 'center',
					'background_background' => 'classic',
					'background_color'      => '#F2B42A',
					'border_radius'         => cfhg_el_box( 'px', 40, 40, 40, 40 ),
					'border_radius_mobile'  => cfhg_el_box( 'px', 24, 24, 24, 24 ),
					'padding'               => cfhg_el_box( 'px', 60, 60, 60, 60 ),
					'padding_mobile'        => cfhg_el_box( 'px', 32, 24, 32, 24 ),
					'css_classes'           => 'cfhg-el-join',
					'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfhgold' ) ),
				)
			),
		)
	);

	/* 10. Partners. */
	$sections[] = cfhg_el_section(
		'cfhg-el-partners',
		array(
			cfhg_el_heading( esc_html( $m( 'partners_kicker' ) ), 'p', 'cfhg-kicker' ),
			cfhg_el_heading( wp_kses( $m( 'partners_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
			cfhg_el_text( cfhg_component_partners(), 'cfhg-body cfhg-el-list' ),
		),
		array(
			'padding'        => cfhg_el_box( 'px', 160, 60, 160, 60 ),
			'padding_mobile' => cfhg_el_box( 'px', 88, 20, 88, 20 ),
		)
	);

	return cfhg_el_strip_nulls( $sections );
}

/**
 * Remove null children (optional buttons that had no link).
 *
 * @param array $elements Elements.
 * @return array
 */
function cfhg_el_strip_nulls( $elements ) {
	$clean = array();
	foreach ( $elements as $element ) {
		if ( null === $element ) {
			continue;
		}
		if ( ! empty( $element['elements'] ) ) {
			$element['elements'] = cfhg_el_strip_nulls( $element['elements'] );
		}
		$clean[] = $element;
	}
	return $clean;
}

/**
 * Copy a theme image into the Media Library once, with its alt text.
 *
 * @param string $slug Manifest key.
 * @return int Attachment ID, or 0 on failure.
 */
function cfhg_import_theme_image( $slug ) {
	$imported = get_option( 'cfhg_imported_images', array() );
	if ( ! empty( $imported[ $slug ] ) && wp_attachment_is_image( (int) $imported[ $slug ] ) ) {
		return (int) $imported[ $slug ];
	}

	$manifest = cfhg_image_manifest();
	if ( empty( $manifest[ $slug ] ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$width = max( array_keys( $manifest[ $slug ]['sizes'] ) );
	$file  = $slug . '-' . $width . '.jpg';
	$path  = CFHG_DIR . '/assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! copy( $path, $tmp ) ) {
		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => $slug . '.jpg',
			'tmp_name' => $tmp,
		),
		0,
		ucfirst( str_replace( '-', ' ', $slug ) )
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $manifest[ $slug ]['alt'] );
	$imported[ $slug ] = $id;
	update_option( 'cfhg_imported_images', $imported, false );
	return (int) $id;
}

/**
 * Import every theme image the homepage uses.
 *
 * @return array<string, int>
 */
function cfhg_import_home_images() {
	$map = array();
	foreach ( array_unique( array_values( cfhg_image_defaults() ) ) as $slug ) {
		$id = cfhg_import_theme_image( $slug );
		if ( $id ) {
			$map[ $slug ] = $id;
		}
	}
	return $map;
}

/**
 * Create the homepage as an Elementor page.
 *
 * @param bool $set_as_front Also publish it and make it the homepage.
 * @return int|WP_Error Page ID.
 */
function cfhg_create_elementor_home( $set_as_front = false ) {
	if ( ! cfhg_elementor_is_active() ) {
		return new WP_Error( 'cfhg_no_elementor', __( 'Elementor is not active.', 'cfhg' ) );
	}

	$data    = cfhg_elementor_home_data( cfhg_import_home_images() );
	$page_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => $set_as_front ? 'publish' : 'draft',
			'post_title'  => __( 'Home', 'cfhg' ),
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		return $page_id;
	}

	update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

	if ( $set_as_front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );
	}

	if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}

	return $page_id;
}

/**
 * The homepage as an Elementor template file (for Templates > Import).
 * Images point at the theme folder; Elementor copies them into the
 * Media Library during import.
 *
 * @return array
 */
function cfhg_elementor_template_file() {
	return array(
		'version'       => '0.4',
		'title'         => __( 'CFHG homepage (Option A)', 'cfhg' ),
		'type'          => 'page',
		'content'       => cfhg_elementor_home_data(),
		'page_settings' => array( 'template' => 'elementor_header_footer' ),
	);
}
