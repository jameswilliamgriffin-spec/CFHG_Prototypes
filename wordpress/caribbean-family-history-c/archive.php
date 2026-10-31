<?php
/**
 * News archive: categories, tags, dates and authors.
 *
 * @package caribbean-family-history-c
 */

get_header();

cfhg_c_page_header( get_the_archive_title(), __( 'Stories and news', 'caribbean-family-history-c' ), wp_strip_all_tags( get_the_archive_description() ) );

get_template_part( 'template-parts/content/post-list' );

get_footer();
