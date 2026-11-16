<?php
/**
 * Surname entries: the surnames members are researching, by island and
 * parish, so visitors researching the same family can be put in touch.
 *
 * Public: the surnames, the island and the parish or town.
 * Private (admin only, never printed on the site or exposed through the REST
 * API): which member is researching them, their email, and whether they
 * agreed to be put in touch.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register surname entries and their fields.
 */
function cfhg_site_register_surnames() {
	register_post_type(
		'cfhg_surname',
		array(
			'labels'          => array(
				'name'               => __( 'Surnames', 'cfhg-site' ),
				'singular_name'      => __( 'Surname entry', 'cfhg-site' ),
				'add_new'            => __( 'Add surnames', 'cfhg-site' ),
				'add_new_item'       => __( 'Add surnames being researched', 'cfhg-site' ),
				'edit_item'          => __( 'Edit surname entry', 'cfhg-site' ),
				'search_items'       => __( 'Search surnames', 'cfhg-site' ),
				'not_found'          => __( 'No surnames found.', 'cfhg-site' ),
				'not_found_in_trash' => __( 'No surnames in the bin.', 'cfhg-site' ),
				'all_items'          => __( 'All surnames', 'cfhg-site' ),
				'menu_name'          => __( 'Surnames', 'cfhg-site' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_position'   => 6,
			'menu_icon'       => 'dashicons-groups',
			'supports'        => array( 'title', 'revisions' ),
			'capability_type' => 'post',
		)
	);

	$fields = array(
		'_cfhg_parish'       => 'sanitize_text_field',
		'_cfhg_member_name'  => 'sanitize_text_field',
		'_cfhg_member_email' => 'sanitize_email',
		'_cfhg_consent'      => 'sanitize_key',
	);
	foreach ( $fields as $key => $sanitize ) {
		register_post_meta(
			'cfhg_surname',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => $sanitize,
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'cfhg_site_register_surnames' );

/**
 * Surnames as typed ("Cox/Blackman/ Ford") tidied to "Cox / Blackman / Ford".
 *
 * @param string $text Surnames.
 * @return string
 */
function cfhg_site_tidy_surnames( $text ) {
	$names = array_filter( array_map( 'trim', preg_split( '#\s*[/,;]\s*#', (string) $text ) ) );
	return implode( ' / ', $names );
}

/**
 * Public surname entries, A to Z by island, parish and surname.
 *
 * @param string $island Island slug, or '' for all.
 * @return array<int, array{surnames: string, island: string, island_slug: string, parish: string}>
 */
function cfhg_site_surnames( $island = '' ) {
	$args = array(
		'post_type'      => 'cfhg_surname',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	);
	if ( 'others' === $island && function_exists( 'cfhg_site_island_slugs' ) ) {
		/* Every island that has no guide of its own. */
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'cfhg_island',
				'field'    => 'slug',
				'terms'    => cfhg_site_island_slugs(),
				'operator' => 'NOT IN',
			),
		);
	} elseif ( $island ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'cfhg_island',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $island ) ),
			),
		);
	}
	$rows = array();
	foreach ( get_posts( $args ) as $post ) {
		$terms  = get_the_terms( $post, 'cfhg_island' );
		$rows[] = array(
			'surnames'    => cfhg_site_tidy_surnames( $post->post_title ),
			'island'      => is_array( $terms ) ? $terms[0]->name : '',
			'island_slug' => is_array( $terms ) ? $terms[0]->slug : '',
			'parish'      => (string) get_post_meta( $post->ID, '_cfhg_parish', true ),
		);
	}
	usort(
		$rows,
		static function ( $a, $b ) {
			return strcasecmp( $a['island'] . '|' . ( '' === $a['parish'] ? '~' : $a['parish'] ) . '|' . $a['surnames'], $b['island'] . '|' . ( '' === $b['parish'] ? '~' : $b['parish'] ) . '|' . $b['surnames'] );
		}
	);
	return $rows;
}

/**
 * [cfhg_surnames island="jamaica" search="yes"]: the surnames being
 * researched, with a filter-as-you-type box, and the invitation to get in
 * touch. Without island="…" it lists every island.
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_surnames_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'island' => '',
			'search' => 'yes',
		),
		$atts,
		'cfhg_surnames'
	);
	$rows = cfhg_site_surnames( $atts['island'] );
	$mail = function_exists( 'cfhg_mailto' ) ? cfhg_mailto() : '';

	$invite = '<p class="cfhg-surnames__invite">' . esc_html__( 'Researching one of these names?', 'cfhg-site' ) . ' '
		. ( $mail ? '<a href="' . esc_url( $mail ) . '">' . esc_html__( 'Email us', 'cfhg-site' ) . '</a>' : esc_html__( 'Email us', 'cfhg-site' ) )
		. ' ' . esc_html__( 'and we’ll put you in touch with the member researching them. Members: send us your names to add.', 'cfhg-site' ) . '</p>';

	if ( ! $rows ) {
		return '<div class="cfhg-surnames"><p class="cfhg-surnames__empty">' . esc_html__( 'No surnames listed yet.', 'cfhg-site' ) . '</p>' . $invite . '</div>';
	}

	wp_enqueue_script( 'cfhg-site' );
	$all  = '' === $atts['island'] || 'others' === $atts['island'] || false !== strpos( $atts['island'], ',' );
	$id   = wp_unique_id( 'cfhg-surnames-' );
	$html = '<div class="cfhg-surnames" data-cfhg-surnames>';
	if ( 'no' !== $atts['search'] ) {
		$html .= '<p class="cfhg-surnames__search"><label for="' . esc_attr( $id ) . '">' . esc_html__( 'Find a surname', 'cfhg-site' ) . '</label> <input type="search" id="' . esc_attr( $id ) . '" placeholder="' . esc_attr__( 'e.g. Grant', 'cfhg-site' ) . '" autocomplete="off" data-cfhg-surnames-filter></p>';
	}
	$html .= '<table class="cfhg-surnames__table"><thead><tr><th scope="col">' . esc_html__( 'Surnames', 'cfhg-site' ) . '</th>'
		. ( $all ? '<th scope="col">' . esc_html__( 'Island', 'cfhg-site' ) . '</th>' : '' )
		. '<th scope="col">' . esc_html__( 'Parish or town', 'cfhg-site' ) . '</th></tr></thead><tbody>';
	foreach ( $rows as $row ) {
		$html .= '<tr><td class="cfhg-surnames__names">' . esc_html( $row['surnames'] ) . '</td>'
			. ( $all ? '<td>' . esc_html( $row['island'] ) . '</td>' : '' )
			. '<td>' . ( '' === $row['parish'] ? '<span class="cfhg-surnames__any">' . esc_html__( 'Anywhere on the island', 'cfhg-site' ) . '</span>' : esc_html( $row['parish'] ) ) . '</td></tr>';
	}
	$html .= '</tbody></table><p class="cfhg-surnames__none" hidden>' . esc_html__( 'No surnames match. Email us anyway: someone may know the family.', 'cfhg-site' ) . '</p>';
	return $html . $invite . '</div>';
}
add_shortcode( 'cfhg_surnames', 'cfhg_site_surnames_shortcode' );

/**
 * [cfhg_surname_ribbon island="" label="…" url=""]: a slow band of the
 * surnames members are tracing, written in capitals the way family
 * historians write them. Each entry gives its first spelling. The band is
 * a plain list; the theme's CSS sets it moving and pauses it on hover, on
 * focus and for anyone who prefers reduced motion. Needs six names or more.
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_surname_ribbon_shortcode( $atts ) {
	$atts  = shortcode_atts(
		array(
			'island' => '',
			'label'  => __( 'Names our members are tracing', 'cfhg-site' ),
			'url'    => '',
		),
		$atts,
		'cfhg_surname_ribbon'
	);
	$names = array();
	foreach ( cfhg_site_surnames( $atts['island'] ) as $row ) {
		$first = trim( preg_split( '#[/,(]#', $row['surnames'] )[0] );
		if ( '' !== $first ) {
			$names[ strtolower( $first ) ] = $first;
		}
	}
	if ( count( $names ) < 6 ) {
		return '';
	}
	ksort( $names );
	$items = '';
	foreach ( $names as $name ) {
		$items .= '<li>' . esc_html( $name ) . '</li>';
	}
	$label = esc_html( $atts['label'] );
	if ( $atts['url'] ) {
		$label = '<a href="' . esc_url( $atts['url'] ) . '">' . $label . '</a>';
	}
	return '<div class="cfhg-ribbon" style="--cfhg-ribbon-n:' . count( $names ) . '">'
		. '<p class="cfhg-ribbon__label">' . $label . '</p>'
		. '<div class="cfhg-ribbon__view"><ul class="cfhg-ribbon__names">' . $items . '</ul><ul class="cfhg-ribbon__names" aria-hidden="true">' . $items . '</ul></div>'
		. '</div>';
}
add_shortcode( 'cfhg_surname_ribbon', 'cfhg_site_surname_ribbon_shortcode' );
