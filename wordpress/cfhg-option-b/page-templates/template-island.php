<?php
/**
 * Template Name: CFHG Island
 * Template Post Type: page
 *
 * An island research page from the CFHG Site plugin's island guide: the
 * record finder, the parish explorer, where to look, surnames, stories, and
 * the island's events and posts. See inc/island.php.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
$cfhg_island = cfhg_island_for_page( get_queried_object_id() );
if ( $cfhg_island ) {
	echo cfhg_island_markup( $cfhg_island ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in inc/island.php.
} else {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/page' );
	}
}
get_footer();
