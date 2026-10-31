<?php
/**
 * Document head and site header.
 *
 * An Elementor Theme Builder header replaces template-parts/site-header.php
 * when one is assigned.
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
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="cfhg-c-skip" href="#cfhg-c-main"><?php esc_html_e( 'Skip to content', 'caribbean-family-history-c' ); ?></a>
<?php
if ( ! cfhg_c_elementor_location( 'header' ) ) {
	get_template_part( 'template-parts/site-header' );
}
?>
<main id="cfhg-c-main" class="cfhg-c-main" tabindex="-1">
