<?php
/**
 * Horizontal island scroller: scroll-snap track, previous and next buttons,
 * keyboard reachable. Pulls from the Islands post type.
 *
 * Args: heading_id (labels the region), exclude (post id), all (link array).
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_args    = wp_parse_args(
	isset( $args ) ? (array) $args : array(),
	array(
		'heading_id' => '',
		'exclude'    => 0,
		'all'        => cfhg_c_content( 'home.islands.all', array() ),
	)
);
$cfhg_c_islands = cfhg_c_get_islands( (int) $cfhg_c_args['exclude'] );

if ( ! $cfhg_c_islands ) {
	return;
}

$cfhg_c_uid = wp_unique_id( 'cfhg-c-scroller-' );
?>
<div class="cfhg-c-scroller" data-cfhg-c-scroller>
	<div class="cfhg-c-container cfhg-c-scroller__bar">
		<div class="cfhg-c-scroller__controls">
			<button type="button" class="cfhg-c-scroller__btn" data-cfhg-c-prev aria-controls="<?php echo esc_attr( $cfhg_c_uid ); ?>">
				<span aria-hidden="true">&larr;</span><span class="screen-reader-text"><?php esc_html_e( 'Previous islands', 'caribbean-family-history-c' ); ?></span>
			</button>
			<button type="button" class="cfhg-c-scroller__btn" data-cfhg-c-next aria-controls="<?php echo esc_attr( $cfhg_c_uid ); ?>">
				<span aria-hidden="true">&rarr;</span><span class="screen-reader-text"><?php esc_html_e( 'Next islands', 'caribbean-family-history-c' ); ?></span>
			</button>
		</div>
		<?php if ( ! empty( $cfhg_c_args['all'] ) ) : ?>
			<a class="cfhg-c-link-arrow" href="<?php echo esc_url( cfhg_c_url( $cfhg_c_args['all']['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_args['all']['label'] ); ?><span aria-hidden="true"> &rarr;</span></a>
		<?php endif; ?>
	</div>

	<div class="cfhg-c-scroller__track" id="<?php echo esc_attr( $cfhg_c_uid ); ?>" role="region" tabindex="0"
		<?php if ( $cfhg_c_args['heading_id'] ) : ?>
			aria-labelledby="<?php echo esc_attr( $cfhg_c_args['heading_id'] ); ?>"
		<?php else : ?>
			aria-label="<?php esc_attr_e( 'Islands', 'caribbean-family-history-c' ); ?>"
		<?php endif; ?>
		data-cfhg-c-track>
		<ul class="cfhg-c-scroller__list">
			<?php foreach ( $cfhg_c_islands as $cfhg_c_island ) : ?>
				<li class="cfhg-c-scroller__item">
					<?php get_template_part( 'template-parts/components/island-card', null, $cfhg_c_island ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
