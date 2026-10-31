<?php
/**
 * Post card. Variants: 'lead' (picture, title, excerpt) and 'row' (a ruled
 * line with date and title, small picture when there is one).
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_variant = ( isset( $args['variant'] ) && 'lead' === $args['variant'] ) ? 'lead' : 'row';
$cfhg_c_tag     = ( isset( $args['tag'] ) && 'h2' === $args['tag'] ) ? 'h2' : 'h3';
?>
<article <?php post_class( 'cfhg-c-post-card cfhg-c-post-card--' . $cfhg_c_variant ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="cfhg-c-post-card__media">
			<?php
			the_post_thumbnail(
				'lead' === $cfhg_c_variant ? 'cfhg-c-wide' : 'cfhg-c-card',
				array(
					'alt'     => '',
					'sizes'   => 'lead' === $cfhg_c_variant ? '(min-width: 64em) 40rem, 100vw' : '8rem',
					'loading' => 'lazy',
				)
			);
			?>
		</div>
	<?php endif; ?>
	<div class="cfhg-c-post-card__body">
		<?php cfhg_c_post_meta(); ?>
		<<?php echo esc_html( $cfhg_c_tag ); ?> class="cfhg-c-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_html( $cfhg_c_tag ); ?>>
		<?php if ( 'lead' === $cfhg_c_variant ) : ?>
			<p class="cfhg-c-post-card__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<?php endif; ?>
	</div>
</article>
