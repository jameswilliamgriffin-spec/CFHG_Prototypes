<?php
/**
 * Homepage: research by island. A ruled list beside a photograph.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-islands" aria-labelledby="cfhg-islands-title">
	<div class="cfhg-container">
		<div class="cfhg-split-head" data-reveal>
			<div>
				<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'islands_kicker' ) ); ?></p>
				<h2 class="cfhg-h2" id="cfhg-islands-title"><?php echo wp_kses( cfhg_mod( 'islands_heading' ), cfhg_inline_kses() ); ?></h2>
			</div>
			<p class="cfhg-split-head__text"><?php echo esc_html( cfhg_mod( 'islands_text' ) ); ?></p>
		</div>
		<div class="cfhg-islands__grid">
			<div class="cfhg-islands__list"><?php echo cfhg_component_islands(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<figure class="cfhg-islands__media cfhg-rounded" data-reveal>
				<?php echo cfhg_setting_image( 'islands_image', array( 'sizes' => '(min-width: 1024px) 55vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</div>
	</div>
</section>
