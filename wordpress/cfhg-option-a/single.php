<?php
/**
 * Single post, used for stories and news.
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
		get_template_part( 'template-parts/content/single' );
	endif;
endwhile;

get_footer();
