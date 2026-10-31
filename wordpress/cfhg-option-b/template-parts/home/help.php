<?php
/**
 * Homepage: research help. A sand card holding the text and a photograph.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-help" aria-labelledby="cfhg-help-title">
	<div class="cfhg-container">
		<div class="cfhg-help__card" data-reveal>
			<?php echo cfhg_shape( 'hibiscus', 'cfhg-help__flower' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="cfhg-help__copy">
				<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'help_kicker' ) ); ?></p>
				<h2 class="cfhg-h2" id="cfhg-help-title"><?php echo wp_kses( cfhg_mod( 'help_heading' ), cfhg_inline_kses() ); ?></h2>
				<p><?php echo esc_html( cfhg_mod( 'help_text' ) ); ?></p>
				<?php echo cfhg_button( cfhg_mod( 'help_cta_label' ), cfhg_mod( 'help_cta_url' ), 'green' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<figure class="cfhg-help__media">
				<?php echo cfhg_setting_image( 'help_image', array( 'sizes' => '(min-width: 1024px) 44vw, 88vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</div>
	</div>
</section>
