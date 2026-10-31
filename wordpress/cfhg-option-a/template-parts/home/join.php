<?php
/**
 * Homepage: membership. A gold panel that bookends the opening panel.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_mail = cfhg_mailto();
?>
<section class="cfhg-section cfhg-join" id="join" aria-labelledby="cfhg-join-title">
	<div class="cfhg-container">
		<div class="cfhg-join__panel" data-reveal>
			<div class="cfhg-join__copy">
				<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'join_kicker' ) ); ?></p>
				<h2 class="cfhg-h1" id="cfhg-join-title"><?php echo wp_kses( cfhg_mod( 'join_heading' ), cfhg_inline_kses() ); ?></h2>
				<p class="cfhg-lead"><?php echo esc_html( cfhg_mod( 'join_text' ) ); ?></p>
				<div class="cfhg-actions">
					<?php
					echo cfhg_button( cfhg_mod( 'join_cta_label' ), cfhg_mod( 'join_cta_url' ), 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( $cfhg_mail ) {
						echo cfhg_button( cfhg_mod( 'join_secondary_label' ), $cfhg_mail, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
			<figure class="cfhg-join__media cfhg-rounded">
				<?php echo cfhg_setting_image( 'join_image', array( 'sizes' => '(min-width: 1024px) 45vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</div>
	</div>
</section>
