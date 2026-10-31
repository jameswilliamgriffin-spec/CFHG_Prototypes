<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme features.
 */
function cfhg_c_setup() {
	load_theme_textdomain( 'caribbean-family-history-c', CFHG_C_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 160,
			'width'       => 542,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'cfhg_c_primary' => __( 'Main navigation bar', 'caribbean-family-history-c' ),
			'cfhg_c_utility' => __( 'Header utility links', 'caribbean-family-history-c' ),
		)
	);

	add_image_size( 'cfhg-c-card', 640, 427, true );
	add_image_size( 'cfhg-c-wide', 1200, 800, true );
	add_image_size( 'cfhg-c-portrait', 900, 1125, true );

	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'Ink', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-ink', 'color' => '#13202D' ),
			array( 'name' => __( 'Sea blue', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-sea', 'color' => '#00568F' ),
			array( 'name' => __( 'Sun gold', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-gold', 'color' => '#F0B424' ),
			array( 'name' => __( 'Palm green', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-green', 'color' => '#0B7A3E' ),
			array( 'name' => __( 'Ring red', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-red', 'color' => '#C8201F' ),
			array( 'name' => __( 'Shallows', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-tint', 'color' => '#DCEFF1' ),
			array( 'name' => __( 'Paper', 'caribbean-family-history-c' ), 'slug' => 'cfhg-c-paper', 'color' => '#FBFAF6' ),
		)
	);
}
add_action( 'after_setup_theme', 'cfhg_c_setup' );

/**
 * Readable measure for embeds and images in post content.
 */
function cfhg_c_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'cfhg_c_content_width', 720 );
}
add_action( 'after_setup_theme', 'cfhg_c_content_width', 0 );

/**
 * Shorter excerpts for cards.
 *
 * @return int
 */
function cfhg_c_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'cfhg_c_excerpt_length' );

/**
 * Plain ellipsis instead of [...].
 *
 * @return string
 */
function cfhg_c_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'cfhg_c_excerpt_more' );

/**
 * Body classes the stylesheet relies on.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function cfhg_c_body_class( $classes ) {
	$classes[] = 'cfhg-c';
	if ( is_page_template( 'page-templates/full-width.php' ) || is_page_template( 'page-templates/canvas.php' ) ) {
		$classes[] = 'cfhg-c-is-wide';
	}
	return $classes;
}
add_filter( 'body_class', 'cfhg_c_body_class' );

/**
 * The news archive title without the "Category:" style prefix.
 *
 * @param string $title Title.
 * @return string
 */
function cfhg_c_archive_title( $title ) {
	if ( is_category() || is_tag() ) {
		return single_term_title( '', false );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'cfhg_c_archive_title' );
