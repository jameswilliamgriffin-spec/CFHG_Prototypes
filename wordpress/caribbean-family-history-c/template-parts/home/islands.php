<?php
/**
 * Islands band with the horizontal scroller.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_isl = (array) cfhg_c_content( 'home.islands', array() );
?>
<section class="cfhg-c-section cfhg-c-band" aria-labelledby="cfhg-c-islands-heading">
	<div class="cfhg-c-container">
		<div class="cfhg-c-band__head" data-cfhg-c-reveal>
			<div>
				<?php cfhg_c_eyebrow( $cfhg_c_isl['eyebrow'] ); ?>
				<h2 class="cfhg-c-h2" id="cfhg-c-islands-heading"><?php echo esc_html( $cfhg_c_isl['heading'] ); ?></h2>
			</div>
			<p class="cfhg-c-band__lead"><?php echo esc_html( $cfhg_c_isl['lead'] ); ?></p>
		</div>
	</div>
	<?php
	get_template_part(
		'template-parts/components/island-scroller',
		null,
		array(
			'heading_id' => 'cfhg-c-islands-heading',
			'all'        => $cfhg_c_isl['all'],
		)
	);
	?>
</section>
