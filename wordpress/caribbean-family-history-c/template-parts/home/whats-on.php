<?php
/**
 * What's on: the programme of coming dates.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_wo = (array) cfhg_c_content( 'home.whats_on', array() );
?>
<section class="cfhg-c-section cfhg-c-whatson" aria-labelledby="cfhg-c-whatson-heading">
	<div class="cfhg-c-container cfhg-c-whatson__grid">
		<header class="cfhg-c-whatson__head" data-cfhg-c-reveal>
			<?php cfhg_c_eyebrow( $cfhg_c_wo['eyebrow'] ); ?>
			<h2 class="cfhg-c-h2" id="cfhg-c-whatson-heading"><?php echo esc_html( $cfhg_c_wo['heading'] ); ?></h2>
			<p><?php echo esc_html( $cfhg_c_wo['lead'] ); ?></p>
			<?php echo cfhg_c_button( $cfhg_c_wo['all']['label'], $cfhg_c_wo['all']['url'], 'outline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</header>
		<?php get_template_part( 'template-parts/components/programme' ); ?>
	</div>
</section>
