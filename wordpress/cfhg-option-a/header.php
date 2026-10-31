<?php
/**
 * Document head and site header.
 *
 * If Elementor Pro's Theme Builder has a header template, that is used instead
 * of the theme header.
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
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="cfhg-skip" href="#content"><?php esc_html_e( 'Skip to content', 'cfhg' ); ?></a>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	get_template_part( 'template-parts/site/header' );
}
?>
<div id="content" class="cfhg-content" tabindex="-1">
