<?php
/**
 * Appearance > CFHG Setup.
 *
 * One screen that shows what is set up and offers one-click actions:
 * apply the design tokens to Elementor, create the Elementor homepage,
 * download the homepage template, and create a starter menu.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the screen.
 */
function cfhg_admin_menu() {
	add_theme_page(
		__( 'CFHG Setup', 'cfhg' ),
		__( 'CFHG Setup', 'cfhg' ),
		'edit_theme_options',
		'cfhg-setup',
		'cfhg_admin_page'
	);
}
add_action( 'admin_menu', 'cfhg_admin_menu' );

/**
 * One status row.
 *
 * @param bool   $ok    Done.
 * @param string $label Label.
 * @param string $hint  Hint when not done.
 */
function cfhg_admin_status( $ok, $label, $hint = '' ) {
	printf(
		'<li><strong>%1$s</strong> %2$s%3$s</li>',
		$ok ? '&#10003;' : '&#9675;',
		esc_html( $label ),
		( ! $ok && $hint ) ? ' <span class="description">' . esc_html( $hint ) . '</span>' : ''
	);
}

/**
 * A button that posts to admin-post.php.
 *
 * @param string $action Action name.
 * @param string $label  Button label.
 * @param string $extra  Extra form fields (already escaped).
 * @param bool   $primary Primary styling.
 */
function cfhg_admin_action_form( $action, $label, $extra = '', $primary = false ) {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin:0 0 1em">';
	echo '<input type="hidden" name="action" value="' . esc_attr( $action ) . '">';
	wp_nonce_field( $action );
	echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped strings.
	submit_button( $label, $primary ? 'primary' : 'secondary', 'submit', false );
	echo '</form>';
}

/**
 * Render the screen.
 */
function cfhg_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$elementor = cfhg_elementor_is_active();
	$locations = get_nav_menu_locations();
	$front_id  = (int) get_option( 'page_on_front' );
	$notice    = isset( $_GET['cfhg'] ) ? sanitize_key( wp_unslash( $_GET['cfhg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$messages = array(
		'tokens'   => __( 'The CFHG colours and fonts are now in Elementor’s Site Settings.', 'cfhg' ),
		'home'     => __( 'The Elementor homepage has been created.', 'cfhg' ),
		'menu'     => __( 'A starter menu has been created and assigned. Edit it in Appearance > Menus.', 'cfhg' ),
		'error'    => __( 'Something went wrong. Please check Elementor is active and try again.', 'cfhg' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'CFHG Setup', 'cfhg' ); ?></h1>
		<?php if ( isset( $messages[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo 'error' === $notice ? 'error' : 'success'; ?> is-dismissible"><p><?php echo esc_html( $messages[ $notice ] ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Where things stand', 'cfhg' ); ?></h2>
		<ul>
			<?php
			cfhg_admin_status( $elementor, __( 'Elementor is active', 'cfhg' ), __( 'Install Elementor from Plugins > Add New. The theme also works without it.', 'cfhg' ) );
			cfhg_admin_status( $elementor && cfhg_elementor_kit_is_synced(), __( 'Elementor has the CFHG colours and fonts', 'cfhg' ), __( 'Use the first button below.', 'cfhg' ) );
			cfhg_admin_status( ! empty( $locations['primary'] ), __( 'A menu is assigned to the main menu', 'cfhg' ), __( 'Until then, a default menu is shown.', 'cfhg' ) );
			cfhg_admin_status( 'page' === get_option( 'show_on_front' ) && $front_id, __( 'A page is set as the homepage', 'cfhg' ), __( 'Until then, the theme shows its own homepage, edited in Appearance > Customize.', 'cfhg' ) );
			cfhg_admin_status( $front_id && cfhg_is_built_with_elementor( $front_id ), __( 'The homepage is built with Elementor', 'cfhg' ) );
			?>
		</ul>

		<?php if ( $elementor ) : ?>
			<h2><?php esc_html_e( '1. Design tokens', 'cfhg' ); ?></h2>
			<p><?php esc_html_e( 'Adds the CFHG colours and fonts to Elementor > Site Settings > Global Colours and Global Fonts. The theme reads them back, so changing a global colour in Elementor changes the whole site. Other colours you have added are kept.', 'cfhg' ); ?></p>
			<?php cfhg_admin_action_form( 'cfhg_apply_tokens', __( 'Apply CFHG colours and fonts to Elementor', 'cfhg' ), '', true ); ?>

			<h2><?php esc_html_e( '2. Homepage in Elementor', 'cfhg' ); ?></h2>
			<p><?php esc_html_e( 'Creates a page called Home, built from Elementor containers and widgets with the same layout and copy as the theme homepage. The photographs are copied into the Media Library with their alt text.', 'cfhg' ); ?></p>
			<?php
			cfhg_admin_action_form(
				'cfhg_create_home',
				__( 'Create the Elementor homepage', 'cfhg' ),
				'<p><label><input type="checkbox" name="set_front" value="1"> ' . esc_html__( 'Publish it and make it the homepage now', 'cfhg' ) . '</label></p>'
			);
			?>
		<?php endif; ?>

		<h2><?php esc_html_e( '3. Homepage template file', 'cfhg' ); ?></h2>
		<p><?php esc_html_e( 'Downloads the homepage as an Elementor template, for Elementor > Templates > Saved Templates > Import. Useful for moving the design to another site.', 'cfhg' ); ?></p>
		<?php cfhg_admin_action_form( 'cfhg_download_template', __( 'Download homepage template (.json)', 'cfhg' ) ); ?>

		<h2><?php esc_html_e( '4. Starter menu', 'cfhg' ); ?></h2>
		<p><?php esc_html_e( 'Creates a menu with Research, What’s On, Stories, Who We Are, Join the group and Contact, and assigns it to the main menu and footer. Links point to existing pages where they match.', 'cfhg' ); ?></p>
		<?php cfhg_admin_action_form( 'cfhg_create_menu', __( 'Create starter menu', 'cfhg' ) ); ?>

		<h2><?php esc_html_e( 'Where to edit things', 'cfhg' ); ?></h2>
		<ul>
			<li><?php esc_html_e( 'Homepage copy, photos and meeting details: Appearance > Customize > CFHG homepage, and Meetings and prices.', 'cfhg' ); ?></li>
			<li><?php esc_html_e( 'Menus: Appearance > Menus.', 'cfhg' ); ?></li>
			<li><?php esc_html_e( 'Logo: Appearance > Customize > Site Identity. If none is set, the CFHG logo in the theme is used.', 'cfhg' ); ?></li>
			<li><?php esc_html_e( 'Full guide: docs/README.md and docs/ELEMENTOR.md in the theme folder.', 'cfhg' ); ?></li>
		</ul>
	</div>
	<?php
}

/**
 * Shared checks for every action.
 *
 * @param string $action Action name.
 */
function cfhg_admin_guard( $action ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'cfhg' ) );
	}
	check_admin_referer( $action );
}

/**
 * Redirect back to the setup screen with a message.
 *
 * @param string $code Message code.
 */
function cfhg_admin_back( $code ) {
	wp_safe_redirect( add_query_arg( 'cfhg', $code, admin_url( 'themes.php?page=cfhg-setup' ) ) );
	exit;
}

/**
 * Action: apply tokens.
 */
function cfhg_admin_apply_tokens() {
	cfhg_admin_guard( 'cfhg_apply_tokens' );
	cfhg_admin_back( cfhg_elementor_apply_tokens() ? 'tokens' : 'error' );
}
add_action( 'admin_post_cfhg_apply_tokens', 'cfhg_admin_apply_tokens' );

/**
 * Action: create the Elementor homepage.
 */
function cfhg_admin_create_home() {
	cfhg_admin_guard( 'cfhg_create_home' );
	$set_front = ! empty( $_POST['set_front'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked in guard.
	$page_id   = cfhg_create_elementor_home( $set_front );
	if ( is_wp_error( $page_id ) ) {
		cfhg_admin_back( 'error' );
	}
	$edit = add_query_arg(
		array(
			'post'   => $page_id,
			'action' => 'elementor',
		),
		admin_url( 'post.php' )
	);
	wp_safe_redirect( $edit );
	exit;
}
add_action( 'admin_post_cfhg_create_home', 'cfhg_admin_create_home' );

/**
 * Action: download the template file.
 */
function cfhg_admin_download_template() {
	cfhg_admin_guard( 'cfhg_download_template' );
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="cfhg-homepage-option-a.json"' );
	echo wp_json_encode( cfhg_elementor_template_file(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_cfhg_download_template', 'cfhg_admin_download_template' );

/**
 * Action: create the starter menu.
 */
function cfhg_admin_create_menu() {
	cfhg_admin_guard( 'cfhg_create_menu' );

	$name = __( 'Main menu', 'cfhg' );
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		$menu_id = (int) $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			cfhg_admin_back( 'error' );
		}
		foreach ( cfhg_default_menu_items() as $position => $item ) {
			$page = get_page_by_path( trim( $item[1], '/' ) );
			$args = $page
				? array(
					'menu-item-title'     => $item[0],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
				)
				: array(
					'menu-item-title' => $item[0],
					'menu-item-url'   => cfhg_url( $item[1] ),
					'menu-item-type'  => 'custom',
				);
			$args['menu-item-status']   = 'publish';
			$args['menu-item-position'] = $position + 1;
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	if ( empty( $locations['footer'] ) ) {
		$locations['footer'] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	cfhg_admin_back( 'menu' );
}
add_action( 'admin_post_cfhg_create_menu', 'cfhg_admin_create_menu' );

/**
 * Nudge on the Themes and Dashboard screens until Elementor has the tokens.
 */
function cfhg_admin_notice() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'themes', 'dashboard' ), true ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_theme_options' ) || ! cfhg_elementor_is_active() || cfhg_elementor_kit_is_synced() ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Elementor does not have the CFHG colours and fonts yet.', 'cfhg' ),
		esc_url( admin_url( 'themes.php?page=cfhg-setup' ) ),
		esc_html__( 'Open CFHG Setup', 'cfhg' )
	);
}
add_action( 'admin_notices', 'cfhg_admin_notice' );
