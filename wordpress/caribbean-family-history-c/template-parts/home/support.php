<?php
/**
 * Membership and donations band, edged with a strip of madras cloth.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_sup = (array) cfhg_c_content( 'home.support', array() );
?>
<section class="cfhg-c-support" aria-labelledby="cfhg-c-support-heading">
	<div class="cfhg-c-support__cloth" aria-hidden="true" style="background-image:url('<?php echo esc_url( cfhg_c_image_url( 'generated/texture-madras' ) ); ?>')"></div>
	<div class="cfhg-c-container cfhg-c-support__inner" data-cfhg-c-reveal>
		<div>
			<?php cfhg_c_eyebrow( $cfhg_c_sup['eyebrow'] ); ?>
			<h2 class="cfhg-c-h2" id="cfhg-c-support-heading"><?php echo esc_html( $cfhg_c_sup['heading'] ); ?></h2>
		</div>
		<div class="cfhg-c-support__body">
			<p><?php echo esc_html( $cfhg_c_sup['body'] ); ?></p>
			<div class="cfhg-c-actions">
				<?php
				echo cfhg_c_button( cfhg_c_mod( 'cfhg_c_member_label' ), cfhg_c_mod( 'cfhg_c_member_url' ), 'gold' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo cfhg_c_button( cfhg_c_mod( 'cfhg_c_donate_label' ), cfhg_c_mod( 'cfhg_c_donate_url' ), 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
			<p class="cfhg-c-support__note">
				<?php echo esc_html( $cfhg_c_sup['note'] ); ?>
				<a href="<?php echo esc_url( cfhg_c_url( $cfhg_c_sup['note_link']['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_sup['note_link']['label'] ); ?></a>
			</p>
		</div>
	</div>
</section>
