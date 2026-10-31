<?php
/**
 * Homepage: partners, as colour-barred rows in one white card.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-partners" aria-labelledby="cfhg-partners-title">
	<div class="cfhg-container">
		<div class="cfhg-partners__head" data-reveal>
			<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'partners_kicker' ) ); ?></p>
			<h2 class="cfhg-h2" id="cfhg-partners-title"><?php echo wp_kses( cfhg_mod( 'partners_heading' ), cfhg_inline_kses() ); ?></h2>
		</div>
		<div class="cfhg-card cfhg-partners__card" data-reveal>
			<?php echo cfhg_component_partners(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
