<?php
/**
 * Homepage: who we are. A white card between two tilted snapshot photographs.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-intro" id="cfhg-intro" aria-labelledby="cfhg-intro-title">
	<div class="cfhg-intro__stage">
		<div class="cfhg-intro__snap cfhg-intro__snap--left" data-reveal="tilt">
			<?php echo cfhg_component_snapshot( 'intro_photo_1', 'coral', 'portrait' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cfhg-card cfhg-intro__card" data-reveal>
			<h2 class="cfhg-card__title" id="cfhg-intro-title"><?php echo esc_html( cfhg_mod( 'intro_title' ) ); ?></h2>
			<?php echo cfhg_paragraphs( cfhg_mod( 'intro_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo cfhg_button( cfhg_mod( 'intro_link_label' ), cfhg_mod( 'intro_link_url' ), 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cfhg-intro__snap cfhg-intro__snap--right" data-reveal="tilt">
			<?php echo cfhg_component_snapshot( 'intro_photo_2', 'green', 'landscape' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
