<?php
/**
 * Homepage: next meeting. A white card with the date set large, beside a
 * photograph with a label. The date is worked out automatically.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_mail = cfhg_mailto();
?>
<section class="cfhg-section cfhg-meeting" id="next-meeting" aria-labelledby="cfhg-meeting-title">
	<div class="cfhg-container cfhg-meeting__grid">
		<div class="cfhg-card cfhg-meeting__card" data-reveal>
			<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'meeting_kicker' ) ); ?></p>
			<?php echo cfhg_component_next_meeting( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h2 class="cfhg-h3 cfhg-meeting__heading" id="cfhg-meeting-title"><span class="cfhg-highlight"><?php echo wp_kses( cfhg_mod( 'meeting_heading' ), cfhg_inline_kses() ); ?></span></h2>
			<?php echo cfhg_component_next_meeting( 'facts' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo cfhg_component_prices(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="cfhg-actions">
				<?php
				echo cfhg_button( cfhg_mod( 'meeting_cta_label' ), cfhg_mod( 'meeting_cta_url' ), 'green' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( $cfhg_mail ) {
					echo cfhg_button( cfhg_mod( 'meeting_secondary_label' ), $cfhg_mail, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
		<figure class="cfhg-meeting__media" data-reveal>
			<?php echo cfhg_setting_image( 'meeting_image', array( 'sizes' => '(min-width: 1024px) 52vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( cfhg_mod( 'meeting_image_label' ) ) : ?>
				<figcaption class="cfhg-chip"><?php echo esc_html( cfhg_mod( 'meeting_image_label' ) ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>
