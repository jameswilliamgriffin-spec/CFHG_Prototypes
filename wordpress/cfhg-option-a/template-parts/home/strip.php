<?php
/**
 * Homepage: a staggered, sideways-scrolling strip of meeting photographs.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-strip-wrap" aria-label="<?php echo esc_attr( cfhg_mod( 'strip_label' ) ); ?>">
	<div class="cfhg-strip" tabindex="0">
		<?php for ( $cfhg_i = 1; $cfhg_i <= 5; $cfhg_i++ ) : ?>
			<figure class="cfhg-strip__item cfhg-strip__item--<?php echo (int) $cfhg_i; ?>">
				<?php
				echo cfhg_setting_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'strip_image_' . $cfhg_i,
					array( 'sizes' => '(min-width: 1024px) 30vw, 72vw' )
				);
				?>
			</figure>
		<?php endfor; ?>
	</div>
</section>
