<?php
/**
 * Single post content.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/* The topic: the first category other than Stories, which every story is in. */
$cfhg_topic = null;
foreach ( (array) get_the_category() as $cfhg_cat ) {
	if ( sanitize_title( cfhg_mod( 'stories_category' ) ) !== $cfhg_cat->slug ) {
		$cfhg_topic = $cfhg_cat;
		break;
	}
}
?>
<main id="main" class="cfhg-main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-entry' ); ?>>
		<header class="cfhg-page-hero">
			<div class="cfhg-container">
				<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
				<p class="cfhg-page-hero__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><?php if ( $cfhg_topic ) : ?><span class="ui-meta-sep" aria-hidden="true"> · </span><a href="<?php echo esc_url( get_category_link( $cfhg_topic ) ); ?>"><?php echo esc_html( $cfhg_topic->name ); ?></a><?php endif; ?></p>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="cfhg-container cfhg-entry__media cfhg-rounded">
				<?php
				the_post_thumbnail(
					'cfhg-wide',
					array(
						'sizes'         => '(min-width: 1200px) 1200px, 92vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
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
				echo cfhg_button( get_the_title( $cfhg_prev ), get_permalink( $cfhg_prev ), 'plain', 'cfhg-post-nav__prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $cfhg_next ) {
				echo cfhg_button( get_the_title( $cfhg_next ), get_permalink( $cfhg_next ), 'plain', 'cfhg-post-nav__next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</nav>
	</article>
</main>
