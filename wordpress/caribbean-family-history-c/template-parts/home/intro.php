<?php
/**
 * Centred introduction.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_intro = (array) cfhg_c_content( 'home.intro', array() );
?>
<section class="cfhg-c-section cfhg-c-intro" aria-labelledby="cfhg-c-intro-heading">
	<div class="cfhg-c-container cfhg-c-intro__inner" data-cfhg-c-reveal>
		<h2 class="cfhg-c-eyebrow" id="cfhg-c-intro-heading"><?php echo esc_html( $cfhg_c_intro['eyebrow'] ); ?></h2>
		<p class="cfhg-c-intro__statement"><?php echo esc_html( cfhg_c_mod( 'cfhg_c_intro_statement' ) ); ?></p>
		<p class="cfhg-c-intro__body"><?php echo esc_html( $cfhg_c_intro['body'] ); ?></p>
		<?php echo cfhg_c_button( $cfhg_c_intro['link']['label'], $cfhg_c_intro['link']['url'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
