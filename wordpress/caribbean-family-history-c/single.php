<?php
/**
 * Single post.
 *
 * @package caribbean-family-history-c
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-c-article' ); ?>>
		<header class="cfhg-c-article__head">
			<div class="cfhg-c-container cfhg-c-article__narrow">
				<a class="cfhg-c-back" href="<?php echo esc_url( cfhg_c_news_url() ); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Stories and news', 'caribbean-family-history-c' ); ?></a>
				<?php cfhg_c_post_meta(); ?>
				<h1 class="cfhg-c-article__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="cfhg-c-article__standfirst"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="cfhg-c-container cfhg-c-article__figure">
				<?php the_post_thumbnail( 'cfhg-c-wide', array( 'sizes' => '(min-width: 64em) 60rem, 100vw', 'loading' => 'eager' ) ); ?>
				<?php if ( get_the_post_thumbnail_caption() ) : ?>
					<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="cfhg-c-container cfhg-c-article__narrow cfhg-c-prose">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>

		<footer class="cfhg-c-container cfhg-c-article__narrow cfhg-c-article__foot">
			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="cfhg-c-eyebrow">' . esc_html__( 'Previous', 'caribbean-family-history-c' ) . '</span> %title',
					'next_text' => '<span class="cfhg-c-eyebrow">' . esc_html__( 'Next', 'caribbean-family-history-c' ) . '</span> %title',
				)
			);
			?>
		</footer>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="cfhg-c-container cfhg-c-article__narrow">';
		comments_template();
		echo '</div>';
	}
endwhile;

get_footer();
