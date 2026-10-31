<?php
/**
 * Front page.
 *
 * If the front page has been built with Elementor, Elementor's content is
 * shown. Otherwise the theme prints its homepage sections, which take their
 * words from the Customizer and inc/content.json.
 *
 * @package caribbean-family-history-c
 */

get_header();

$cfhg_c_front_id = (int) get_option( 'page_on_front' );

if ( 'page' === get_option( 'show_on_front' ) && cfhg_c_is_elementor_page( $cfhg_c_front_id ) ) {
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
} else {
	foreach ( cfhg_c_home_sections() as $cfhg_c_section ) {
		get_template_part( 'template-parts/home/' . $cfhg_c_section );
	}
}

get_footer();
