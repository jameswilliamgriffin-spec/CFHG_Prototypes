<?php
/**
 * Homepage: membership. A photograph band with large words across it, and a
 * white card overlapping its lower edge.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_mail = cfhg_mailto();
?>
<section class="cfhg-section cfhg-join" id="join" aria-labelledby="cfhg-join-title">
	<div class="cfhg-join__band">
		<figure class="cfhg-join__image">
			<?php echo cfhg_setting_image( 'join_image', array( 'sizes' => '100vw', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
		<?php if ( cfhg_mod( 'join_big' ) ) : ?>
			<p class="cfhg-join__big" aria-hidden="true"><?php echo esc_html( cfhg_mod( 'join_big' ) ); ?></p>
		<?php endif; ?>
	</div>
	<div class="cfhg-container">
		<div class="cfhg-card cfhg-join__card" data-reveal>
			<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'join_kicker' ) ); ?></p>
			<h2 class="cfhg-h2" id="cfhg-join-title"><?php echo wp_kses( cfhg_mod( 'join_heading' ), cfhg_inline_kses() ); ?></h2>
			<p class="cfhg-lead"><?php echo esc_html( cfhg_mod( 'join_text' ) ); ?></p>
			<div class="cfhg-actions">
				<?php
				echo cfhg_button( cfhg_mod( 'join_cta_label' ), cfhg_mod( 'join_cta_url' ), 'mark', 'cfhg-btn--big' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( $cfhg_mail ) {
					echo cfhg_button( cfhg_mod( 'join_secondary_label' ), $cfhg_mail, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
	</div>
</section>
