<?php
/**
 * Theme footer.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_privacy = get_privacy_policy_url() ? get_privacy_policy_url() : cfhg_url( cfhg_mod( 'privacy_url' ) );
$cfhg_photo   = cfhg_url( cfhg_mod( 'footer_photo_url' ) );
?>
<footer class="cfhg-footer">
	<div class="cfhg-footer__inner">
		<div class="cfhg-footer__brand">
			<a class="cfhg-footer__badge" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( CFHG_URI . '/assets/images/cfhg-logo-badge.png' ); ?>" width="511" height="512" alt="<?php esc_attr_e( 'Caribbean Family History Group', 'cfhg' ); ?>" loading="lazy" decoding="async">
			</a>
			<p class="cfhg-footer__tagline"><?php echo cfhg_text( cfhg_mod( 'footer_tagline' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses in helper. ?></p>
			<?php if ( cfhg_mailto() ) : ?>
				<a class="cfhg-footer__email" href="<?php echo esc_url( cfhg_mailto() ); ?>"><?php echo cfhg_email_display(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- entities only. ?></a>
			<?php endif; ?>
		</div>

		<nav class="cfhg-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'cfhg' ); ?>">
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

		<div class="cfhg-footer__meeting">
			<p class="cfhg-kicker"><?php echo esc_html( cfhg_mod( 'meeting_kicker' ) ); ?></p>
			<?php if ( cfhg_next_meeting() ) : ?>
				<p class="cfhg-footer__date"><?php echo esc_html( cfhg_next_meeting_date( 'l j F' ) ); ?></p>
			<?php endif; ?>
			<p class="cfhg-footer__venue"><?php echo esc_html( cfhg_mod( 'meeting_short_where' ) ); ?></p>
			<?php echo cfhg_button( cfhg_mod( 'join_cta_label' ), cfhg_mod( 'join_cta_url' ), 'disc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
		</div>
	</div>

	<div class="cfhg-footer__base">
		<?php echo cfhg_component_social(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in component. ?>
		<?php if ( cfhg_mod( 'footer_photo_note' ) ) : ?>
			<p class="cfhg-footer__photo">
				<?php echo esc_html( cfhg_mod( 'footer_photo_note' ) ); ?>
				<?php if ( $cfhg_photo ) : ?>
					<a href="<?php echo esc_url( $cfhg_photo ); ?>"><?php esc_html_e( 'Photography and video policy', 'cfhg' ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
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
		<p class="cfhg-footer__copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Caribbean Family History Group', 'cfhg' ); ?></p>
	</div>
</footer>
