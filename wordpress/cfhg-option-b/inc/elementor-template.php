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
 * @param string $variant       mark, green, plain or text.
 * @param string $extra_class   Extra classes.
 * @return array
 */
function cfhg_el_button( $label, $url, $variant = 'mark', $extra_class = '' ) {
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
			'_css_classes' => trim( 'cfhg-el-btn cfhg-el-btn--' . $variant . ' ' . $extra_class ),
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
				'boxed_width'    => cfhg_el_size( 1600 ),
				'flex_direction' => 'column',
				'flex_gap'       => cfhg_el_gap( 40 ),
				'padding'        => cfhg_el_box( 'px', 140, 32, 0, 32 ),
				'padding_tablet' => cfhg_el_box( 'px', 110, 24, 0, 24 ),
				'padding_mobile' => cfhg_el_box( 'px', 80, 18, 0, 18 ),
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
 * Absolutely positioned decorative shape, via the [cfhg_shape] shortcode.
 *
 * @param string $name  Shape.
 * @param string $class Class.
 * @param int    $width Width in px.
 * @param int    $x     Offset from the left in px.
 * @param int    $y     Offset from the top in px.
 * @param string $from  Measure x from the start (left) or end (right).
 * @return array
 */
function cfhg_el_shape( $name, $class, $width, $x, $y, $from = 'start' ) {
	$horizontal = 'end' === $from
		? array(
			'_offset_orientation_h' => 'end',
			'_offset_x_end'         => cfhg_el_size( $x ),
		)
		: array(
			'_offset_orientation_h' => 'start',
			'_offset_x'             => cfhg_el_size( $x ),
		);
	return cfhg_el_widget(
		'shortcode',
		array_merge(
			$horizontal,
			array(
			'shortcode'               => '[cfhg_shape name="' . $name . '"]',
			'_css_classes'            => $class,
			'_position'               => 'absolute',
			'_offset_orientation_v'   => 'start',
			'_offset_y'               => cfhg_el_size( $y ),
			'_element_width'          => 'initial',
			'_element_custom_width'   => cfhg_el_size( $width ),
			'_element_custom_width_mobile' => cfhg_el_size( round( $width * 0.6 ) ),
			'_z_index'                => 0,
			)
		)
	);
}

/**
 * White card container.
 *
 * @param array $children Children.
 * @param array $extra    Extra settings.
 * @return array
 */
function cfhg_el_card( $children, $extra = array() ) {
	return cfhg_el_container(
		array_merge(
			array(
				'content_width'         => 'full',
				'flex_direction'        => 'column',
				'flex_gap'              => cfhg_el_gap( 20 ),
				'background_background' => 'classic',
				'background_color'      => '#FFFFFF',
				'border_radius'         => cfhg_el_box( 'px', 12, 12, 12, 12 ),
				'padding'               => cfhg_el_box( 'px', 40, 40, 40, 40 ),
				'padding_mobile'        => cfhg_el_box( 'px', 24, 22, 24, 22 ),
				'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfbwhit' ) ),
			),
			$extra
		),
		$children
	);
}

/**
 * Build the homepage as Elementor element data.
 *
 * @param array $image_map Optional map of theme image slug => attachment ID.
 * @return array
 */
function cfhg_elementor_home_data( $image_map = array() ) {
	$m    = 'cfhg_mod';
	$mail = cfhg_mailto();
	$para = static function ( $key ) {
		return '<p>' . esc_html( cfhg_mod( $key ) ) . '</p>';
	};
	$label = static function ( $key ) {
		return cfhg_el_heading( esc_html( cfhg_mod( $key ) ), 'p', 'cfhg-label' );
	};
	$buttons = static function ( $items ) {
		return cfhg_el_row(
			array_values( array_filter( $items ) ),
			array(
				'flex_direction_tablet' => 'row',
				'flex_wrap'             => 'wrap',
				'flex_align_items'      => 'center',
				'flex_gap'              => cfhg_el_gap( 20 ),
			)
		);
	};

	$sections = array();

	/* 1. Opening panel: the words beside a photograph in a large circle. */
	$sections[] = cfhg_el_container(
		array(
			'html_tag'              => 'section',
			'content_width'         => 'boxed',
			'boxed_width'           => cfhg_el_size( 1600 ),
			'flex_direction'        => 'row',
			'flex_direction_tablet' => 'column',
			'flex_direction_mobile' => 'column',
			'flex_align_items'      => 'center',
			'flex_gap'              => cfhg_el_gap( 48 ),
			'min_height'            => cfhg_el_size( 100, 'vh' ),
			'min_height_tablet'     => cfhg_el_size( 0 ),
			'min_height_mobile'     => cfhg_el_size( 0 ),
			'padding'               => cfhg_el_box( 'px', 120, 32, 64, 32 ),
			'padding_tablet'        => cfhg_el_box( 'px', 110, 24, 48, 24 ),
			'padding_mobile'        => cfhg_el_box( 'px', 100, 18, 40, 18 ),
			'overflow'              => 'hidden',
			'css_classes'           => 'cfhg-el-section cfhg-el-hero',
			'_element_id'           => 'top',
		),
		array(
			cfhg_el_column(
				50,
				array(
					cfhg_el_text( '<p class="cfhg-hero__kicker">' . esc_html( $m( 'hero_kicker' ) ) . '</p>', 'cfhg-el-kicker' ),
					cfhg_el_heading( wp_kses( $m( 'hero_heading' ), cfhg_inline_kses() ), 'h1', 'cfhg-el-hero-title' ),
					cfhg_el_text( $para( 'hero_text' ), 'cfhg-lead cfhg-el-narrow' ),
					cfhg_el_shortcode( '[cfhg_hero_proof]' ),
					$buttons(
						array(
							cfhg_el_button( $m( 'hero_primary_label' ), $m( 'hero_primary_url' ), 'mark', 'cfhg-el-btn--big' ),
							cfhg_el_button( $m( 'hero_secondary_label' ), $m( 'hero_secondary_url' ), 'text' ),
						)
					),
					cfhg_el_shortcode( '[cfhg_next_meeting style="inline"]' ),
				)
			),
			cfhg_el_column(
				50,
				array( cfhg_el_image( 'hero_image', 'cfhg-el-img cfhg-el-img--disc', $image_map ) ),
				array( 'css_classes' => 'cfhg-el-hero-art' )
			),
		),
		false
	);

	/* 2. Who we are: a white card between two tilted snapshots. */
	$sections[] = cfhg_el_section(
		'cfhg-el-intro',
		array(
			cfhg_el_row(
				array(
					cfhg_el_widget(
						'shortcode',
						array(
							'shortcode'                    => '[cfhg_snapshot image="intro_photo_1" tone="coral" shape="portrait"]',
							'_css_classes'                 => 'cfhg-intro__snap cfhg-intro__snap--left',
							'_element_width'               => 'initial',
							'_element_custom_width'        => cfhg_el_size( 24, '%' ),
							'_element_custom_width_mobile' => cfhg_el_size( 46, '%' ),
						)
					),
					cfhg_el_card(
						array(
							cfhg_el_heading( esc_html( $m( 'intro_title' ) ), 'h2', 'cfhg-el-card-title' ),
							cfhg_el_text( cfhg_paragraphs( $m( 'intro_text' ) ) ),
							cfhg_el_button( $m( 'intro_link_label' ), $m( 'intro_link_url' ), 'mark' ),
						),
						array( 'width' => cfhg_el_size( 44, '%' ) )
					),
					cfhg_el_widget(
						'shortcode',
						array(
							'shortcode'                    => '[cfhg_snapshot image="intro_photo_2" tone="green" shape="landscape"]',
							'_css_classes'                 => 'cfhg-intro__snap cfhg-intro__snap--right',
							'_element_width'               => 'initial',
							'_element_custom_width'        => cfhg_el_size( 24, '%' ),
							'_element_custom_width_mobile' => cfhg_el_size( 46, '%' ),
						)
					),
				),
				array(
					'flex_align_items'     => 'center',
					'flex_justify_content' => 'space-between',
				)
			),
		)
	);

	/* 3. Next meeting. */
	$sections[] = cfhg_el_section(
		'cfhg-el-meeting',
		array(
			cfhg_el_row(
				array(
					cfhg_el_card(
						array(
							$label( 'meeting_kicker' ),
							cfhg_el_shortcode( '[cfhg_next_meeting style="title"]' ),
							cfhg_el_heading( '<span class="cfhg-highlight">' . wp_kses( $m( 'meeting_heading' ), cfhg_inline_kses() ) . '</span>', 'h2', 'cfhg-h3' ),
							cfhg_el_shortcode( '[cfhg_next_meeting style="facts"]' ),
							cfhg_el_shortcode( '[cfhg_prices]' ),
							$buttons(
								array(
									cfhg_el_button( $m( 'meeting_cta_label' ), $m( 'meeting_cta_url' ), 'green' ),
									$mail ? cfhg_el_button( $m( 'meeting_secondary_label' ), $mail, 'text' ) : null,
								)
							),
						),
						array( 'width' => cfhg_el_size( 45, '%' ) )
					),
					cfhg_el_column(
						55,
						array(
							cfhg_el_image( 'meeting_image', 'cfhg-el-img--rounded cfhg-el-img--fill', $image_map ),
							cfhg_el_widget(
								'heading',
								array(
									'title'                 => esc_html( $m( 'meeting_image_label' ) ),
									'header_size'           => 'p',
									'_css_classes'          => 'cfhg-el-chip',
									'_position'             => 'absolute',
									'_offset_orientation_h' => 'start',
									'_offset_x'             => cfhg_el_size( 16 ),
									'_offset_orientation_v' => 'end',
									'_offset_y_end'         => cfhg_el_size( 16 ),
								)
							),
						)
					),
				),
				array( 'flex_align_items' => 'stretch', 'flex_gap' => cfhg_el_gap( 32 ) )
			),
		),
		array( '_element_id' => 'next-meeting' )
	);

	/* 4. The group in numbers. */
	$sections[] = cfhg_el_section(
		'cfhg-el-numbers',
		array(
			cfhg_el_widget(
				'heading',
				array(
					'title'        => esc_html( $m( 'numbers_title' ) ),
					'header_size'  => 'h2',
					'align'        => 'center',
					'_css_classes' => 'cfhg-h2',
				)
			),
			cfhg_el_text( cfhg_component_numbers(), 'cfhg-body cfhg-el-list' ),
		)
	);

	/* 5. Start here: five coloured tiles. */
	$sections[] = cfhg_el_section(
		'cfhg-el-start',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column( 58, array( $label( 'start_kicker' ), cfhg_el_heading( wp_kses( $m( 'start_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ) ) ),
					cfhg_el_column( 38, array( cfhg_el_text( $para( 'start_text' ), 'cfhg-lead' ) ) ),
				),
				array(
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'flex-end',
				)
			),
			cfhg_el_text( cfhg_component_steps(), 'cfhg-body cfhg-el-list' ),
			cfhg_el_button( $m( 'start_link_label' ), $m( 'start_link_url' ), 'mark' ),
		),
		array( '_element_id' => 'start-here' )
	);

	/* 6. Research by island. */
	$sections[] = cfhg_el_section(
		'cfhg-el-islands',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						54,
						array(
							$label( 'islands_kicker' ),
							cfhg_el_heading( wp_kses( $m( 'islands_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
							cfhg_el_text( $para( 'islands_text' ) ),
							cfhg_el_text( cfhg_component_islands(), 'cfhg-body cfhg-el-list' ),
						)
					),
					cfhg_el_column(
						42,
						array(
							cfhg_el_image( 'islands_image', 'cfhg-el-img--oval', $image_map ),
							cfhg_el_shape( 'palm-frond', 'cfhg-el-shape cfhg-el-shape--green', 180, -20, 360 ),
						),
						array(
							'overflow'              => 'hidden',
							'background_background' => 'classic',
							'background_color'      => '#F6C343',
							'border_radius'         => cfhg_el_box( 'px', 20, 20, 20, 20 ),
							'padding'               => cfhg_el_box( 'px', 40, 48, 0, 48 ),
							'min_height'            => cfhg_el_size( 520 ),
							'min_height_mobile'     => cfhg_el_size( 380 ),
							'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfbgold' ) ),
						)
					),
				),
				array(
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'center',
				)
			),
		)
	);

	/* 7. Stories carousel (live posts). */
	$sections[] = cfhg_el_section(
		'cfhg-el-stories',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column( 60, array( $label( 'stories_kicker' ), cfhg_el_heading( wp_kses( $m( 'stories_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ) ) ),
					cfhg_el_button( $m( 'stories_link_label' ), $m( 'stories_link_url' ), 'text' ),
				),
				array(
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'flex-end',
				)
			),
			cfhg_el_shortcode( '[cfhg_stories count="3"]', 'cfhg-el-bleed' ),
		)
	);

	/* 8. Research help. */
	$sections[] = cfhg_el_section(
		'cfhg-el-help',
		array(
			cfhg_el_row(
				array(
					cfhg_el_shape( 'hibiscus', 'cfhg-el-shape cfhg-el-shape--coral', 220, -60, -60, 'end' ),
					cfhg_el_column(
						46,
						array(
							$label( 'help_kicker' ),
							cfhg_el_heading( wp_kses( $m( 'help_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
							cfhg_el_text( $para( 'help_text' ) ),
							cfhg_el_button( $m( 'help_cta_label' ), $m( 'help_cta_url' ), 'green' ),
						)
					),
					cfhg_el_column( 50, array( cfhg_el_image( 'help_image', 'cfhg-el-img--rounded', $image_map ) ) ),
				),
				array(
					'flex_justify_content'  => 'space-between',
					'flex_align_items'      => 'center',
					'overflow'              => 'hidden',
					'background_background' => 'classic',
					'background_color'      => '#ECE4D9',
					'border_radius'         => cfhg_el_box( 'px', 20, 20, 20, 20 ),
					'padding'               => cfhg_el_box( 'px', 56, 56, 56, 56 ),
					'padding_mobile'        => cfhg_el_box( 'px', 24, 22, 24, 22 ),
					'__globals__'           => array( 'background_color' => cfhg_el_global( 'cfbsand' ) ),
				)
			),
		)
	);

	/* 9. Membership: photograph band with large words, card overlapping it. */
	$join_id  = absint( get_theme_mod( 'join_image', 0 ) );
	$join_id  = $join_id ? $join_id : ( isset( $image_map['local-history-walk'] ) ? (int) $image_map['local-history-walk'] : 0 );
	$join_url = $join_id ? wp_get_attachment_image_url( $join_id, 'full' ) : cfhg_theme_image_url( 'local-history-walk' );
	$sections[] = cfhg_el_section(
		'cfhg-el-join',
		array(
			cfhg_el_container(
				array(
					'content_width'                 => 'full',
					'min_height'                    => cfhg_el_size( 520 ),
					'min_height_mobile'             => cfhg_el_size( 300 ),
					'flex_justify_content'          => 'flex-start',
					'background_background'         => 'classic',
					'background_image'              => array(
						'url' => $join_url,
						'id'  => $join_id ? $join_id : '',
					),
					'background_position'           => 'center center',
					'background_size'               => 'cover',
					'background_overlay_background' => 'classic',
					'background_overlay_color'      => '#10213B',
					'background_overlay_opacity'    => cfhg_el_size( 0.3 ),
					'border_radius'                 => cfhg_el_box( 'px', 20, 20, 20, 20 ),
					'padding'                       => cfhg_el_box( 'px', 12, 12, 12, 12 ),
				),
				array( cfhg_el_heading( esc_html( $m( 'join_big' ) ), 'p', 'cfhg-el-big' ) )
			),
			cfhg_el_card(
				array(
					$label( 'join_kicker' ),
					cfhg_el_heading( wp_kses( $m( 'join_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
					cfhg_el_text( $para( 'join_text' ), 'cfhg-lead' ),
					$buttons(
						array(
							cfhg_el_button( $m( 'join_cta_label' ), $m( 'join_cta_url' ), 'mark', 'cfhg-el-btn--big' ),
							$mail ? cfhg_el_button( $m( 'join_secondary_label' ), $mail, 'text' ) : null,
						)
					),
				),
				array(
					'width'         => cfhg_el_size( 60, '%' ),
					'width_mobile'  => cfhg_el_size( 100, '%' ),
					'margin'        => cfhg_el_box( 'px', -140, 0, 0, 0 ),
					'margin_mobile' => cfhg_el_box( 'px', -60, 0, 0, 0 ),
					'_z_index'      => 2,
				)
			),
		),
		array(
			'flex_align_items' => 'center',
			'flex_gap'         => cfhg_el_gap( 0 ),
			'_element_id'      => 'join',
		)
	);

	/* 10. Partners. */
	$sections[] = cfhg_el_section(
		'cfhg-el-partners',
		array(
			$label( 'partners_kicker' ),
			cfhg_el_heading( wp_kses( $m( 'partners_heading' ), cfhg_inline_kses() ), 'h2', 'cfhg-h2' ),
			cfhg_el_card(
				array( cfhg_el_text( cfhg_component_partners(), 'cfhg-body cfhg-el-list' ) ),
				array( 'padding' => cfhg_el_box( 'px', 8, 36, 8, 36 ) )
			),
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
	$imported = get_option( 'cfhg_b_imported_images', array() );
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
	update_option( 'cfhg_b_imported_images', $imported, false );
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
		'title'         => __( 'CFHG homepage (Option B)', 'cfhg' ),
		'type'          => 'page',
		'content'       => cfhg_elementor_home_data(),
		'page_settings' => array( 'template' => 'elementor_header_footer' ),
	);
}
