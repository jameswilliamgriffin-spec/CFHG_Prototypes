<?php
/**
 * Three ways in: meetings, research, sharing a record or story. Each has its
 * own layout: a wide photograph with key facts, a ruled index of guides, and
 * an ink panel.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_ways = (array) cfhg_c_content( 'home.ways', array() );
$cfhg_c_meet = $cfhg_c_ways['meet'];
$cfhg_c_res  = $cfhg_c_ways['research'];
$cfhg_c_shr  = $cfhg_c_ways['share'];
?>
<section class="cfhg-c-section cfhg-c-ways" aria-labelledby="cfhg-c-ways-heading">
	<div class="cfhg-c-container">
		<header class="cfhg-c-section__head" data-cfhg-c-reveal>
			<?php cfhg_c_eyebrow( $cfhg_c_ways['eyebrow'] ); ?>
			<h2 class="cfhg-c-h2" id="cfhg-c-ways-heading"><?php echo esc_html( $cfhg_c_ways['heading'] ); ?></h2>
		</header>

		<article class="cfhg-c-way cfhg-c-way--meet" data-cfhg-c-reveal>
			<figure class="cfhg-c-way__media">
				<?php echo cfhg_c_theme_image( $cfhg_c_meet['image'], $cfhg_c_meet['image_alt'], array( 'sizes' => '(min-width: 64em) 40rem, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">01</p>
				<?php cfhg_c_eyebrow( $cfhg_c_meet['label'] ); ?>
				<h3 class="cfhg-c-h3"><?php echo esc_html( $cfhg_c_meet['heading'] ); ?></h3>
				<p><?php echo esc_html( $cfhg_c_meet['body'] ); ?></p>
				<dl class="cfhg-c-facts">
					<?php foreach ( $cfhg_c_meet['facts'] as $cfhg_c_fact ) : ?>
						<div><dt><?php echo esc_html( $cfhg_c_fact['term'] ); ?></dt><dd><?php echo esc_html( $cfhg_c_fact['detail'] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
				<?php echo cfhg_c_button( $cfhg_c_meet['link']['label'], $cfhg_c_meet['link']['url'], 'solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</article>

		<article class="cfhg-c-way cfhg-c-way--research" data-cfhg-c-reveal>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">02</p>
				<?php cfhg_c_eyebrow( $cfhg_c_res['label'] ); ?>
				<h3 class="cfhg-c-h3"><?php echo esc_html( $cfhg_c_res['heading'] ); ?></h3>
				<p><?php echo esc_html( $cfhg_c_res['body'] ); ?></p>
				<ul class="cfhg-c-index">
					<?php foreach ( $cfhg_c_res['links'] as $cfhg_c_link ) : ?>
						<li><a href="<?php echo esc_url( cfhg_c_url( $cfhg_c_link['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_link['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<figure class="cfhg-c-way__media">
				<?php echo cfhg_c_theme_image( $cfhg_c_res['image'], $cfhg_c_res['image_alt'], array( 'sizes' => '(min-width: 64em) 30rem, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
		</article>

		<article class="cfhg-c-way cfhg-c-way--share" data-cfhg-c-reveal>
			<figure class="cfhg-c-way__media">
				<?php echo cfhg_c_theme_image( $cfhg_c_shr['image'], $cfhg_c_shr['image_alt'], array( 'sizes' => '(min-width: 64em) 16rem, 60vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</figure>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">03</p>
				<?php cfhg_c_eyebrow( $cfhg_c_shr['label'] ); ?>
				<h3 class="cfhg-c-h3"><?php echo esc_html( $cfhg_c_shr['heading'] ); ?></h3>
				<p><?php echo esc_html( $cfhg_c_shr['body'] ); ?></p>
				<?php echo cfhg_c_button( $cfhg_c_shr['link']['label'], $cfhg_c_shr['link']['url'], 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</article>
	</div>
</section>
