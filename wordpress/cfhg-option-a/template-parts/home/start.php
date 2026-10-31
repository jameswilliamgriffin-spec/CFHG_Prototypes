<?php
/**
 * Homepage: start here. Five numbered steps beside a sticky introduction.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-start" id="start-here" aria-labelledby="cfhg-start-title">
	<div class="cfhg-container cfhg-start__grid">
		<div class="cfhg-start__intro">
			<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'start_kicker' ) ); ?></p>
			<h2 class="cfhg-h2" id="cfhg-start-title"><?php echo wp_kses( cfhg_mod( 'start_heading' ), cfhg_inline_kses() ); ?></h2>
			<p class="cfhg-lead"><?php echo esc_html( cfhg_mod( 'start_text' ) ); ?></p>
			<figure class="cfhg-start__media cfhg-rounded">
				<?php echo cfhg_setting_image( 'start_image', array( 'sizes' => '(min-width: 1024px) 34vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</div>
		<div class="cfhg-start__steps">
			<?php echo cfhg_component_steps(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo cfhg_button( cfhg_mod( 'start_link_label' ), cfhg_mod( 'start_link_url' ), 'disc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
