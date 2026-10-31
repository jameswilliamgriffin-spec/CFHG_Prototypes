<?php
/**
 * CFHG Option A theme functions.
 *
 * Everything is split into small files in /inc so each concern is easy to find.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

define( 'CFHG_VERSION', '1.0.0' );
define( 'CFHG_DIR', get_template_directory() );
define( 'CFHG_URI', get_template_directory_uri() );

require_once CFHG_DIR . '/inc/defaults.php';
require_once CFHG_DIR . '/inc/setup.php';
require_once CFHG_DIR . '/inc/enqueue.php';
require_once CFHG_DIR . '/inc/images.php';
require_once CFHG_DIR . '/inc/template-tags.php';
require_once CFHG_DIR . '/inc/class-cfhg-menu-walker.php';
require_once CFHG_DIR . '/inc/components.php';
require_once CFHG_DIR . '/inc/shortcodes.php';
require_once CFHG_DIR . '/inc/customizer.php';
require_once CFHG_DIR . '/inc/elementor.php';
require_once CFHG_DIR . '/inc/elementor-template.php';

if ( is_admin() ) {
	require_once CFHG_DIR . '/inc/admin.php';
}
