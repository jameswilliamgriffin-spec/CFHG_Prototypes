<?php
/**
 * Homepage: research help. A card that overlaps the photograph.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-help" aria-labelledby="cfhg-help-title">
	<div class="cfhg-container cfhg-help__grid">
		<figure class="cfhg-help__media cfhg-rounded" data-reveal>
			<?php echo cfhg_setting_image( 'help_image', array( 'sizes' => '(min-width: 1024px) 60vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
		<div class="cfhg-help__card" data-reveal>
			<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'help_kicker' ) ); ?></p>
			<h2 class="cfhg-h2" id="cfhg-help-title"><?php echo wp_kses( cfhg_mod( 'help_heading' ), cfhg_inline_kses() ); ?></h2>
			<p><?php echo esc_html( cfhg_mod( 'help_text' ) ); ?></p>
			<?php echo cfhg_button( cfhg_mod( 'help_cta_label' ), cfhg_mod( 'help_cta_url' ), 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
