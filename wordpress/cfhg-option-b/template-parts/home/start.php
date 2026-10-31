<?php
/**
 * Homepage: start here. Five coloured step tiles.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-start" id="start-here" aria-labelledby="cfhg-start-title">
	<div class="cfhg-container">
		<div class="cfhg-split-head" data-reveal>
			<div>
				<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'start_kicker' ) ); ?></p>
				<h2 class="cfhg-h2" id="cfhg-start-title"><?php echo wp_kses( cfhg_mod( 'start_heading' ), cfhg_inline_kses() ); ?></h2>
			</div>
			<p class="cfhg-lead cfhg-split-head__text"><?php echo esc_html( cfhg_mod( 'start_text' ) ); ?></p>
		</div>
		<?php echo cfhg_component_steps(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="cfhg-actions cfhg-start__actions">
			<?php echo cfhg_button( cfhg_mod( 'start_link_label' ), cfhg_mod( 'start_link_url' ), 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
