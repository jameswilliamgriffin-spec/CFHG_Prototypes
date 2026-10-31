<?php
/**
 * Footer: contact details, newsletter sign-up, links.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_email     = cfhg_c_mod( 'cfhg_c_email' );
$cfhg_c_address   = (array) cfhg_c_content( 'site.address', array() );
$cfhg_c_instagram = cfhg_c_mod( 'cfhg_c_instagram_url' );
$cfhg_c_x         = cfhg_c_mod( 'cfhg_c_x_url' );
?>
<footer class="cfhg-c-footer">
	<?php cfhg_c_ring_rule(); ?>
	<div class="cfhg-c-container">
		<div class="cfhg-c-footer__grid">
			<div class="cfhg-c-footer__about">
				<?php cfhg_c_logo( 'light' ); ?>
				<p><?php echo esc_html( cfhg_c_content( 'footer.about', '' ) ); ?></p>
			</div>

			<div class="cfhg-c-footer__col">
				<h2 class="cfhg-c-footer__heading"><?php esc_html_e( 'Meetings', 'caribbean-family-history-c' ); ?></h2>
				<address>
					<?php echo esc_html( cfhg_c_content( 'site.venue_room', '' ) ); ?><br>
					<?php echo implode( '<br>', array_map( 'esc_html', $cfhg_c_address ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</address>
				<p><?php echo esc_html( cfhg_c_content( 'site.meeting_rule', '' ) ); ?>. <?php esc_html_e( 'Also on Zoom.', 'caribbean-family-history-c' ); ?></p>
			</div>

			<div class="cfhg-c-footer__col" id="contact">
				<h2 class="cfhg-c-footer__heading"><?php esc_html_e( 'Contact', 'caribbean-family-history-c' ); ?></h2>
				<?php if ( $cfhg_c_email ) : ?>
					<p><a href="mailto:<?php echo esc_attr( $cfhg_c_email ); ?>"><?php echo esc_html( antispambot( $cfhg_c_email ) ); ?></a></p>
				<?php endif; ?>
				<?php if ( $cfhg_c_instagram || $cfhg_c_x ) : ?>
					<ul class="cfhg-c-footer__links">
						<?php if ( $cfhg_c_instagram ) : ?>
							<li><a href="<?php echo esc_url( $cfhg_c_instagram ); ?>"><?php esc_html_e( 'Instagram', 'caribbean-family-history-c' ); ?></a></li>
						<?php endif; ?>
						<?php if ( $cfhg_c_x ) : ?>
							<li><a href="<?php echo esc_url( $cfhg_c_x ); ?>"><?php esc_html_e( 'X (Twitter)', 'caribbean-family-history-c' ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
				<nav aria-label="<?php esc_attr_e( 'Footer', 'caribbean-family-history-c' ); ?>">
					<?php cfhg_c_menu( 'cfhg_c_primary', 'cfhg-c-footer-menu', 'cfhg-c-footer__links', 1 ); ?>
				</nav>
			</div>

			<div class="cfhg-c-footer__col cfhg-c-footer__news" id="newsletter">
				<h2 class="cfhg-c-footer__heading"><?php echo esc_html( cfhg_c_content( 'footer.newsletter_heading', '' ) ); ?></h2>
				<p><?php echo esc_html( cfhg_c_content( 'footer.newsletter_body', '' ) ); ?></p>
				<?php cfhg_c_newsletter_form( 'cfhg-c-footer-news' ); ?>
			</div>
		</div>

		<div class="cfhg-c-footer__base">
			<p><?php echo esc_html( cfhg_c_content( 'footer.partners', '' ) ); ?></p>
			<ul class="cfhg-c-footer__legal">
				<li>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( cfhg_c_content( 'site.name', get_bloginfo( 'name' ) ) ); ?></li>
				<?php foreach ( (array) cfhg_c_content( 'footer.legal', array() ) as $cfhg_c_link ) : ?>
					<li><a href="<?php echo esc_url( cfhg_c_url( $cfhg_c_link['url'] ) ); ?>"><?php echo esc_html( $cfhg_c_link['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>
