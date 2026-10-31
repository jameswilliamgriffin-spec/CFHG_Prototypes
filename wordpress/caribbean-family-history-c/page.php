<?php
/**
 * Standard inner page: page heading, a readable text column, and the next
 * meeting card alongside on wide screens.
 *
 * @package caribbean-family-history-c
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( cfhg_c_is_elementor_page( get_the_ID() ) ) {
		the_content();
		continue;
	}

	cfhg_c_page_header( get_the_title(), '', has_excerpt() ? get_the_excerpt() : '' );
	?>
	<div class="cfhg-c-section cfhg-c-section--tight">
		<div class="cfhg-c-container cfhg-c-page">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-c-prose' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="cfhg-c-page__figure"><?php the_post_thumbnail( 'cfhg-c-wide', array( 'sizes' => '(min-width: 64em) 45rem, 100vw' ) ); ?></figure>
				<?php endif; ?>
				<?php
				the_content();
				wp_link_pages();
				?>
			</article>
			<aside class="cfhg-c-page__aside">
				<?php get_template_part( 'template-parts/components/meeting-card' ); ?>
			</aside>
		</div>
	</div>
	<?php
endwhile;

get_footer();
