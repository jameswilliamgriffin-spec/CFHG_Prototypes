<?php
/**
 * Theme footer: a photograph band with large words across it, and a white
 * card overlapping its lower edge.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_privacy = get_privacy_policy_url() ? get_privacy_policy_url() : cfhg_url( cfhg_mod( 'privacy_url' ) );
$cfhg_photo   = cfhg_url( cfhg_mod( 'footer_photo_url' ) );
?>
<footer class="cfhg-footer">
	<div class="cfhg-footer__band">
		<figure class="cfhg-footer__image">
			<?php echo cfhg_setting_image( 'footer_image', array( 'sizes' => '100vw', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
		<?php if ( cfhg_mod( 'footer_big' ) ) : ?>
			<p class="cfhg-footer__big" aria-hidden="true"><?php echo esc_html( cfhg_mod( 'footer_big' ) ); ?></p>
		<?php endif; ?>
	</div>

	<div class="cfhg-footer__card">
		<div class="cfhg-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo cfhg_logo_markup( 'cfhg-footer__logo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<p class="cfhg-footer__tagline"><?php echo cfhg_text( cfhg_mod( 'footer_tagline' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</div>

		<nav class="cfhg-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'cfhg' ); ?>">
			<p class="cfhg-footer__heading"><?php esc_html_e( 'Explore', 'cfhg' ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'cfhg-footer__links',
					'depth'          => 1,
					'fallback_cb'    => 'cfhg_footer_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="cfhg-footer__col">
			<p class="cfhg-footer__heading"><?php echo esc_html( cfhg_mod( 'meeting_kicker' ) ); ?></p>
			<?php if ( cfhg_next_meeting() ) : ?>
				<p class="cfhg-footer__date"><?php echo esc_html( cfhg_next_meeting_date( 'l j F' ) ); ?></p>
			<?php endif; ?>
			<p class="cfhg-footer__venue"><?php echo esc_html( cfhg_mod( 'meeting_short_where' ) ); ?></p>
			<?php echo cfhg_button( cfhg_mod( 'join_cta_label' ), cfhg_mod( 'join_cta_url' ), 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<div class="cfhg-footer__col">
			<p class="cfhg-footer__heading"><?php esc_html_e( 'Contact', 'cfhg' ); ?></p>
			<?php if ( cfhg_mailto() ) : ?>
				<p><a class="cfhg-footer__email" href="<?php echo esc_url( cfhg_mailto() ); ?>"><?php echo cfhg_email_display(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- entities only. ?></a></p>
			<?php endif; ?>
			<?php echo cfhg_component_social(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'cfhg-footer__legal',
						'depth'          => 1,
					)
				);
			} elseif ( $cfhg_privacy ) {
				printf(
					'<ul class="cfhg-footer__legal"><li><a href="%1$s">%2$s</a></li></ul>',
					esc_url( $cfhg_privacy ),
					esc_html__( 'Privacy and cookies', 'cfhg' )
				);
			}
			?>
		</div>

		<div class="cfhg-footer__base">
			<?php if ( cfhg_mod( 'footer_photo_note' ) ) : ?>
				<p class="cfhg-footer__photo">
					<?php echo esc_html( cfhg_mod( 'footer_photo_note' ) ); ?>
					<?php if ( $cfhg_photo ) : ?>
						<a href="<?php echo esc_url( $cfhg_photo ); ?>"><?php esc_html_e( 'Photography and video policy', 'cfhg' ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<p class="cfhg-footer__copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Caribbean Family History Group', 'cfhg' ); ?></p>
		</div>
	</div>
</footer>
