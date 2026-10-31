<?php
/**
 * Page content for pages not built with Elementor.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_parent = wp_get_post_parent_id( get_the_ID() );
?>
<main id="main" class="cfhg-main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-entry' ); ?>>
		<header class="cfhg-page-hero">
			<div class="cfhg-container">
				<?php if ( $cfhg_parent ) : ?>
					<p class="cfhg-kicker"><a href="<?php echo esc_url( get_permalink( $cfhg_parent ) ); ?>"><?php echo esc_html( get_the_title( $cfhg_parent ) ); ?></a></p>
				<?php endif; ?>
				<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
				<?php if ( has_excerpt() ) : ?>
					<p class="cfhg-lead cfhg-page-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="cfhg-container cfhg-entry__media cfhg-rounded">
				<?php the_post_thumbnail( 'cfhg-wide', array( 'sizes' => '(min-width: 1200px) 1200px, 92vw' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="cfhg-container">
			<div class="cfhg-prose">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="cfhg-page-links" aria-label="' . esc_attr__( 'Page', 'cfhg' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</div>
	</article>
</main>
