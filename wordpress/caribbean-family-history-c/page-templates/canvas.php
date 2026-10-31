<?php
/**
 * Template Name: Canvas (no header or footer)
 * Template Post Type: page, post, cfhg_c_island
 *
 * A blank page with only the content, for landing pages and Elementor
 * layouts that bring their own header.
 *
 * @package caribbean-family-history-c
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'cfhg-c-canvas' ); ?>>
<?php wp_body_open(); ?>
<main id="cfhg-c-main" class="cfhg-c-main">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php wp_footer(); ?>
</body>
</html>
