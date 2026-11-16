<?php
/**
 * Island pages ("Research > Jamaica"): the island guide from the CFHG Site
 * plugin, laid out in Option B's style.
 *
 * Sections, top to bottom:
 *  1. The island: name, introduction, its sub-group's next meeting, and the
 *     illustration.
 *  2. Where should I look? (the plugin's [cfhg_record_finder]).
 *  3. Explore the parishes ([cfhg_parish_map]).
 *  4. Where to look: the main record sources ([cfhg_island_sources]).
 *  5. Surnames being researched ([cfhg_surnames]).
 *  6. How members researched the island: stories, written by the group.
 *  7. From the group: the island's events and posts.
 *  8. Come to a meeting.
 *
 * Two ways to show it, like the homepage and the five steps page:
 * - Theme: a page using the "CFHG Island" template. The island is the page's
 *   address (/jamaica/) unless Page > Island says otherwise.
 * - Elementor: Appearance > CFHG Setup > "Create an island page" builds it
 *   from Elementor widgets, so the group can edit the words and add stories.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fixed words on island pages. {island} becomes the island's name.
 *
 * @return array<string, string>
 */
function cfhg_island_text() {
	return array(
		'finder_heading'  => __( 'Where should I look?', 'cfhg' ),
		'finder_lead'     => __( 'Tell us a little about who you’re looking for, and we’ll suggest the records to try first.', 'cfhg' ),
		'parish_heading'  => __( 'Explore the {areas}', 'cfhg' ),
		'sources_heading' => __( 'The main record sources', 'cfhg' ),
		'names_heading'   => __( 'Surnames we’re researching in {island}', 'cfhg' ),
		'stories_heading' => __( 'How members researched {island}', 'cfhg' ),
		'stories_lead'    => __( 'Talks, tips and members’ accounts from our meetings.', 'cfhg' ),
		'story_todo'      => __( 'Story to come. A member’s account of researching a {island} family: who they were looking for, where they looked and what they found. Named or anonymous, and with a photograph if they’d like one.', 'cfhg' ),
		'from_heading'    => __( 'From the group', 'cfhg' ),
		'events_heading'  => __( 'Coming up', 'cfhg' ),
		'events_empty'    => __( 'No {island} events planned yet. The sub-group’s dates will appear here.', 'cfhg' ),
		'posts_heading'   => __( 'Recent posts', 'cfhg' ),
		'posts_empty'     => __( 'News and stories about {island} will appear here.', 'cfhg' ),
		'timeline_heading' => __( 'What survives, and from when', 'cfhg' ),
		'timeline_lead'   => __( 'Each bar is a set of records and the years it covers. Find the years your ancestor was alive, and read down to see what could name them.', 'cfhg' ),
		'jump_finder'     => __( 'Where should I look?', 'cfhg' ),
		'jump_timeline'   => __( 'Records by year', 'cfhg' ),
		'jump_parishes'   => __( '{Areas}', 'cfhg' ),
		'jump_sources'    => __( 'Where to search', 'cfhg' ),
		'jump_names'      => __( 'Surnames', 'cfhg' ),
		'cta_heading'     => __( 'Researching {island}? Come along', 'cfhg' ),
		'cta_text'        => __( 'Bring what you know and what you’re stuck on. Your first visit is free.', 'cfhg' ),
	);
}

/**
 * One fixed phrase with the island's name in.
 *
 * @param string $key  Key in cfhg_island_text().
 * @param string $name Island name.
 * @return string
 */
function cfhg_island_say( $key, $name ) {
	$text  = cfhg_island_text();
	$areas = __( 'parishes', 'cfhg' );
	if ( function_exists( 'cfhg_site_island' ) && function_exists( 'cfhg_site_area_word' ) ) {
		foreach ( cfhg_site_island_slugs() as $slug ) {
			$guide = cfhg_site_island( $slug );
			if ( $guide && $guide['name'] === $name ) {
				$areas = cfhg_site_area_word( $guide, 'many' );
			}
		}
	}
	return str_replace(
		array( '{island}', '{areas}', '{Areas}' ),
		array( $name, $areas, ucfirst( $areas ) ),
		isset( $text[ $key ] ) ? $text[ $key ] : ''
	);
}

/**
 * Which island a page is about: the page's Island field, else its address.
 *
 * @param int $post_id Page.
 * @return string Slug, or '' if there is no guide for it.
 */
function cfhg_island_for_page( $post_id ) {
	if ( ! function_exists( 'cfhg_site_island' ) ) {
		return '';
	}
	$slug = (string) get_post_meta( $post_id, '_cfhg_island', true );
	$slug = $slug ? $slug : (string) get_post_field( 'post_name', $post_id );
	return cfhg_site_island( $slug ) ? $slug : '';
}

/**
 * The opening section's text column.
 *
 * @param array $guide Island guide.
 * @return string
 */
function cfhg_island_hero_copy( $guide ) {
	return '<p class="cfhg-lead">' . esc_html( $guide['intro'] ) . '</p>'
		. '<nav class="cfhg-jump" aria-label="' . esc_attr__( 'On this page', 'cfhg' ) . '">' . cfhg_island_jump_list( $guide ) . '</nav>'
		. '<div class="cfhg-island__next">' . do_shortcode( '[cfhg_events group="' . esc_attr( $guide['slug'] ) . '" limit="1" show_group="no" empty="none"]' ) . '</div>';
}

/**
 * Links from the top of an island page to its sections.
 *
 * @param array $guide Island guide.
 * @return string A list.
 */
function cfhg_island_jump_list( $guide ) {
	$html = '<ul>';
	foreach ( array( 'finder', 'timeline', 'parishes', 'sources', 'names' ) as $n => $key ) {
		$html .= '<li><a href="#cfhg-island-' . $key . '">' . esc_html( cfhg_island_say( 'jump_' . $key, $guide['name'] ) ) . '</a></li>';
		if ( 2 === $n ) {
			$html .= '<li class="cfhg-jump__break" aria-hidden="true"></li>';
		}
	}
	return $html . '</ul>';
}

/**
 * The stories placeholders, for the group to replace.
 *
 * @param array $guide Island guide.
 * @return string
 */
function cfhg_island_story_placeholders( $guide ) {
	return do_shortcode( '[cfhg_story_grid island="' . esc_attr( $guide['slug'] ) . '" count="3"]' );
}

/**
 * The whole page, for the theme template.
 *
 * @param string $slug Island slug.
 * @return string
 */
function cfhg_island_markup( $slug ) {
	$guide = function_exists( 'cfhg_site_island' ) ? cfhg_site_island( $slug ) : null;
	if ( ! $guide ) {
		return '';
	}
	$name = $guide['name'];
	$s    = esc_attr( $guide['slug'] );
	$mail = cfhg_mailto();

	$section = static function ( $class, $id, $heading, $body, $lead = '' ) {
		return '<section class="cfhg-section cfhg-island__section ' . esc_attr( $class ) . '" id="' . esc_attr( $id ) . '" aria-labelledby="' . esc_attr( $id ) . '-title"><div class="cfhg-container">'
			. '<h2 class="cfhg-h2" id="' . esc_attr( $id ) . '-title">' . esc_html( $heading ) . '</h2>'
			. ( $lead ? '<p class="cfhg-lead cfhg-island__lead">' . esc_html( $lead ) . '</p>' : '' )
			. $body . '</div></section>';
	};

	return '<main id="main" class="cfhg-main cfhg-island cfhg-island--' . $s . '">'
		. '<header class="cfhg-island__hero"><div class="cfhg-container cfhg-island__hero-grid">'
		. '<div class="cfhg-island__copy"><h1 class="cfhg-h1">' . esc_html( $name ) . '</h1>' . cfhg_island_hero_copy( $guide ) . '</div>'
		. '<figure class="cfhg-island__art" data-cfhg-parallax="1">' . cfhg_theme_image( $guide['illustration'], array( 'sizes' => '(min-width: 1024px) 46vw, 92vw', 'loading' => 'eager' ) ) . '</figure>'
		. '</div></header>'
		. $section( 'cfhg-island__finder', 'cfhg-island-finder', cfhg_island_say( 'finder_heading', $name ), do_shortcode( '[cfhg_record_finder island="' . $s . '"]' ), cfhg_island_say( 'finder_lead', $name ) )
		. $section( 'cfhg-island__timeline', 'cfhg-island-timeline', cfhg_island_say( 'timeline_heading', $name ), do_shortcode( '[cfhg_record_timeline island="' . $s . '"]' ), cfhg_island_say( 'timeline_lead', $name ) )
		. $section( 'cfhg-island__parishes', 'cfhg-island-parishes', cfhg_island_say( 'parish_heading', $name ), do_shortcode( '[cfhg_parish_map island="' . $s . '"]' ) )
		. $section( 'cfhg-island__sources', 'cfhg-island-sources', cfhg_island_say( 'sources_heading', $name ), do_shortcode( '[cfhg_island_sources island="' . $s . '"]' ) )
		. $section( 'cfhg-island__names', 'cfhg-island-names', cfhg_island_say( 'names_heading', $name ), do_shortcode( '[cfhg_surnames island="' . $s . '"]' ) )
		. $section( 'cfhg-island__stories', 'cfhg-island-stories', cfhg_island_say( 'stories_heading', $name ), cfhg_island_story_placeholders( $guide ), cfhg_island_say( 'stories_lead', $name ) )
		. $section(
			'cfhg-island__from',
			'cfhg-island-from',
			cfhg_island_say( 'from_heading', $name ),
			'<div class="cfhg-island__from-grid"><div><h3 class="cfhg-h3">' . esc_html( cfhg_island_say( 'events_heading', $name ) ) . '</h3>' . do_shortcode( '[cfhg_events island="' . $s . '" limit="4" empty="' . esc_attr( cfhg_island_say( 'events_empty', $name ) ) . '"]' ) . '</div>'
			. '<div><h3 class="cfhg-h3">' . esc_html( cfhg_island_say( 'posts_heading', $name ) ) . '</h3>' . do_shortcode( '[cfhg_island_posts island="' . $s . '" limit="4" not_in="stories" empty="' . esc_attr( cfhg_island_say( 'posts_empty', $name ) ) . '"]' ) . '</div></div>'
		)
		. '<section class="cfhg-section cfhg-guide__cta" aria-labelledby="cfhg-island-cta"><div class="cfhg-container"><div class="cfhg-card cfhg-guide__cta-card">'
		. '<h2 class="cfhg-h2" id="cfhg-island-cta">' . esc_html( cfhg_island_say( 'cta_heading', $name ) ) . '</h2>'
		. '<p class="cfhg-lead">' . esc_html( cfhg_island_say( 'cta_text', $name ) ) . '</p>'
		. cfhg_component_next_meeting( 'inline' )
		. '<div class="cfhg-actions">' . cfhg_button( cfhg_mod( 'meeting_cta_label' ), home_url( '/#next-meeting' ), 'mark', 'cfhg-btn--big' ) . ( $mail ? cfhg_button( __( 'Ask a research question', 'cfhg' ), $mail, 'text' ) : '' ) . '</div>'
		. '</div></div></section>'
		. '</main>';
}

/**
 * The island page as Elementor element data.
 *
 * @param string $slug     Island slug.
 * @param int    $image_id Media Library ID of the illustration, if imported.
 * @return array
 */
function cfhg_elementor_island_data( $slug, $image_id = 0 ) {
	$guide = cfhg_site_island( $slug );
	$name  = $guide['name'];
	$s     = $guide['slug'];
	$mail  = cfhg_mailto();
	$block = static function ( $class, $heading, $children, $lead = '', $id = '' ) use ( $name ) {
		$items = array( cfhg_el_heading( esc_html( $heading ), 'h2', 'cfhg-h2' ) );
		if ( $lead ) {
			$items[] = cfhg_el_text( '<p>' . esc_html( $lead ) . '</p>', 'cfhg-lead cfhg-el-narrow' );
		}
		return cfhg_el_section(
			'cfhg-island__section ' . $class,
			array_merge( $items, $children ),
			array_merge( cfhg_el_page_rhythm(), array( 'flex_gap' => cfhg_el_gap( 24 ), '_element_id' => $id ) )
		);
	};
	$manifest = cfhg_image_manifest();

	$sections   = array();
	$sections[] = cfhg_el_section(
		'cfhg-island__hero',
		array(
			cfhg_el_row(
				array(
					cfhg_el_column(
						50,
						array(
							cfhg_el_heading( esc_html( $name ), 'h1', 'cfhg-h1' ),
							cfhg_el_text( '<p>' . esc_html( $guide['intro'] ) . '</p>', 'cfhg-lead' ),
							cfhg_el_text( cfhg_island_jump_list( $guide ), 'cfhg-jump' ),
							cfhg_el_shortcode( '[cfhg_events group="' . $s . '" limit="1" show_group="no" empty="none"]', 'cfhg-island__next' ),
						)
					),
					cfhg_el_column(
						46,
						array(
							cfhg_el_widget(
								'image',
								array(
									'image'        => array(
										'url'    => $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : cfhg_theme_image_url( $guide['illustration'] ),
										'id'     => $image_id ? $image_id : '',
										'alt'    => isset( $manifest[ $guide['illustration'] ] ) ? $manifest[ $guide['illustration'] ]['alt'] : '',
										'source' => 'library',
									),
									'image_size'   => 'full',
									'_css_classes' => 'cfhg-el-img cfhg-island__art',
								)
							),
						)
					),
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
	$sections[] = $block( 'cfhg-island__finder', cfhg_island_say( 'finder_heading', $name ), array( cfhg_el_shortcode( '[cfhg_record_finder island="' . $s . '"]' ) ), cfhg_island_say( 'finder_lead', $name ), 'cfhg-island-finder' );
	$sections[] = $block( 'cfhg-island__timeline', cfhg_island_say( 'timeline_heading', $name ), array( cfhg_el_shortcode( '[cfhg_record_timeline island="' . $s . '"]' ) ), cfhg_island_say( 'timeline_lead', $name ), 'cfhg-island-timeline' );
	$sections[] = $block( 'cfhg-island__parishes', cfhg_island_say( 'parish_heading', $name ), array( cfhg_el_shortcode( '[cfhg_parish_map island="' . $s . '"]' ) ), '', 'cfhg-island-parishes' );
	$sections[] = $block( 'cfhg-island__sources', cfhg_island_say( 'sources_heading', $name ), array( cfhg_el_shortcode( '[cfhg_island_sources island="' . $s . '"]' ) ), '', 'cfhg-island-sources' );
	$sections[] = $block( 'cfhg-island__names', cfhg_island_say( 'names_heading', $name ), array( cfhg_el_shortcode( '[cfhg_surnames island="' . $s . '"]' ) ), '', 'cfhg-island-names' );
	$sections[] = $block( 'cfhg-island__stories', cfhg_island_say( 'stories_heading', $name ), array( cfhg_el_shortcode( '[cfhg_story_grid island="' . $s . '" count="3"]' ) ), cfhg_island_say( 'stories_lead', $name ) );
	$sections[] = $block(
		'cfhg-island__from',
		cfhg_island_say( 'from_heading', $name ),
		array(
			cfhg_el_row(
				array(
					cfhg_el_column( 48, array( cfhg_el_heading( esc_html( cfhg_island_say( 'events_heading', $name ) ), 'h3', 'cfhg-h3' ), cfhg_el_shortcode( '[cfhg_events island="' . $s . '" limit="4" empty="' . esc_attr( cfhg_island_say( 'events_empty', $name ) ) . '"]' ) ) ),
					cfhg_el_column( 48, array( cfhg_el_heading( esc_html( cfhg_island_say( 'posts_heading', $name ) ), 'h3', 'cfhg-h3' ), cfhg_el_shortcode( '[cfhg_island_posts island="' . $s . '" limit="4" not_in="stories" empty="' . esc_attr( cfhg_island_say( 'posts_empty', $name ) ) . '"]' ) ) ),
				),
				array( 'flex_justify_content' => 'space-between' )
			),
		)
	);
	$sections[] = cfhg_el_section(
		'cfhg-guide__cta',
		array(
			cfhg_el_card(
				array(
					cfhg_el_heading( esc_html( cfhg_island_say( 'cta_heading', $name ) ), 'h2', 'cfhg-h2' ),
					cfhg_el_text( '<p>' . esc_html( cfhg_island_say( 'cta_text', $name ) ) . '</p>', 'cfhg-lead' ),
					cfhg_el_shortcode( '[cfhg_next_meeting style="inline"]' ),
					cfhg_el_row(
						array_values(
							array_filter(
								array(
									cfhg_el_button( cfhg_mod( 'meeting_cta_label' ), '/#next-meeting', 'mark', 'cfhg-el-btn--big' ),
									$mail ? cfhg_el_button( __( 'Ask a research question', 'cfhg' ), $mail, 'text' ) : null,
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
		array(
			'padding'        => cfhg_el_box( 'px', 104, 32, 0, 32 ),
			'padding_tablet' => cfhg_el_box( 'px', 84, 24, 0, 24 ),
			'padding_mobile' => cfhg_el_box( 'px', 60, 18, 0, 18 ),
		)
	);
	return cfhg_el_strip_nulls( $sections );
}

/**
 * Create (or rebuild) an island page at /island-slug/: built with Elementor
 * when it is active, otherwise using the theme template.
 *
 * @param string $slug Island slug.
 * @return int|WP_Error Page ID.
 */
function cfhg_create_island_page( $slug ) {
	$guide = function_exists( 'cfhg_site_island' ) ? cfhg_site_island( $slug ) : null;
	if ( ! $guide ) {
		return new WP_Error( 'cfhg_no_island', __( 'There is no guide for that island in the CFHG Site plugin.', 'cfhg' ) );
	}
	$existing = get_page_by_path( $guide['slug'] );
	$page_id  = $existing ? (int) $existing->ID : wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $guide['name'],
			'post_name'    => $guide['slug'],
			'post_excerpt' => $guide['intro'],
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		return $page_id;
	}
	update_post_meta( $page_id, '_cfhg_island', $guide['slug'] );

	if ( cfhg_elementor_is_active() ) {
		$image_id = cfhg_import_theme_image( $guide['illustration'] );
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( cfhg_elementor_island_data( $guide['slug'], $image_id ) ) ) );
		if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	} else {
		update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-island.php' );
	}
	return (int) $page_id;
}
