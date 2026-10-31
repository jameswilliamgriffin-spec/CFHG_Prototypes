<?php
/**
 * Fallback template: blog index, search results and anything without a more
 * specific template.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/content/listing' );
get_footer();
