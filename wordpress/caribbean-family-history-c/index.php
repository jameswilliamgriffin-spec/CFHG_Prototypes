<?php
/**
 * Fallback template: blog index and search results.
 *
 * @package caribbean-family-history-c
 */

get_header();

if ( is_search() ) {
	/* translators: %s: search terms */
	cfhg_c_page_header( sprintf( __( 'Results for "%s"', 'caribbean-family-history-c' ), get_search_query() ), __( 'Search', 'caribbean-family-history-c' ) );
} else {
	$cfhg_c_posts_page = (int) get_option( 'page_for_posts' );
	cfhg_c_page_header(
		$cfhg_c_posts_page ? get_the_title( $cfhg_c_posts_page ) : __( 'Stories and news', 'caribbean-family-history-c' ),
		__( 'From the group', 'caribbean-family-history-c' )
	);
}

get_template_part( 'template-parts/content/post-list' );

get_footer();
