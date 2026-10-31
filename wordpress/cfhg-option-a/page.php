<?php
/**
 * Single page.
 *
 * Pages built with Elementor render edge to edge. Other pages get the theme's
 * title band and readable text column.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( cfhg_is_built_with_elementor( get_the_ID() ) ) :
		?>
		<main id="main" class="cfhg-main cfhg-main--builder">
			<?php the_content(); ?>
		</main>
		<?php
	else :
		get_template_part( 'template-parts/content/page' );
	endif;
endwhile;

get_footer();
