<?php
/**
 * Caribbean Family History Group, Option C.
 *
 * Every function, hook, handle and class in this theme uses the cfhg_c_ /
 * cfhg-c- prefix so it can be installed alongside Options A and B.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CFHG_C_VERSION', '1.0.0' );
define( 'CFHG_C_DIR', get_template_directory() );
define( 'CFHG_C_URI', get_template_directory_uri() );

$cfhg_c_includes = array(
	'content',
	'setup',
	'enqueue',
	'template-tags',
	'meetings',
	'islands',
	'customizer',
	'sections',
	'elementor',
	'admin',
);

foreach ( $cfhg_c_includes as $cfhg_c_file ) {
	require_once CFHG_C_DIR . '/inc/' . $cfhg_c_file . '.php';
}

unset( $cfhg_c_includes, $cfhg_c_file );
