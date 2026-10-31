<?php
/**
 * Homepage: opening panel.
 *
 * Elementor: a full-width container with the CFHG Gold background and a 40px
 * bottom radius, holding two columns. See docs/ELEMENTOR.md, section 1.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-hero" id="top" aria-labelledby="cfhg-hero-title">
	<div class="cfhg-hero__inner">
		<div class="cfhg-hero__copy">
			<p class="cfhg-kicker cfhg-kicker--plain cfhg-hero__kicker"><?php echo cfhg_text( cfhg_mod( 'hero_kicker' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses in helper. ?></p>
			<h1 class="cfhg-display cfhg-hero__title" id="cfhg-hero-title"><?php echo wp_kses( cfhg_mod( 'hero_heading' ), cfhg_inline_kses() ); ?></h1>

			<div class="cfhg-hero__foot">
				<a class="cfhg-hero__cue" href="#cfhg-intro">
					<span class="cfhg-hero__cue-label"><?php echo esc_html( cfhg_mod( 'hero_cue_label' ) ); ?></span>
					<span class="cfhg-hero__cue-line" aria-hidden="true"></span>
				</a>
				<div class="cfhg-hero__body">
					<p class="cfhg-lead"><?php echo esc_html( cfhg_mod( 'hero_text' ) ); ?></p>
					<div class="cfhg-actions">
						<?php
						echo cfhg_button( cfhg_mod( 'hero_primary_label' ), cfhg_mod( 'hero_primary_url' ), 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo cfhg_button( cfhg_mod( 'hero_secondary_label' ), cfhg_mod( 'hero_secondary_url' ), 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
					<?php echo cfhg_component_next_meeting( 'inline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>

		<figure class="cfhg-hero__media cfhg-arch">
			<?php
			echo cfhg_setting_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'hero_image',
				array(
					'sizes'   => '(min-width: 1024px) 40vw, 92vw',
					'loading' => 'eager',
				)
			);
			?>
		</figure>
	</div>
</section>
