<?php
/**
 * Template Name: CFHG Full width
 * Template Post Type: page, post
 *
 * The site header and footer, with the content running edge to edge and no
 * theme containers. Best for pages built with Elementor. Content starts below
 * the header.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="cfhg-main cfhg-main--builder">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</main>
<?php
get_footer();
