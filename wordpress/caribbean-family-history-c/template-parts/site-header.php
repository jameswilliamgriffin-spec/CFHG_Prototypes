<?php
/**
 * Two-tier header: white top bar with logo, utility links, Donate, Become a
 * member and the text-size control; sea-blue navigation bar underneath.
 * Below 64em both tiers fold into one menu panel.
 *
 * @package caribbean-family-history-c
 */

?>
<header class="cfhg-c-header" data-cfhg-c-header>
	<div class="cfhg-c-header__top">
		<div class="cfhg-c-container cfhg-c-header__inner">
			<?php cfhg_c_logo(); ?>

			<div class="cfhg-c-header__tools">
				<nav class="cfhg-c-utility" aria-label="<?php esc_attr_e( 'Utility', 'caribbean-family-history-c' ); ?>">
					<?php cfhg_c_menu( 'cfhg_c_utility', 'cfhg-c-utility-menu', 'cfhg-c-utility__list', 1 ); ?>
				</nav>
				<?php cfhg_c_header_buttons(); ?>
				<?php cfhg_c_text_size_control(); ?>
			</div>

			<button class="cfhg-c-menu-toggle" type="button" aria-expanded="false" aria-controls="cfhg-c-panel" data-cfhg-c-menu-toggle>
				<span class="cfhg-c-menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
				<span class="cfhg-c-menu-toggle__label" data-open-label="<?php esc_attr_e( 'Menu', 'caribbean-family-history-c' ); ?>" data-close-label="<?php esc_attr_e( 'Close', 'caribbean-family-history-c' ); ?>"><?php esc_html_e( 'Menu', 'caribbean-family-history-c' ); ?></span>
			</button>
		</div>
	</div>

	<nav class="cfhg-c-nav" aria-label="<?php esc_attr_e( 'Main', 'caribbean-family-history-c' ); ?>">
		<div class="cfhg-c-container">
			<?php cfhg_c_menu( 'cfhg_c_primary', 'cfhg-c-primary-menu', 'cfhg-c-nav__list' ); ?>
		</div>
	</nav>

	<div class="cfhg-c-panel" id="cfhg-c-panel" hidden data-cfhg-c-panel>
		<div class="cfhg-c-container">
			<nav aria-label="<?php esc_attr_e( 'Main', 'caribbean-family-history-c' ); ?>">
				<?php cfhg_c_menu( 'cfhg_c_primary', 'cfhg-c-panel-menu', 'cfhg-c-panel__list' ); ?>
			</nav>
			<?php cfhg_c_header_buttons( 'panel' ); ?>
			<div class="cfhg-c-panel__row">
				<nav aria-label="<?php esc_attr_e( 'Utility', 'caribbean-family-history-c' ); ?>">
					<?php cfhg_c_menu( 'cfhg_c_utility', 'cfhg-c-panel-utility', 'cfhg-c-panel__utility', 1 ); ?>
				</nav>
				<?php cfhg_c_text_size_control(); ?>
			</div>
		</div>
	</div>
</header>
