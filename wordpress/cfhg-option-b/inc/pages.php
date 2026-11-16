<?php
/**
 * The information pages: Who we are, Join the group, Contact, Stories, and
 * the Photography and Privacy notices.
 *
 * Their words live in inc/pages-content.json: a title, an opening line, an
 * optional photograph, and sections of a heading, some text and any live
 * shortcodes. Appearance > CFHG Setup builds them in Elementor (so every
 * word can be edited there) or, without Elementor, as ordinary pages.
 *
 * {email} in the text becomes the group's address as a mailto link.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * All the pages, by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function cfhg_pages_content() {
	static $pages = null;
	if ( null === $pages ) {
		$file  = CFHG_DIR . '/inc/pages-content.json';
		$pages = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		$pages = is_array( $pages ) ? $pages : array();
	}
	return $pages;
}

/**
 * Put the group's email address into a section's text.
 *
 * @param string $html Text with {email} in it.
 * @return string
 */
function cfhg_pages_text( $html ) {
	$email = sanitize_email( cfhg_mod( 'contact_email' ) );
	$link  = $email ? '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>' : '';
	return str_replace( '{email}', $link, $html );
}

/**
 * A page's sections as plain markup, for a site without Elementor.
 *
 * @param array $page Page.
 * @return string
 */
function cfhg_pages_plain_html( $page ) {
	$html = '';
	foreach ( $page['sections'] as $s ) {
		$html .= $s['heading'] ? '<h2>' . esc_html( $s['heading'] ) . '</h2>' : '';
		$html .= cfhg_pages_text( $s['html'] );
		foreach ( isset( $s['shortcodes'] ) ? $s['shortcodes'] : array() as $code ) {
			$html .= $code;
		}
	}
	return $html;
}

/**
 * The Elementor widgets for one page.
 *
 * @param string $slug     Page slug.
 * @param int    $image_id Media Library ID of the photograph, if imported.
 * @return array
 */
function cfhg_elementor_content_page_data( $slug, $image_id = 0 ) {
	$pages = cfhg_pages_content();
	$page  = $pages[ $slug ];
	$mail  = cfhg_mailto();

	/* Opening: the title, the opening line and, if there is one, a postcard. */
	$copy = array(
		cfhg_el_heading( esc_html( $page['title'] ), 'h1', 'cfhg-h1' ),
		cfhg_el_text( '<p>' . esc_html( $page['lead'] ) . '</p>', 'cfhg-lead cfhg-el-narrow' ),
	);
	$hero = array( cfhg_el_column( $page['image'] ? 50 : 100, $copy ) );
	if ( $page['image'] ) {
		$manifest = cfhg_image_manifest();
		$tone     = in_array( $page['tone'], array( 'sea', 'gold' ), true ) ? ' cfhg-postcard--' . $page['tone'] : '';
		$hero[]   = cfhg_el_column(
			44,
			array(
				cfhg_el_widget(
					'image',
					array(
						'image'        => array(
							'url'    => $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : cfhg_theme_image_url( $page['image'] ),
							'id'     => $image_id ? $image_id : '',
							'alt'    => isset( $manifest[ $page['image'] ] ) ? $manifest[ $page['image'] ]['alt'] : '',
							'source' => 'library',
						),
						'image_size'   => 'full',
						'_css_classes' => 'cfhg-el-img cfhg-postcard' . $tone,
					)
				),
			)
		);
	}
	$sections   = array();
	$sections[] = cfhg_el_section(
		'cfhg-el-page-hero',
		array(
			cfhg_el_row(
				$hero,
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

	foreach ( $page['sections'] as $s ) {
		$items = array();
		if ( $s['heading'] ) {
			$items[] = cfhg_el_heading( esc_html( $s['heading'] ), 'h2', 'cfhg-h2' );
		}
		if ( $s['html'] ) {
			$items[] = cfhg_el_text( cfhg_pages_text( $s['html'] ), 'cfhg-body cfhg-el-prose' );
		}
		foreach ( isset( $s['shortcodes'] ) ? $s['shortcodes'] : array() as $code ) {
			$items[] = cfhg_el_shortcode( $code );
		}
		$sections[] = cfhg_el_section(
			'cfhg-el-page-section cfhg-page__section',
			$items,
			array_merge( cfhg_el_page_rhythm(), array( 'flex_gap' => cfhg_el_gap( 20 ), '_element_id' => 'cfhg-page-' . $s['id'] ) )
		);
	}

	if ( ! empty( $page['cta'] ) ) {
		$sections[] = cfhg_el_section(
			'cfhg-guide__cta',
			array(
				cfhg_el_card(
					array(
						cfhg_el_heading( esc_html__( 'Come to a meeting', 'cfhg' ), 'h2', 'cfhg-h2' ),
						cfhg_el_text( '<p>' . esc_html__( 'Bring what you know and what you’re stuck on. Your first visit is free.', 'cfhg' ) . '</p>', 'cfhg-lead' ),
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
	}
	return cfhg_el_strip_nulls( $sections );
}

/**
 * Create (or rebuild) one information page.
 *
 * @param string $slug Page slug.
 * @return int|WP_Error Page ID.
 */
function cfhg_create_content_page( $slug ) {
	$pages = cfhg_pages_content();
	if ( empty( $pages[ $slug ] ) ) {
		return new WP_Error( 'cfhg_no_page', __( 'There is no page with that name.', 'cfhg' ) );
	}
	$page     = $pages[ $slug ];
	$existing = get_page_by_path( $slug );
	$args     = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $page['title'],
		'post_name'    => $slug,
		'post_excerpt' => $page['lead'],
	);
	$page_id  = $existing ? (int) $existing->ID : wp_insert_post( $args, true );
	if ( is_wp_error( $page_id ) ) {
		return $page_id;
	}

	if ( cfhg_elementor_is_active() ) {
		$image_id = $page['image'] ? cfhg_import_theme_image( $page['image'] ) : 0;
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( cfhg_elementor_content_page_data( $slug, $image_id ) ) ) );
		if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	} else {
		delete_post_meta( $page_id, '_elementor_edit_mode' );
		wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => cfhg_pages_plain_html( $page ),
			)
		);
	}
	return (int) $page_id;
}

/**
 * Create all of them.
 *
 * @return array<string, int|WP_Error> Page IDs by slug.
 */
function cfhg_create_content_pages() {
	$ids = array();
	foreach ( cfhg_pages_content() as $slug => $page ) {
		/* A page marked "preview" is for the client copy only: build it with cfhg_create_content_page(). */
		if ( ! empty( $page['preview'] ) ) {
			continue;
		}
		$ids[ $slug ] = cfhg_create_content_page( $slug );
	}
	return $ids;
}

/**
 * [cfhg_story_grid count="12" island=""]: the stories as a grid of cards,
 * newest first. With island="jamaica" only that island's, or nothing.
 */
add_shortcode(
	'cfhg_story_grid',
	static function ( $atts ) {
		$atts  = shortcode_atts(
			array(
				'count'  => 12,
				'island' => '',
			),
			$atts,
			'cfhg_story_grid'
		);
		$cards = array();
		if ( $atts['island'] ) {
			/* One island's stories: real posts only, and nothing at all if there are none. */
			$posts = get_posts(
				array(
					'post_type'      => 'post',
					'posts_per_page' => max( 1, (int) $atts['count'] ),
					'category_name'  => sanitize_title( cfhg_mod( 'stories_category' ) ),
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'cfhg_island',
							'field'    => 'slug',
							'terms'    => array( sanitize_title( $atts['island'] ) ),
						),
					),
				)
			);
			foreach ( $posts as $post ) {
				$cards[] = array(
					'meta'  => get_the_date( '', $post ),
					'title' => get_the_title( $post ),
					'text'  => wp_strip_all_tags( get_the_excerpt( $post ) ),
					'url'   => get_permalink( $post ),
					'image' => has_post_thumbnail( $post ) ? get_the_post_thumbnail( $post, 'cfhg-card', array( 'sizes' => '(min-width: 1024px) 34vw, 90vw' ) ) : '',
				);
			}
			if ( ! $cards ) {
				return '';
			}
		} else {
			$cards = cfhg_story_cards( (int) $atts['count'] );
		}
		$html = '<ul class="cfhg-story-grid">';
		foreach ( $cards as $card ) {
			$html .= '<li class="cfhg-story-card">'
				. ( $card['image'] ? '<a class="cfhg-story-card__media" href="' . esc_url( cfhg_url( $card['url'] ) ) . '" tabindex="-1" aria-hidden="true">' . $card['image'] . '</a>' : '' )
				. '<div class="cfhg-story-card__body">'
				. ( $card['meta'] ? '<p class="cfhg-story-card__meta">' . esc_html( $card['meta'] ) . '</p>' : '' )
				. '<h3 class="cfhg-story-card__title"><a href="' . esc_url( cfhg_url( $card['url'] ) ) . '">' . esc_html( $card['title'] ) . '</a></h3>'
				. ( $card['text'] ? '<p class="cfhg-story-card__text">' . esc_html( $card['text'] ) . '</p>' : '' )
				. '</div></li>';
		}
		return $html . '</ul>';
	}
);

/**
 * Addresses that lead to a page which lives somewhere else. The Jamaica page
 * is at /jamaica/ until the Research section exists to hold it.
 *
 * @return array<string, string>
 */
function cfhg_page_redirects() {
	return apply_filters(
		'cfhg_page_redirects',
		array(
			'/research/jamaica/'             => '/jamaica/',
			'/research/barbados/'            => '/barbados/',
			'/research/st-kitts-and-nevis/'  => '/st-kitts-and-nevis/',
			'/research/trinidad-and-tobago/' => '/trinidad-and-tobago/',
			'/research/other-islands/'       => '/other-islands/',
			'/research/get-help/'            => '/contact/',
		)
	);
}

/**
 * WordPress guesses at a missing address and sends people to the nearest
 * match, so /research/ would land on a story about researching Trinidad.
 * Say "not found" instead, apart from the addresses listed above.
 */
add_filter( 'do_redirect_guess_404_permalink', '__return_false' );

add_action(
	'template_redirect',
	static function () {
		if ( ! is_404() ) {
			return;
		}
		$path = '/' . trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH ), '/' ) . '/';
		$map  = cfhg_page_redirects();
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
	}
);
