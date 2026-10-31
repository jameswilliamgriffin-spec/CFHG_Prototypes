<?php
/**
 * Homepage.
 *
 * If the page set as the homepage is built with Elementor, Elementor's content
 * is shown. Otherwise the theme's own homepage sections are shown, edited in
 * Appearance > Customize > CFHG homepage.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();

$cfhg_front_id = (int) get_option( 'page_on_front' );
$cfhg_use_elementor = 'page' === get_option( 'show_on_front' ) && $cfhg_front_id && cfhg_is_built_with_elementor( $cfhg_front_id );
?>
<main id="main" class="cfhg-main cfhg-main--home">
	<?php
	if ( apply_filters( 'cfhg_use_elementor_homepage', $cfhg_use_elementor ) ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	} else {
		foreach ( cfhg_home_sections() as $cfhg_section ) {
			get_template_part( 'template-parts/home/' . $cfhg_section );
		}
	}
	?>
</main>
<?php
get_footer();
