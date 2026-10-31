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
}
add_action( 'wp_enqueue_scripts', 'cfhg_enqueue_assets' );

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
}
add_action( 'wp_head', 'cfhg_js_class', 0 );

/**
 * Load the theme fonts and tokens inside the block editor too.
 */
function cfhg_block_editor_assets() {
	wp_enqueue_style( 'cfhg-editor-tokens', CFHG_URI . '/assets/css/tokens.css', array(), CFHG_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'cfhg_block_editor_assets' );
