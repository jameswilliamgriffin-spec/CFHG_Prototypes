<?php
/**
 * Homepage: research by island. A colour-barred list beside an oval window.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-islands" aria-labelledby="cfhg-islands-title">
	<div class="cfhg-container cfhg-islands__grid">
		<div class="cfhg-islands__copy">
			<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'islands_kicker' ) ); ?></p>
			<h2 class="cfhg-h2" id="cfhg-islands-title"><?php echo wp_kses( cfhg_mod( 'islands_heading' ), cfhg_inline_kses() ); ?></h2>
			<p class="cfhg-islands__text"><?php echo esc_html( cfhg_mod( 'islands_text' ) ); ?></p>
			<?php echo cfhg_component_islands(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cfhg-islands__window" data-reveal>
			<figure class="cfhg-oval">
				<?php echo cfhg_setting_image( 'islands_image', array( 'sizes' => '(min-width: 1024px) 34vw, 80vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
			<?php echo cfhg_shape( 'palm-frond', 'cfhg-islands__frond' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
