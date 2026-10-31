<?php
/**
 * Styles, scripts, font preload and the early text-size script.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end assets. No libraries and no build step.
 */
function cfhg_c_enqueue_assets() {
	wp_enqueue_style( 'cfhg-c-main', CFHG_C_URI . '/assets/css/main.css', array(), CFHG_C_VERSION );

	wp_enqueue_script(
		'cfhg-c-main',
		CFHG_C_URI . '/assets/js/main.js',
		array(),
		CFHG_C_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'cfhg_c_enqueue_assets' );

/**
 * Preload the heading font, and apply the saved text size before first
 * paint so the page does not jump.
 */
function cfhg_c_head_early() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( CFHG_C_URI . '/assets/fonts/newsreader-latin-var.woff2' )
	);

	wp_print_inline_script_tag(
		"(function(d){d.documentElement.classList.add('cfhg-c-js');try{var s=localStorage.getItem('cfhg-c-text-size');if(s&&/^\\d{2,3}(\\.\\d)?$/.test(s)){d.documentElement.style.fontSize=s+'%';}}catch(e){}})(document);"
	);
}
add_action( 'wp_head', 'cfhg_c_head_early', 1 );
