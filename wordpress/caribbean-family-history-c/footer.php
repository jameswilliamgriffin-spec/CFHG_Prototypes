<?php
/**
 * Site footer. An Elementor Theme Builder footer replaces
 * template-parts/site-footer.php when one is assigned.
 *
 * @package caribbean-family-history-c
 */

?>
</main>
<?php
if ( ! cfhg_c_elementor_location( 'footer' ) ) {
	get_template_part( 'template-parts/site-footer' );
}
wp_footer();
?>
</body>
</html>
