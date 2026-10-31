<?php
/**
 * Homepage hero: the "museum label". Headline and introduction on paper,
 * an arched print of a Caribbean hillside, and an index card with the next
 * meeting pinned over its corner. The logo ring runs along the base.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_hero = (array) cfhg_c_content( 'home.hero', array() );
?>
<section class="cfhg-c-hero" aria-labelledby="cfhg-c-hero-title">
	<div class="cfhg-c-container cfhg-c-hero__grid">
		<div class="cfhg-c-hero__text">
			<?php cfhg_c_eyebrow( $cfhg_c_hero['eyebrow'] ); ?>
			<h1 class="cfhg-c-hero__title" id="cfhg-c-hero-title"><?php echo esc_html( cfhg_c_mod( 'cfhg_c_hero_title' ) ); ?></h1>
			<p class="cfhg-c-hero__lead"><?php echo esc_html( cfhg_c_mod( 'cfhg_c_hero_lead' ) ); ?></p>
			<div class="cfhg-c-actions">
				<?php
				echo cfhg_c_button( $cfhg_c_hero['primary']['label'], $cfhg_c_hero['primary']['url'], 'gold' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo cfhg_c_button( $cfhg_c_hero['secondary']['label'], $cfhg_c_hero['secondary']['url'], 'outline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>

		<div class="cfhg-c-hero__media">
			<figure class="cfhg-c-hero__frame">
				<?php
				echo cfhg_c_theme_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$cfhg_c_hero['image'],
					$cfhg_c_hero['image_alt'],
					array(
						'sizes'         => '(min-width: 64em) 30rem, (min-width: 40em) 60vw, 90vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</figure>
			<?php get_template_part( 'template-parts/components/meeting-card' ); ?>
		</div>
	</div>
	<?php cfhg_c_ring_rule( 'cfhg-c-hero__ring' ); ?>
</section>
