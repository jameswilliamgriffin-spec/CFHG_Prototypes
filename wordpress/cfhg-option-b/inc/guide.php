<?php
/**
 * "Starting your family tree": the five steps guide page.
 *
 * A static page with the group's five steps in full, the explainer video and
 * how to complete pedigree charts and family group records. The words live in
 * inc/guide-content.json, which is shared with scripts/build-preview-b.mjs so
 * the static preview shows the same page.
 *
 * Two ways to show it, like the homepage:
 * - Theme: a page using the "CFHG Five steps guide" template
 *   (page-templates/template-guide.php) renders cfhg_guide_markup().
 * - Elementor: Appearance > CFHG Setup > "Create the five steps page" builds
 *   the same page from Elementor widgets (cfhg_elementor_guide_data()), so
 *   every word can be edited in Elementor.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * The guide's content.
 *
 * @return array<string, mixed>
 */
function cfhg_guide_content() {
	static $content = null;
	if ( null === $content ) {
		$file    = CFHG_DIR . '/inc/guide-content.json';
		$content = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		$content = is_array( $content ) ? $content : array();
	}
	return $content;
}

/**
 * The guide page's address.
 *
 * @return string
 */
function cfhg_guide_url() {
	$c = cfhg_guide_content();
	return home_url( '/' . ( isset( $c['slug'] ) ? $c['slug'] : 'starting-your-family-tree' ) . '/' );
}

/**
 * A plain bullet list.
 *
 * @param array  $items Items (plain text).
 * @param string $class List class.
 * @return string
 */
function cfhg_guide_list( $items, $class = 'cfhg-guide__points' ) {
	$html = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( (array) $items as $item ) {
		$html .= '<li>' . esc_html( $item ) . '</li>';
	}
	return $html . '</ul>';
}

/**
 * The body of one step: its list, paragraphs or groups.
 *
 * @param array $step Step.
 * @return string
 */
function cfhg_guide_step_body( $step ) {
	$html = '';
	if ( ! empty( $step['points'] ) ) {
		$html .= cfhg_guide_list( $step['points'] );
	}
	if ( ! empty( $step['text'] ) ) {
		foreach ( (array) $step['text'] as $para ) {
			$html .= '<p>' . esc_html( $para ) . '</p>';
		}
	}
	if ( ! empty( $step['groups'] ) ) {
		$html .= '<div class="cfhg-guide__groups">';
		foreach ( (array) $step['groups'] as $group ) {
			$html .= '<div class="cfhg-guide__group"><h4>' . esc_html( $group['title'] ) . '</h4><p>' . esc_html( $group['text'] ) . '</p></div>';
		}
		$html .= '</div>';
	}
	return $html;
}

/**
 * The big step number with its icon sticker beside it.
 *
 * @param int   $index Zero-based position.
 * @param array $step  Step.
 * @return string
 */
function cfhg_guide_step_number( $index, $step ) {
	$icon = isset( $step['icon'] ) ? sanitize_key( $step['icon'] ) : '';
	$icon = isset( cfhg_card_icons()[ $icon ] ) ? ' cfhg-has-icon cfhg-icon--' . $icon : '';
	return sprintf(
		'<p class="cfhg-guide-step__num%1$s"><span class="screen-reader-text">%2$s </span>%3$s</p>',
		esc_attr( $icon ),
		esc_html__( 'Step', 'cfhg' ),
		esc_html( sprintf( '%02d', $index + 1 ) )
	);
}

/**
 * "Even numbers represent males, odd numbers represent females", with a
 * small numbered pedigree chart showing it.
 *
 * @param array $numbering The numbering section.
 * @return string
 */
function cfhg_guide_numbering( $numbering ) {
	$html  = '<div class="cfhg-guide__evenodd">';
	$html .= '<p><strong class="is-even">' . esc_html__( 'Even', 'cfhg' ) . '</strong> ' . esc_html( $numbering['even'] ) . '</p>';
	$html .= '<p><strong class="is-odd">' . esc_html__( 'Odd', 'cfhg' ) . '</strong> ' . esc_html( $numbering['odd'] ) . '</p>';
	$html .= '</div>';
	if ( ! empty( $numbering['chart'] ) ) {
		$html .= '<ol class="cfhg-pedigree" aria-label="' . esc_attr__( 'How a pedigree chart is numbered', 'cfhg' ) . '">';
		foreach ( (array) $numbering['chart'] as $person ) {
			$n     = (int) $person[0];
			$kind  = 1 === $n ? 'is-you' : ( 0 === $n % 2 ? 'is-even' : 'is-odd' );
			$html .= '<li class="cfhg-pedigree__person cfhg-pedigree__person--' . $n . '"><span class="cfhg-pedigree__n ' . $kind . '">' . $n . '</span> ' . esc_html( $person[1] ) . '</li>';
		}
		$html .= '</ol>';
	}
	return $html;
}

/**
 * The film's poster as a link. With JavaScript it opens in a lightbox
 * (assets/js/lightbox.js); without, the link plays the file itself.
 *
 * @return string
 */
function cfhg_guide_video() {
	$c = cfhg_guide_content();
	if ( empty( $c['video']['file'] ) ) {
		return '';
	}
	$base = CFHG_URI . '/assets/video/';
	return sprintf(
		'<a class="cfhg-film cfhg-video-open" href="%1$s" aria-label="%3$s"><img src="%2$s" width="1280" height="720" alt="" loading="lazy" decoding="async"><span class="cfhg-film__play" aria-hidden="true"></span></a>',
		esc_url( $base . $c['video']['file'] ),
		esc_url( $base . $c['video']['poster'] ),
		esc_attr( $c['video']['alt'] )
	);
}

/**
 * The words beside the film: a heading, a sentence, a button that opens it,
 * and a way past it for people who would rather read.
 *
 * @return string
 */
function cfhg_guide_film_copy() {
	$c = cfhg_guide_content();
	$v = $c['video'];
	return '<h2 class="cfhg-h2" id="cfhg-guide-film-title">' . esc_html( $v['heading'] ) . '</h2>'
		. '<p class="cfhg-lead">' . esc_html( $v['text'] ) . '</p>'
		. '<div class="cfhg-actions">'
		. cfhg_button( $v['play'] . ', ' . $v['length'], CFHG_URI . '/assets/video/' . $v['file'], 'mark', 'cfhg-video-open' )
		. cfhg_button( $v['read'], '#cfhg-guide-steps', 'text' )
		. '</div>';
}

/**
 * The call to action at the foot of the page.
 *
 * @return string Inner markup of the card.
 */
function cfhg_guide_cta_inner() {
	$c    = cfhg_guide_content();
	$mail = cfhg_mailto();
	return '<h2 class="cfhg-h2" id="cfhg-guide-cta">' . esc_html( $c['cta']['heading'] ) . '</h2>'
		. '<p class="cfhg-lead">' . esc_html( $c['cta']['text'] ) . '</p>'
		. cfhg_component_next_meeting( 'inline' )
		. '<div class="cfhg-actions">'
		. cfhg_button( cfhg_mod( 'meeting_cta_label' ), home_url( '/#next-meeting' ), 'mark', 'cfhg-btn--big' )
		. ( $mail ? cfhg_button( cfhg_mod( 'meeting_secondary_label' ), $mail, 'text' ) : '' )
		. '</div>';
}

/**
 * Links from the top of the guide to its parts.
 *
 * @return string A list.
 */
function cfhg_guide_jump_list() {
	$links = array(
		'cfhg-guide-film'   => __( 'Watch the film', 'cfhg' ),
		'cfhg-guide-steps'  => __( 'The five steps', 'cfhg' ),
		'cfhg-guide-charts' => __( 'Filling in the charts', 'cfhg' ),
	);
	$html  = '<ul>';
	foreach ( $links as $id => $label ) {
		$html .= '<li><a href="#' . esc_attr( $id ) . '">' . esc_html( $label ) . '</a></li>';
	}
	return $html . '</ul>';
}

/**
 * The whole page, for the theme template.
 *
 * @return string
 */
function cfhg_guide_markup() {
	$c = cfhg_guide_content();
	if ( empty( $c['steps'] ) ) {
		return '';
	}

	$steps = '';
	foreach ( $c['steps'] as $i => $step ) {
		$steps .= sprintf(
			'<li class="cfhg-guide-step cfhg-guide-step--%1$d" id="step-%1$d"><div class="cfhg-guide-step__copy">%2$s<h3 class="cfhg-h2 cfhg-guide-step__title">%3$s</h3>%4$s%5$s</div><figure class="cfhg-guide-step__media">%6$s</figure></li>',
			$i + 1,
			cfhg_guide_step_number( $i, $step ),
			esc_html( $step['title'] ),
			empty( $step['question'] ) ? '' : '<p class="cfhg-lead cfhg-guide-step__question">' . esc_html( $step['question'] ) . '</p>',
			cfhg_guide_step_body( $step ),
			cfhg_theme_image( $step['image'], array( 'sizes' => '(min-width: 1024px) 40vw, 92vw' ) )
		);
	}

	$charts = $c['charts'];

	return '<main id="main" class="cfhg-main cfhg-guide">'
		. '<header class="cfhg-page-hero"><div class="cfhg-container cfhg-guide__hero-grid">'
		. '<div><h1 class="cfhg-h1 cfhg-page-hero__title">' . esc_html( $c['title'] ) . '</h1><p class="cfhg-lead cfhg-page-hero__lead">' . esc_html( $c['lead'] ) . '</p>'
		. '<nav class="cfhg-jump" aria-label="' . esc_attr__( 'On this page', 'cfhg' ) . '">' . cfhg_guide_jump_list() . '</nav></div>'
		. '<figure class="cfhg-postcard cfhg-postcard--gold cfhg-guide__hero-art">' . cfhg_theme_image( 'filling-in-a-family-tree-chart', array( 'sizes' => '(min-width: 1024px) 42vw, 92vw', 'loading' => 'eager' ) ) . '</figure>'
		. '</div></header>'
		. '<section class="cfhg-section cfhg-guide__film" id="cfhg-guide-film" aria-labelledby="cfhg-guide-film-title"><div class="cfhg-container cfhg-guide__film-grid"><div class="cfhg-guide__film-copy">' . cfhg_guide_film_copy() . '</div><div class="cfhg-guide__film-player">' . cfhg_guide_video() . '</div></div></section>'
		. '<section class="cfhg-section cfhg-guide__before" aria-labelledby="cfhg-guide-before"><div class="cfhg-container"><h2 class="cfhg-h2" id="cfhg-guide-before">' . esc_html( $c['before']['heading'] ) . '</h2>' . cfhg_guide_list( $c['before']['questions'], 'cfhg-guide__questions' ) . '</div></section>'
		. '<section class="cfhg-section cfhg-guide__steps" aria-labelledby="cfhg-guide-steps"><div class="cfhg-container"><h2 class="cfhg-h2" id="cfhg-guide-steps">' . esc_html( $c['steps_heading'] ) . '</h2><ol class="cfhg-guide__list">' . $steps . '</ol></div></section>'
		. '<section class="cfhg-guide__charts" aria-labelledby="cfhg-guide-charts"><div class="cfhg-container">'
		. '<h2 class="cfhg-h2" id="cfhg-guide-charts">' . esc_html( $charts['heading'] ) . '</h2><p class="cfhg-lead">' . esc_html( $charts['lead'] ) . '</p>'
		. '<div class="cfhg-guide__chart-grid">'
		. '<article class="cfhg-card cfhg-guide-card"><h3 class="cfhg-h3">' . esc_html( $charts['record']['title'] ) . '</h3>' . cfhg_guide_list( $charts['record']['points'] ) . '<p class="cfhg-guide__note">' . esc_html( $charts['record']['note'] ) . '</p></article>'
		. '<article class="cfhg-card cfhg-guide-card"><h3 class="cfhg-h3">' . esc_html( $charts['numbering']['title'] ) . '</h3>' . cfhg_guide_numbering( $charts['numbering'] ) . '<p class="cfhg-guide__note">' . esc_html( $charts['numbering']['note'] ) . '</p></article>'
		. '<article class="cfhg-card cfhg-guide-card"><h3 class="cfhg-h3">' . esc_html( $charts['family']['title'] ) . '</h3>' . cfhg_guide_list( $charts['family']['points'] ) . '</article>'
		. '</div></div></section>'
		. '<section class="cfhg-section cfhg-guide__cta" aria-labelledby="cfhg-guide-cta"><div class="cfhg-container"><div class="cfhg-card cfhg-guide__cta-card">' . cfhg_guide_cta_inner() . '</div></div></section>'
		. '</main>';
}

/**
 * The guide page as Elementor element data: native widgets holding the same
 * words and classes, so it can be edited in Elementor.
 *
 * @param array $image_map Theme image slug => attachment ID.
 * @return array
 */
function cfhg_elementor_guide_data( $image_map = array() ) {
	$c     = cfhg_guide_content();
	$base  = CFHG_URI . '/assets/video/';
	$image = static function ( $slug ) use ( $image_map ) {
		$manifest = cfhg_image_manifest();
		$id       = isset( $image_map[ $slug ] ) ? (int) $image_map[ $slug ] : 0;
		return cfhg_el_widget(
			'image',
			array(
				'image'        => array(
					'url'    => $id ? wp_get_attachment_image_url( $id, 'full' ) : cfhg_theme_image_url( $slug ),
					'id'     => $id ? $id : '',
					'alt'    => isset( $manifest[ $slug ] ) ? $manifest[ $slug ]['alt'] : '',
					'source' => 'library',
				),
				'image_size'   => 'large',
				'_css_classes' => 'cfhg-el-img cfhg-el-img--rounded cfhg-guide-step__media',
			)
		);
	};

	$sections = array();

	/* Title and introduction. */
	$hero_art   = $image( 'filling-in-a-family-tree-chart' );
	$hero_art['settings']['_css_classes'] = 'cfhg-el-img cfhg-postcard cfhg-postcard--gold';
	$hero_art['settings']['image_size']   = 'full';
	$sections[] = cfhg_el_section(
		'cfhg-el-guide-hero',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						50,
						array(
							cfhg_el_heading( esc_html( $c['title'] ), 'h1', 'cfhg-h1' ),
							cfhg_el_text( '<p>' . esc_html( $c['lead'] ) . '</p>', 'cfhg-lead cfhg-el-narrow' ),
							cfhg_el_text( cfhg_guide_jump_list(), 'cfhg-jump' ),
						)
					),
					cfhg_el_column( 44, array( $hero_art ) ),
				),
				array(
					'flex_align_items'     => 'center',
					'flex_justify_content' => 'space-between',
				)
			),
		),
		array(
			/* The page already starts below the header: just a little air. */
			'padding'        => cfhg_el_box( 'px', 48, 32, 0, 32 ),
			'padding_mobile' => cfhg_el_box( 'px', 32, 18, 0, 18 ),
		)
	);

	/* The explainer film: words on one side, the poster on the other; it opens in a lightbox. */
	$v          = $c['video'];
	$sections[] = cfhg_el_section(
		'cfhg-el-guide-film cfhg-guide__film',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						44,
						array(
							cfhg_el_heading( esc_html( $v['heading'] ), 'h2', 'cfhg-h2' ),
							cfhg_el_text( '<p>' . esc_html( $v['text'] ) . '</p>', 'cfhg-lead' ),
							cfhg_el_row(
								array(
									cfhg_el_button( $v['play'] . ', ' . $v['length'], $base . $v['file'], 'mark', 'cfhg-video-open' ),
									cfhg_el_button( $v['read'], '#cfhg-guide-steps', 'text' ),
								),
								array(
									'flex_direction_tablet' => 'row',
									'flex_wrap'             => 'wrap',
									'flex_align_items'      => 'center',
									'flex_gap'              => cfhg_el_gap( 20 ),
								)
							),
						),
						array( 'flex_gap' => cfhg_el_gap( 20 ) )
					),
					cfhg_el_column( 50, array( cfhg_el_shortcode( '[cfhg_video_lightbox]', 'cfhg-guide__film-player' ) ) ),
				),
				array(
					'flex_align_items'     => 'center',
					'flex_justify_content' => 'space-between',
				)
			),
		),
		array_merge( cfhg_el_page_rhythm(), array( '_element_id' => 'cfhg-guide-film' ) )
	);

	/* Your family tree: two questions. */
	$sections[] = cfhg_el_section(
		'cfhg-el-guide-before cfhg-guide__before',
		array(
			cfhg_el_heading( esc_html( $c['before']['heading'] ), 'h2', 'cfhg-h2' ),
			cfhg_el_text( cfhg_guide_list( $c['before']['questions'], 'cfhg-guide__questions' ), 'cfhg-body cfhg-el-list' ),
		),
		cfhg_el_page_rhythm()
	);

	/* The five steps, alternating sides. */
	$steps = array( cfhg_el_heading( esc_html( $c['steps_heading'] ), 'h2', 'cfhg-h2' ) );
	foreach ( $c['steps'] as $i => $step ) {
		$copy = array(
			cfhg_el_text( cfhg_guide_step_number( $i, $step ), 'cfhg-body' ),
			cfhg_el_heading( esc_html( $step['title'] ), 'h3', 'cfhg-h2 cfhg-guide-step__title' ),
		);
		if ( ! empty( $step['question'] ) ) {
			$copy[] = cfhg_el_text( '<p>' . esc_html( $step['question'] ) . '</p>', 'cfhg-lead cfhg-guide-step__question' );
		}
		$copy[]  = cfhg_el_text( cfhg_guide_step_body( $step ), 'cfhg-body cfhg-el-list' );
		$steps[] = cfhg_el_row(
			array(
				cfhg_el_column( 52, $copy, array( 'flex_gap' => cfhg_el_gap( 16 ) ) ),
				cfhg_el_column( 42, array( $image( $step['image'] ) ) ),
			),
			array(
				'flex_direction'       => 0 === $i % 2 ? 'row' : 'row-reverse',
				'flex_justify_content' => 'space-between',
				'flex_align_items'     => 'center',
				'css_classes'          => 'cfhg-el-guide-step cfhg-guide-step cfhg-guide-step--' . ( $i + 1 ),
				'_element_id'          => 'step-' . ( $i + 1 ),
			)
		);
	}
	$sections[] = cfhg_el_section( 'cfhg-el-guide-steps cfhg-guide__steps', $steps, array_merge( cfhg_el_page_rhythm(), array( 'flex_gap' => cfhg_el_gap( 64 ), '_element_id' => 'cfhg-guide-steps' ) ) );

	/* Completing charts: a navy band with three white cards. */
	$charts     = $c['charts'];
	$card       = static function ( $title, $html ) {
		return cfhg_el_card(
			array(
				cfhg_el_heading( esc_html( $title ), 'h3', 'cfhg-h3' ),
				cfhg_el_text( $html, 'cfhg-body cfhg-el-list' ),
			),
			array(
				'width'        => cfhg_el_size( 32, '%' ),
				'width_tablet' => cfhg_el_size( 100, '%' ),
				'css_classes'  => 'cfhg-guide-card',
			)
		);
	};
	$sections[] = cfhg_el_section(
		'cfhg-el-guide-charts cfhg-guide__charts',
		array(
			cfhg_el_heading( esc_html( $charts['heading'] ), 'h2', 'cfhg-h2' ),
			cfhg_el_text( '<p>' . esc_html( $charts['lead'] ) . '</p>', 'cfhg-lead' ),
			cfhg_el_row(
				array(
					$card( $charts['record']['title'], cfhg_guide_list( $charts['record']['points'] ) . '<p class="cfhg-guide__note">' . esc_html( $charts['record']['note'] ) . '</p>' ),
					$card( $charts['numbering']['title'], cfhg_guide_numbering( $charts['numbering'] ) . '<p class="cfhg-guide__note">' . esc_html( $charts['numbering']['note'] ) . '</p>' ),
					$card( $charts['family']['title'], cfhg_guide_list( $charts['family']['points'] ) ),
				),
				array(
					'flex_gap'         => cfhg_el_gap( 24 ),
					'flex_align_items' => 'stretch',
				)
			),
		),
		array(
			'padding'        => cfhg_el_box( 'px', 96, 32, 96, 32 ),
			'padding_tablet' => cfhg_el_box( 'px', 80, 24, 80, 24 ),
			'padding_mobile' => cfhg_el_box( 'px', 56, 18, 56, 18 ),
			'margin'         => cfhg_el_box( 'px', 104, 0, 0, 0 ),
			'margin_tablet'  => cfhg_el_box( 'px', 84, 0, 0, 0 ),
			'margin_mobile'  => cfhg_el_box( 'px', 60, 0, 0, 0 ),
			'_element_id'    => 'cfhg-guide-charts',
		)
	);

	/* Stuck? Bring it to a meeting. */
	$mail       = cfhg_mailto();
	$sections[] = cfhg_el_section(
		'cfhg-el-guide-cta cfhg-guide__cta',
		array(
			cfhg_el_card(
				array(
					cfhg_el_heading( esc_html( $c['cta']['heading'] ), 'h2', 'cfhg-h2' ),
					cfhg_el_text( '<p>' . esc_html( $c['cta']['text'] ) . '</p>', 'cfhg-lead' ),
					cfhg_el_shortcode( '[cfhg_next_meeting style="inline"]' ),
					cfhg_el_row(
						array_values(
							array_filter(
								array(
									cfhg_el_button( cfhg_mod( 'meeting_cta_label' ), '/#next-meeting', 'mark', 'cfhg-el-btn--big' ),
									$mail ? cfhg_el_button( cfhg_mod( 'meeting_secondary_label' ), $mail, 'text' ) : null,
								)
							)
						),
						array(
							'flex_direction_tablet' => 'row',
							'flex_wrap'             => 'wrap',
							'flex_align_items'      => 'center',
							'flex_gap'              => cfhg_el_gap( 20 ),
						)
					),
				),
				array( 'css_classes' => 'cfhg-guide__cta-card' )
			),
		),
		cfhg_el_page_rhythm()
	);

	return cfhg_el_strip_nulls( $sections );
}

/**
 * Create the guide page: built with Elementor when it is active, otherwise
 * a plain page using the theme template. Reuses the page if the address is
 * already taken by an earlier run.
 *
 * @return int|WP_Error Page ID.
 */
function cfhg_create_guide_page() {
	$c        = cfhg_guide_content();
	$existing = get_page_by_path( $c['slug'] );
	$page_id  = $existing ? (int) $existing->ID : wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => $c['title'],
			'post_name'   => $c['slug'],
			'post_excerpt' => $c['lead'],
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		return $page_id;
	}

	if ( function_exists( 'cfhg_elementor_is_active' ) && cfhg_elementor_is_active() ) {
		$images = array();
		foreach ( array_merge( array( 'filling-in-a-family-tree-chart' ), wp_list_pluck( $c['steps'], 'image' ) ) as $slug ) {
			$id = cfhg_import_theme_image( $slug );
			if ( $id ) {
				$images[ $slug ] = $id;
			}
		}
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( cfhg_elementor_guide_data( $images ) ) ) );
		if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	} else {
		update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-guide.php' );
	}

	return (int) $page_id;
}
