<?php
/**
 * Index card with the next meeting.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_next = cfhg_c_next_meeting();
$cfhg_c_link = (array) cfhg_c_content( 'home.hero.card_link', array() );
$cfhg_c_hid  = wp_unique_id( 'cfhg-c-next-' );
?>
<aside class="cfhg-c-card-index" aria-labelledby="<?php echo esc_attr( $cfhg_c_hid ); ?>">
	<h2 class="cfhg-c-card-index__heading" id="<?php echo esc_attr( $cfhg_c_hid ); ?>">
		<?php echo esc_html( cfhg_c_content( 'home.hero.card_heading', __( 'Next meeting', 'caribbean-family-history-c' ) ) ); ?>
		<?php if ( $cfhg_c_next['today'] ) : ?>
			<span class="cfhg-c-card-index__today"><?php esc_html_e( 'Today', 'caribbean-family-history-c' ); ?></span>
		<?php endif; ?>
	</h2>
	<p class="cfhg-c-card-index__date"><time datetime="<?php echo esc_attr( $cfhg_c_next['iso'] ); ?>"><?php echo esc_html( $cfhg_c_next['date'] ); ?></time></p>
	<dl class="cfhg-c-card-index__list">
		<?php if ( $cfhg_c_next['time'] ) : ?>
			<div><dt><?php esc_html_e( 'Time', 'caribbean-family-history-c' ); ?></dt><dd><?php echo esc_html( $cfhg_c_next['time'] ); ?></dd></div>
		<?php endif; ?>
		<div><dt><?php esc_html_e( 'Where', 'caribbean-family-history-c' ); ?></dt><dd><?php echo esc_html( $cfhg_c_next['where'] ); ?></dd></div>
		<div><dt><?php esc_html_e( 'Cost', 'caribbean-family-history-c' ); ?></dt><dd><?php esc_html_e( 'First visit free', 'caribbean-family-history-c' ); ?></dd></div>
	</dl>
	<?php if ( $cfhg_c_link ) : ?>
		<a class="cfhg-c-link-arrow" href="<?php echo esc_url( cfhg_c_url( $cfhg_c_link['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_link['label'] ); ?><span aria-hidden="true"> &rarr;</span></a>
	<?php endif; ?>
</aside>
