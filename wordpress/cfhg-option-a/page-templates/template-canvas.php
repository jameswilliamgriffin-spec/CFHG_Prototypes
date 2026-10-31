<?php
/**
 * Template Name: CFHG Canvas (no header or footer)
 * Template Post Type: page, post
 *
 * A blank canvas: no theme header, footer or containers. For landing pages
 * built entirely in Elementor.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'cfhg-canvas' ); ?>>
<?php wp_body_open(); ?>
<main id="main" class="cfhg-main cfhg-main--builder">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</main>
<?php wp_footer(); ?>
</body>
</html>
