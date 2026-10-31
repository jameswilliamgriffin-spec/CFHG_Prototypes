<?php
/**
 * Homepage: next meeting. The date is worked out automatically.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_mail = cfhg_mailto();
?>
<section class="cfhg-section cfhg-meeting" id="next-meeting" aria-labelledby="cfhg-meeting-title">
	<div class="cfhg-container">
		<div class="cfhg-meeting__panel cfhg-on-dark" data-reveal>
			<div class="cfhg-meeting__when">
				<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'meeting_kicker' ) ); ?></p>
				<?php echo cfhg_component_next_meeting( 'big' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="cfhg-meeting__details">
				<h2 class="cfhg-h2" id="cfhg-meeting-title"><?php echo wp_kses( cfhg_mod( 'meeting_heading' ), cfhg_inline_kses() ); ?></h2>
				<?php echo cfhg_component_next_meeting( 'facts' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo cfhg_component_prices(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="cfhg-actions">
					<?php
					echo cfhg_button( cfhg_mod( 'meeting_cta_label' ), cfhg_mod( 'meeting_cta_url' ), 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( $cfhg_mail ) {
						echo cfhg_button( cfhg_mod( 'meeting_secondary_label' ), $cfhg_mail, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
			<figure class="cfhg-meeting__media cfhg-rounded">
				<?php echo cfhg_setting_image( 'meeting_image', array( 'sizes' => '(min-width: 1024px) 26vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</div>
	</div>
</section>
