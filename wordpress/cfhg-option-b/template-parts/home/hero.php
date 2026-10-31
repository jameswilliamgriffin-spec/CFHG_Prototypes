<?php
/**
 * Homepage: opening panel. The words on the left (a pill label, the heading,
 * the introduction, small portraits and the buttons), and on the right a
 * photograph in a large sea-blue circle that runs off the edge of the page,
 * with a few smaller circles around it.
 *
 * Elementor: see docs/ELEMENTOR.md, section 1.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-hero" id="top" aria-labelledby="cfhg-hero-title">
	<div class="cfhg-hero__inner">
		<div class="cfhg-hero__copy">
			<?php if ( cfhg_mod( 'hero_kicker' ) ) : ?>
				<p class="cfhg-hero__kicker"><?php echo esc_html( cfhg_mod( 'hero_kicker' ) ); ?></p>
			<?php endif; ?>
			<h1 class="cfhg-hero__title" id="cfhg-hero-title"><?php echo wp_kses( cfhg_mod( 'hero_heading' ), cfhg_inline_kses() ); ?></h1>
			<p class="cfhg-lead"><?php echo esc_html( cfhg_mod( 'hero_text' ) ); ?></p>
			<?php echo cfhg_component_hero_proof(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="cfhg-actions">
				<?php echo cfhg_button( cfhg_mod( 'hero_primary_label' ), cfhg_mod( 'hero_primary_url' ), 'mark', 'cfhg-btn--big' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo cfhg_button( cfhg_mod( 'hero_secondary_label' ), cfhg_mod( 'hero_secondary_url' ), 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php echo cfhg_component_next_meeting( 'inline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cfhg-hero__art">
			<span class="cfhg-hero__dot cfhg-hero__dot--white" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--coral" aria-hidden="true"></span>
			<figure class="cfhg-hero__disc">
				<?php
				echo cfhg_setting_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'hero_image',
					array(
						'sizes'   => '(min-width: 900px) 52vw, 92vw',
						'loading' => 'eager',
					)
				);
				?>
			</figure>
			<span class="cfhg-hero__dot cfhg-hero__dot--blue" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--gold" aria-hidden="true"></span>
		</div>
	</div>
</section>
