<?php
/**
 * Page not found.
 *
 * @package caribbean-family-history-c
 */

get_header();

$cfhg_c_nf = (array) cfhg_c_content( 'not_found', array() );
?>
<section class="cfhg-c-section cfhg-c-notfound" aria-labelledby="cfhg-c-notfound-title">
	<div class="cfhg-c-container cfhg-c-notfound__grid">
		<div>
			<?php cfhg_c_eyebrow( $cfhg_c_nf['eyebrow'] ); ?>
			<h1 class="cfhg-c-notfound__title" id="cfhg-c-notfound-title"><?php echo esc_html( $cfhg_c_nf['title'] ); ?></h1>
			<p class="cfhg-c-lead"><?php echo esc_html( $cfhg_c_nf['body'] ); ?></p>
			<?php get_search_form(); ?>
			<ul class="cfhg-c-index">
				<?php foreach ( $cfhg_c_nf['links'] as $cfhg_c_link ) : ?>
					<li><a href="<?php echo esc_url( cfhg_c_url( $cfhg_c_link['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_link['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<figure class="cfhg-c-notfound__figure">
			<?php echo cfhg_c_theme_image( $cfhg_c_nf['image'], $cfhg_c_nf['image_alt'], array( 'sizes' => '(min-width: 64em) 28rem, 90vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
	</div>
</section>
<?php
get_footer();
