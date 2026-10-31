<?php
/**
 * Site footer and closing tags.
 *
 * If Elementor Pro's Theme Builder has a footer template, that is used instead.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- #content -->
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/site/footer' );
}
wp_footer();
?>
</body>
</html>
