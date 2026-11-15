<?php
/**
 * Homepage: opening panel. The words on the left (the heading, the
 * introduction, small portraits and the buttons), and on the right a
 * photograph in a large sea-blue circle that runs off the edge of the page,
 * with a few smaller circles around it.
 *
 * On load the circle opens like an iris, the small circles pop in and the
 * words rise in turn (cfhg-load-* classes, assets/css/ui-pass.css).
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
			<h1 class="cfhg-hero__title cfhg-load-fade" id="cfhg-hero-title"><?php echo wp_kses( cfhg_mod( 'hero_heading' ), cfhg_inline_kses() ); ?></h1>
			<p class="cfhg-lead cfhg-load-fade" style="--d:1"><?php echo esc_html( cfhg_mod( 'hero_text' ) ); ?></p>
			<?php echo cfhg_component_hero_proof( 'cfhg-load-fade', 2 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="cfhg-actions cfhg-load-fade" style="--d:3">
				<?php echo cfhg_button( cfhg_mod( 'hero_primary_label' ), cfhg_mod( 'hero_primary_url' ), 'mark', 'cfhg-btn--big' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo cfhg_button( cfhg_mod( 'hero_secondary_label' ), cfhg_mod( 'hero_secondary_url' ), 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php echo cfhg_component_next_meeting( 'inline', 'cfhg-load-fade', 3 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="cfhg-hero__art">
			<span class="cfhg-hero__dot cfhg-hero__dot--white cfhg-load-pop" style="--i:0" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--coral cfhg-load-pop" style="--i:1" aria-hidden="true"></span>
			<figure class="cfhg-hero__disc cfhg-load-iris">
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
			<span class="cfhg-hero__dot cfhg-hero__dot--blue cfhg-load-pop" style="--i:2" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--gold cfhg-load-pop" style="--i:3" aria-hidden="true"></span>
		</div>
	</div>
</section>
<?php
/* A slow band of the surnames members are tracing (CFHG Site plugin). */
$cfhg_ribbon = shortcode_exists( 'cfhg_surname_ribbon' ) ? do_shortcode( '[cfhg_surname_ribbon]' ) : '';
if ( $cfhg_ribbon ) :
	?>
	<section class="cfhg-names" aria-label="<?php esc_attr_e( 'Surnames our members are researching', 'cfhg' ); ?>">
		<?php echo $cfhg_ribbon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</section>
<?php endif; ?>
