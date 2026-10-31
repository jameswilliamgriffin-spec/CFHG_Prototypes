<?php
/**
 * Latest stories and news: one lead story with its picture, then a ruled
 * list of the next three.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_news  = (array) cfhg_c_content( 'home.news', array() );
$cfhg_c_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => false,
		'no_found_rows'       => true,
	)
);

if ( ! $cfhg_c_query->have_posts() ) {
	return;
}
?>
<section class="cfhg-c-section cfhg-c-news" aria-labelledby="cfhg-c-news-heading">
	<div class="cfhg-c-container">
		<header class="cfhg-c-section__head cfhg-c-section__head--split" data-cfhg-c-reveal>
			<div>
				<?php cfhg_c_eyebrow( $cfhg_c_news['eyebrow'] ); ?>
				<h2 class="cfhg-c-h2" id="cfhg-c-news-heading"><?php echo esc_html( $cfhg_c_news['heading'] ); ?></h2>
			</div>
			<a class="cfhg-c-link-arrow" href="<?php echo esc_url( cfhg_c_news_url() ); ?>"><?php echo esc_html( $cfhg_c_news['all']['label'] ); ?><span aria-hidden="true"> &rarr;</span></a>
		</header>

		<div class="cfhg-c-news__grid">
			<?php
			$cfhg_c_first = true;
			while ( $cfhg_c_query->have_posts() ) :
				$cfhg_c_query->the_post();
				get_template_part( 'template-parts/components/post-card', null, array( 'variant' => $cfhg_c_first ? 'lead' : 'row' ) );
				$cfhg_c_first = false;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
