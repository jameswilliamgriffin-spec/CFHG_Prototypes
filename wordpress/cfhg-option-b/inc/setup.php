<?php
/**
 * Theme setup: supports, menus, image sizes and body classes.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function cfhg_setup() {
	load_theme_textdomain( 'cfhg', CFHG_DIR . '/languages' );

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
			'height'      => 240,
			'width'       => 800,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	/*
	 * Elementor does not require a theme support flag, but declaring one is
	 * harmless and makes the intent clear. The real integration points are in
	 * inc/elementor.php: Theme Builder header and footer locations, global
	 * colours and fonts, and the page templates in /page-templates.
	 */
	add_theme_support( 'elementor' );

	register_nav_menus(
		array(
			'primary' => __( 'Main menu (side panel)', 'cfhg' ),
			'footer'  => __( 'Footer links', 'cfhg' ),
			'legal'   => __( 'Footer small print', 'cfhg' ),
		)
	);

	add_image_size( 'cfhg-card', 900, 675, true );
	add_image_size( 'cfhg-portrait', 1000, 1250, true );
	add_image_size( 'cfhg-wide', 1600, 900, true );

	add_editor_style( array( 'assets/css/tokens.css', 'assets/css/editor.css' ) );
}
add_action( 'after_setup_theme', 'cfhg_setup' );

/**
 * Content width used by embeds and Elementor's default container width.
 */
function cfhg_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'cfhg_content_width', 1200 );
}
add_action( 'after_setup_theme', 'cfhg_content_width', 0 );

/**
 * Body classes.
 *
 * "cfhg-overlay-header" means the first band on the page already makes room
 * for the fixed header, so the content wrapper does not need top padding.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function cfhg_body_classes( $classes ) {
	$classes[] = 'cfhg';

	$overlay = true;
	if ( is_singular() && ! is_front_page() ) {
		$template = get_page_template_slug();
		if ( 'page-templates/template-full-width.php' === $template || cfhg_is_built_with_elementor( get_the_ID() ) ) {
			$overlay = false;
		}
	}
	if ( $overlay ) {
		$classes[] = 'cfhg-overlay-header';
	}

	if ( is_front_page() ) {
		$classes[] = 'cfhg-home';
	}

	return $classes;
}
add_filter( 'body_class', 'cfhg_body_classes' );

/**
 * Plain "Read more" for excerpts.
 *
 * @return string
 */
function cfhg_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'cfhg_excerpt_more' );

/**
 * Shorter excerpts suit the story cards.
 *
 * @return int
 */
function cfhg_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'cfhg_excerpt_length' );

/**
 * Use icons cut from the CFHG palm badge when no Site Icon is set.
 */
function cfhg_fallback_site_icon() {
	if ( has_site_icon() ) {
		return;
	}
	printf(
		'<link rel="icon" href="%1$s" sizes="32x32" type="image/png">' . "\n" . '<link rel="apple-touch-icon" href="%2$s">' . "\n",
		esc_url( CFHG_URI . '/assets/images/favicon-32.png' ),
		esc_url( CFHG_URI . '/assets/images/apple-touch-icon.png' )
	);
}
add_action( 'wp_head', 'cfhg_fallback_site_icon', 5 );

/**
 * Page titles: "Page | Caribbean Family History Group", and on the
 * homepage the descriptive title set in the Customizer.
 *
 * @return string
 */
function cfhg_title_separator() {
	return '|';
}
add_filter( 'document_title_separator', 'cfhg_title_separator' );

/**
 * Homepage title from the Customizer (Opening panel > Homepage title).
 *
 * @param string $title Title WordPress would use; empty to let it decide.
 * @return string
 */
function cfhg_home_document_title( $title ) {
	if ( is_front_page() && ! is_paged() ) {
		$custom = trim( (string) cfhg_mod( 'home_title' ) );
		if ( '' !== $custom ) {
			return esc_html( $custom );
		}
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'cfhg_home_document_title' );
