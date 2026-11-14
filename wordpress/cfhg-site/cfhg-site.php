<?php
/**
 * Plugin Name: CFHG Site
 * Description: The Caribbean Family History Group's own content: Events (meetings, sub-group meetings, talks and exhibitions) with an "Add a series" tool, and an Island tag shared by events and posts. Works with any theme; the CFHG themes style it.
 * Version: 1.0.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: Creative Alliance
 * License: GPL-2.0-or-later
 * Text Domain: cfhg-site
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

define( 'CFHG_SITE_VERSION', '1.0.0' );
define( 'CFHG_SITE_DIR', __DIR__ );
define( 'CFHG_SITE_URL', plugin_dir_url( __FILE__ ) );

require_once CFHG_SITE_DIR . '/inc/post-types.php';
require_once CFHG_SITE_DIR . '/inc/events.php';
require_once CFHG_SITE_DIR . '/inc/frontend.php';
require_once CFHG_SITE_DIR . '/inc/calendar.php';

if ( is_admin() ) {
	require_once CFHG_SITE_DIR . '/inc/admin-event.php';
	require_once CFHG_SITE_DIR . '/inc/admin-series.php';
}

/**
 * On activation: register the content types, add the starting groups,
 * types and islands, and refresh the site's addresses.
 */
function cfhg_site_activate() {
	cfhg_site_register_content();
	cfhg_site_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cfhg_site_activate' );

/**
 * On deactivation: refresh the site's addresses. Events are kept.
 */
function cfhg_site_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cfhg_site_deactivate' );
