<?php
/**
 * Styles, scripts and font preloading.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets. No build step: these are plain CSS and JS files.
 */
function cfhg_enqueue_assets() {
	$css = CFHG_URI . '/assets/css/';

	wp_enqueue_style( 'cfhg-tokens', $css . 'tokens.css', array(), CFHG_VERSION );
	wp_enqueue_style( 'cfhg-main', $css . 'main.css', array( 'cfhg-tokens' ), CFHG_VERSION );
	wp_enqueue_style( 'cfhg-ui-pass', $css . 'ui-pass.css', array( 'cfhg-main' ), CFHG_VERSION );
	wp_enqueue_style( 'cfhg-polish', $css . 'polish.css', array( 'cfhg-ui-pass' ), CFHG_VERSION );

	wp_enqueue_script(
		'cfhg-main',
		CFHG_URI . '/assets/js/main.js',
		array(),
		CFHG_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_enqueue_script(
		'cfhg-ui-pass',
		CFHG_URI . '/assets/js/ui-pass.js',
		array( 'cfhg-main' ),
		CFHG_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_enqueue_script(
		'cfhg-polish',
		CFHG_URI . '/assets/js/polish.js',
		array(),
		CFHG_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_enqueue_script(
		'cfhg-lightbox',
		CFHG_URI . '/assets/js/lightbox.js',
		array(),
		CFHG_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script( 'cfhg-polish', 'window.cfhgLoops = ' . wp_json_encode( cfhg_loop_films() ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'cfhg_enqueue_assets' );

/**
 * Theme pictures that have a short looping film of the same scene, by the
 * picture's file name (assets/js/polish.js lays the film over the picture).
 *
 * @return array<string, string>
 */
function cfhg_loop_films() {
	$video = CFHG_URI . '/assets/video/';
	return apply_filters(
		'cfhg_loop_films',
		array(
			'three-generations-with-an-album-on-the-sofa' => $video . 'cfhg-hero-loop.mp4',
			'island-jamaica-blue-mountains'               => $video . 'island-jamaica-loop.mp4',
			'island-barbados-chattel-houses'              => $video . 'island-barbados-loop.mp4',
			'island-st-kitts-nevis-narrows'               => $video . 'island-st-kitts-nevis-loop.mp4',
			'island-trinidad-caroni'                      => $video . 'island-trinidad-loop.mp4',
		)
	);
}

/**
 * Preload the two variable fonts so headings do not flash.
 */
function cfhg_preload_fonts() {
	$fonts = array( 'jost-var.woff2', 'outfit-var.woff2' );
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( CFHG_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'cfhg_preload_fonts', 1 );

/**
 * Mark the document as JavaScript-capable before paint, so reveal animations
 * never hide content when scripts fail to load.
 */
function cfhg_js_class() {
	echo "<script>document.documentElement.classList.add('cfhg-js');</script>\n";
	/*
	 * Opt in to the load animations in assets/css/ui-pass.css. If ui-pass.js
	 * has not run within three seconds the class comes off again, so a
	 * blocked or failed script can never leave anything hidden.
	 */
	echo "<script>(function(r){r.classList.add('cfhg-ui');setTimeout(function(){if(!window.cfhgUi){r.classList.remove('cfhg-ui');}},3000);})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'cfhg_js_class', 0 );

/**
 * Browser chrome colour: the page's cream.
 */
function cfhg_theme_color() {
	echo '<meta name="theme-color" content="#F6F1EA">' . "\n";
}
add_action( 'wp_head', 'cfhg_theme_color', 1 );

/**
 * Load the theme fonts and tokens inside the block editor too.
 */
function cfhg_block_editor_assets() {
	wp_enqueue_style( 'cfhg-editor-tokens', CFHG_URI . '/assets/css/tokens.css', array(), CFHG_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'cfhg_block_editor_assets' );

/**
 * A description for search engines and link previews, unless an SEO plugin
 * is doing it: the page's excerpt, or the opening words of the homepage.
 */
function cfhg_meta_description() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}
	$text = '';
	if ( is_front_page() ) {
		$text = cfhg_mod( 'hero_text' );
	} elseif ( is_singular() && has_excerpt() ) {
		$text = get_the_excerpt();
	}
	$text = trim( wp_strip_all_tags( (string) $text ) );
	if ( '' !== $text ) {
		echo '<meta name="description" content="' . esc_attr( wp_html_excerpt( $text, 200, '…' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'cfhg_meta_description', 2 );
