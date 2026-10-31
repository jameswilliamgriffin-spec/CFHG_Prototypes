<?php
/**
 * Page not found.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="cfhg-main">
	<section class="cfhg-hero cfhg-hero--compact cfhg-notfound" aria-labelledby="cfhg-404-title">
		<div class="cfhg-hero__inner">
			<div class="cfhg-hero__copy">
				<p class="cfhg-kicker cfhg-kicker--plain"><?php esc_html_e( 'Page not found', 'cfhg' ); ?></p>
				<h1 class="cfhg-display cfhg-hero__title" id="cfhg-404-title"><?php esc_html_e( 'This branch of the tree doesn’t lead anywhere', 'cfhg' ); ?></h1>
				<div class="cfhg-notfound__body">
					<p class="cfhg-lead"><?php esc_html_e( 'The page may have moved, or the link may be out of date. Try searching, or head back to the homepage.', 'cfhg' ); ?></p>
					<?php get_search_form(); ?>
					<div class="cfhg-actions">
						<?php echo cfhg_button( __( 'Back to the homepage', 'cfhg' ), home_url( '/' ), 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
