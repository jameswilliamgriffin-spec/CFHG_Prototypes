<?php
/**
 * Theme header: logo pill, Join button, menu toggle and the
 * side-panel menu.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_join_label = cfhg_mod( 'header_join_label' );
$cfhg_join_url   = cfhg_url( cfhg_mod( 'header_join_url' ) );
?>
<header class="cfhg-header" data-cfhg-header>
	<div class="cfhg-header__bar">
		<a class="cfhg-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo cfhg_logo_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
		</a>
		<div class="cfhg-header__actions">
			<?php if ( $cfhg_join_label && $cfhg_join_url ) : ?>
				<a class="cfhg-header__join" href="<?php echo esc_url( $cfhg_join_url ); ?>"><?php echo esc_html( $cfhg_join_label ); ?></a>
			<?php endif; ?>
			<button type="button" class="cfhg-menu-toggle" aria-expanded="false" aria-controls="cfhg-menu" data-cfhg-menu-toggle>
				<span class="cfhg-menu-toggle__open"><?php esc_html_e( 'Menu', 'cfhg' ); ?></span>
				<span class="cfhg-menu-toggle__close"><?php esc_html_e( 'Close', 'cfhg' ); ?></span>
				<span class="cfhg-menu-toggle__sign" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	<div class="cfhg-menu" id="cfhg-menu" data-cfhg-menu>
		<p class="cfhg-menu__name"><?php esc_html_e( 'Caribbean Family History Group', 'cfhg' ); ?></p>
		<nav class="cfhg-menu__nav" aria-label="<?php esc_attr_e( 'Main', 'cfhg' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'cfhg-menu__list',
					'depth'          => 2,
					'walker'         => new CFHG_Menu_Walker(),
					'fallback_cb'    => 'cfhg_primary_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="cfhg-menu__foot">
			<?php echo cfhg_component_next_meeting( 'inline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( cfhg_mailto() ) : ?>
				<p class="cfhg-menu__email"><a href="<?php echo esc_url( cfhg_mailto() ); ?>"><?php echo cfhg_email_display(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- entities only. ?></a></p>
			<?php endif; ?>
			<?php echo cfhg_component_social(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</header>
