<?php
/**
 * Template Name: Full width (header and footer)
 * Template Post Type: page, post, cfhg_c_island
 *
 * Theme header and footer with the content running edge to edge. Use this
 * for pages laid out in Elementor that should keep the site header.
 *
 * @package caribbean-family-history-c
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
