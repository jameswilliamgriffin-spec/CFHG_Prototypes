<?php
/**
 * One island card. Args come from cfhg_c_get_islands().
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_card = isset( $args ) ? (array) $args : array();
if ( empty( $cfhg_c_card['name'] ) ) {
	return;
}
?>
<article class="cfhg-c-island-card">
	<?php if ( ! empty( $cfhg_c_card['label'] ) ) : ?>
		<p class="cfhg-c-island-card__label"><?php echo esc_html( $cfhg_c_card['label'] ); ?></p>
	<?php endif; ?>
	<div class="cfhg-c-island-card__media">
		<?php echo $cfhg_c_card['image']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by core or cfhg_c_theme_image(). ?>
	</div>
	<h3 class="cfhg-c-island-card__name">
		<a href="<?php echo esc_url( $cfhg_c_card['url'] ); ?>"><?php echo esc_html( $cfhg_c_card['name'] ); ?></a>
	</h3>
	<p class="cfhg-c-island-card__summary"><?php echo esc_html( $cfhg_c_card['summary'] ); ?></p>
	<p class="cfhg-c-island-card__more" aria-hidden="true">
		<?php
		/* translators: %s: island name */
		echo esc_html( sprintf( __( 'Researching %s', 'caribbean-family-history-c' ), $cfhg_c_card['name'] ) );
		?>
		&rarr;
	</p>
</article>
