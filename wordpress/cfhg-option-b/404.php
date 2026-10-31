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
	<section class="cfhg-notfound" aria-labelledby="cfhg-404-title">
		<?php echo cfhg_shape( 'hibiscus', 'cfhg-notfound__flower' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo cfhg_shape( 'palm-frond', 'cfhg-notfound__frond' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="cfhg-container">
			<div class="cfhg-card cfhg-notfound__card">
				<p class="cfhg-label"><?php esc_html_e( 'Page not found', 'cfhg' ); ?></p>
				<h1 class="cfhg-h1" id="cfhg-404-title"><?php esc_html_e( 'This branch of the tree doesn’t lead anywhere', 'cfhg' ); ?></h1>
				<p class="cfhg-lead"><?php esc_html_e( 'The page may have moved, or the link may be out of date. Try searching, or head back to the homepage.', 'cfhg' ); ?></p>
				<?php get_search_form(); ?>
				<div class="cfhg-actions">
					<?php echo cfhg_button( __( 'Back to the homepage', 'cfhg' ), home_url( '/' ), 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
