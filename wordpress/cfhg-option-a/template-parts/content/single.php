<?php
/**
 * Single post content.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_cats = get_the_category();
?>
<main id="main" class="cfhg-main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-entry' ); ?>>
		<header class="cfhg-page-hero">
			<div class="cfhg-container">
				<?php if ( $cfhg_cats ) : ?>
					<ul class="cfhg-story__tags" aria-label="<?php esc_attr_e( 'Topics', 'cfhg' ); ?>">
						<?php foreach ( array_slice( $cfhg_cats, 0, 3 ) as $cfhg_i => $cfhg_cat ) : ?>
							<li class="cfhg-tag<?php echo 0 === $cfhg_i ? ' cfhg-tag--first' : ''; ?>"><a href="<?php echo esc_url( get_category_link( $cfhg_cat ) ); ?>"><?php echo esc_html( $cfhg_cat->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
				<p class="cfhg-page-hero__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="cfhg-container cfhg-entry__media cfhg-rounded">
				<?php the_post_thumbnail( 'cfhg-wide', array( 'sizes' => '(min-width: 1200px) 1200px, 92vw' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="cfhg-container">
			<div class="cfhg-prose">
				<?php the_content(); ?>
			</div>
		</div>

		<nav class="cfhg-container cfhg-post-nav" aria-label="<?php esc_attr_e( 'More stories', 'cfhg' ); ?>">
			<?php
			$cfhg_prev = get_previous_post();
			$cfhg_next = get_next_post();
			if ( $cfhg_prev ) {
				echo cfhg_button( get_the_title( $cfhg_prev ), get_permalink( $cfhg_prev ), 'disc', 'cfhg-post-nav__prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $cfhg_next ) {
				echo cfhg_button( get_the_title( $cfhg_next ), get_permalink( $cfhg_next ), 'disc', 'cfhg-post-nav__next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</nav>
	</article>
</main>
