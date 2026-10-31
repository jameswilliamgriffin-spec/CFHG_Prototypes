<?php
/**
 * Single island: label bar, name and summary beside the island print, the
 * research notes, and the other islands to finish.
 *
 * @package caribbean-family-history-c
 */

get_header();

while ( have_posts() ) :
	the_post();

	$cfhg_c_summary = get_post_meta( get_the_ID(), 'cfhg_c_summary', true );
	$cfhg_c_label   = get_post_meta( get_the_ID(), 'cfhg_c_label', true );
	$cfhg_c_starter = cfhg_c_starter_island( get_post_field( 'post_name' ) );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-c-island' ); ?>>
		<header class="cfhg-c-island__head">
			<div class="cfhg-c-container cfhg-c-island__grid">
				<div class="cfhg-c-island__intro">
					<a class="cfhg-c-back" href="<?php echo esc_url( get_post_type_archive_link( 'cfhg_c_island' ) ); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'All islands', 'caribbean-family-history-c' ); ?></a>
					<?php if ( $cfhg_c_label ) : ?>
						<p class="cfhg-c-island-card__label"><?php echo esc_html( $cfhg_c_label ); ?></p>
					<?php endif; ?>
					<h1 class="cfhg-c-island__title"><?php the_title(); ?></h1>
					<?php if ( $cfhg_c_summary ) : ?>
						<p class="cfhg-c-island__summary"><?php echo esc_html( $cfhg_c_summary ); ?></p>
					<?php endif; ?>
				</div>
				<figure class="cfhg-c-island__figure">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'cfhg-c-wide', array( 'sizes' => '(min-width: 64em) 40rem, 100vw', 'loading' => 'eager', 'fetchpriority' => 'high' ) );
					} elseif ( $cfhg_c_starter ) {
						echo cfhg_c_theme_image( $cfhg_c_starter['image'], $cfhg_c_starter['image_alt'], array( 'sizes' => '(min-width: 64em) 40rem, 100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</figure>
			</div>
			<?php cfhg_c_ring_rule( 'cfhg-c-ring--thin' ); ?>
		</header>

		<div class="cfhg-c-section cfhg-c-section--tight">
			<div class="cfhg-c-container cfhg-c-page">
				<div class="cfhg-c-prose">
					<?php the_content(); ?>
				</div>
				<aside class="cfhg-c-page__aside">
					<?php get_template_part( 'template-parts/components/meeting-card' ); ?>
				</aside>
			</div>
		</div>
	</article>

	<section class="cfhg-c-section cfhg-c-band" aria-labelledby="cfhg-c-more-islands">
		<div class="cfhg-c-container">
			<h2 class="cfhg-c-h2" id="cfhg-c-more-islands"><?php esc_html_e( 'Other islands', 'caribbean-family-history-c' ); ?></h2>
		</div>
		<?php
		get_template_part(
			'template-parts/components/island-scroller',
			null,
			array(
				'heading_id' => 'cfhg-c-more-islands',
				'exclude'    => get_the_ID(),
			)
		);
		?>
	</section>
	<?php
endwhile;

get_footer();
