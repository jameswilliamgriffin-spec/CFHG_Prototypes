<?php
/**
 * Template Name: CFHG Five steps guide
 * Template Post Type: page
 *
 * "Starting your family tree": the five steps in full, the explainer video,
 * and how to complete pedigree charts and family group records. The words
 * come from inc/guide-content.json (see inc/guide.php). To edit the words on
 * the site, build the page in Elementor instead: Appearance > CFHG Setup.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
echo cfhg_guide_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in inc/guide.php.
get_footer();
