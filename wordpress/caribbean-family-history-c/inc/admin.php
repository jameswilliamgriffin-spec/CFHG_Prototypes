<?php
/**
 * Appearance > Option C setup: one-click starter content.
 *
 * Each action is a separate button so nothing happens without the editor
 * choosing it, and each can be run again safely.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the setup screen.
 */
function cfhg_c_admin_menu() {
	add_theme_page(
		__( 'Option C setup', 'caribbean-family-history-c' ),
		__( 'Option C setup', 'caribbean-family-history-c' ),
		'edit_theme_options',
		'cfhg-c-setup',
		'cfhg_c_setup_screen'
	);
}
add_action( 'admin_menu', 'cfhg_c_admin_menu' );

/**
 * One action button.
 *
 * @param string $action      Action name.
 * @param string $title       Heading.
 * @param string $description Description.
 * @param string $button      Button label.
 * @param string $extra       Extra form HTML (already escaped).
 */
function cfhg_c_setup_action( $action, $title, $description, $button, $extra = '' ) {
	?>
	<div class="card" style="max-width:44rem">
		<h2><?php echo esc_html( $title ); ?></h2>
		<p><?php echo esc_html( $description ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<?php wp_nonce_field( $action ); ?>
			<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php submit_button( $button, 'secondary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}

/**
 * Setup screen.
 */
function cfhg_c_setup_screen() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$done = isset( $_GET['cfhg-c-done'] ) ? sanitize_key( wp_unslash( $_GET['cfhg-c-done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$messages = array(
		'islands' => __( 'Starter islands created. Edit them under Islands.', 'caribbean-family-history-c' ),
		'menus'   => __( 'Menus created and assigned. Edit them under Appearance > Menus.', 'caribbean-family-history-c' ),
		'kit'     => __( 'Elementor global colours and fonts updated.', 'caribbean-family-history-c' ),
		'home'    => __( 'Homepage built in Elementor.', 'caribbean-family-history-c' ),
		'error'   => __( 'Something went wrong. Nothing was changed.', 'caribbean-family-history-c' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Option C setup', 'caribbean-family-history-c' ); ?></h1>
		<?php if ( isset( $messages[ $done ] ) ) : ?>
			<div class="notice notice-<?php echo 'error' === $done ? 'error' : 'success'; ?>"><p><?php echo esc_html( $messages[ $done ] ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'Starting points for a new site. Each button can be run again; it updates what it made before rather than making duplicates.', 'caribbean-family-history-c' ); ?></p>
		<?php
		cfhg_c_setup_action(
			'cfhg_c_seed_islands',
			__( '1. Starter islands', 'caribbean-family-history-c' ),
			__( 'Creates Jamaica, Barbados, St Kitts and Nevis, Trinidad and Tobago and Montserrat as Island posts, with their text, card summaries and illustrations added to the Media Library. Islands that already exist are left alone.', 'caribbean-family-history-c' ),
			__( 'Create starter islands', 'caribbean-family-history-c' )
		);
		cfhg_c_setup_action(
			'cfhg_c_seed_menus',
			__( '2. Menus', 'caribbean-family-history-c' ),
			__( 'Creates a Main navigation menu and a Header utility menu from the default links and assigns them. Point the links at your real pages afterwards.', 'caribbean-family-history-c' ),
			__( 'Create menus', 'caribbean-family-history-c' )
		);
		if ( cfhg_c_has_elementor() ) {
			cfhg_c_setup_action(
				'cfhg_c_sync_kit',
				__( '3. Elementor colours and fonts', 'caribbean-family-history-c' ),
				__( 'Writes the theme colours and fonts into Elementor Site Settings as global colours and global fonts.', 'caribbean-family-history-c' ),
				__( 'Sync to Elementor', 'caribbean-family-history-c' )
			);
			cfhg_c_setup_action(
				'cfhg_c_build_home',
				__( '4. Homepage in Elementor', 'caribbean-family-history-c' ),
				__( 'Builds a Home page from Elementor containers, with every section editable. Running it again rebuilds that page from the defaults, replacing edits made to it.', 'caribbean-family-history-c' ),
				__( 'Build homepage', 'caribbean-family-history-c' ),
				'<p><label><input type="checkbox" name="set_front" value="1" checked> ' . esc_html__( 'Use it as the front page', 'caribbean-family-history-c' ) . '</label></p>'
			);
		} else {
			echo '<div class="card" style="max-width:44rem"><h2>' . esc_html__( 'Elementor', 'caribbean-family-history-c' ) . '</h2><p>' . esc_html__( 'Install and activate Elementor to sync colours and fonts and build the homepage in Elementor. The theme works fully without it.', 'caribbean-family-history-c' ) . '</p></div>';
		}
		?>
	</div>
	<?php
}

/**
 * Check capability and nonce for a setup action.
 *
 * @param string $action Action.
 */
function cfhg_c_setup_guard( $action ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'caribbean-family-history-c' ) );
	}
	check_admin_referer( $action );
}

/**
 * Back to the setup screen with a message.
 *
 * @param string $done Message key.
 */
function cfhg_c_setup_redirect( $done ) {
	wp_safe_redirect( add_query_arg( array( 'page' => 'cfhg-c-setup', 'cfhg-c-done' => $done ), admin_url( 'themes.php' ) ) );
	exit;
}

/**
 * Copy a theme image into the Media Library.
 *
 * @param string $key     Theme image key.
 * @param string $alt     Alt text.
 * @param int    $post_id Attach to post.
 * @return int Attachment id or 0.
 */
function cfhg_c_sideload_theme_image( $key, $alt, $post_id ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$source = CFHG_C_DIR . '/assets/images/' . $key . '.webp';
	if ( ! is_readable( $source ) ) {
		return 0;
	}

	$tmp = wp_tempnam( basename( $source ) );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => basename( $source ),
			'tmp_name' => $tmp,
		),
		$post_id
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
	return (int) $id;
}

/**
 * Create the starter Island posts.
 */
function cfhg_c_handle_seed_islands() {
	cfhg_c_setup_guard( 'cfhg_c_seed_islands' );

	$order = 0;
	foreach ( (array) cfhg_c_content( 'islands', array() ) as $island ) {
		$order += 10;
		if ( get_page_by_path( $island['slug'], OBJECT, 'cfhg_c_island' ) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'cfhg_c_island',
				'post_status'  => 'publish',
				'post_title'   => $island['name'],
				'post_name'    => $island['slug'],
				'post_content' => cfhg_c_render_blocks( $island['body'], true ),
				'post_excerpt' => $island['summary'],
				'menu_order'   => $order,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			cfhg_c_setup_redirect( 'error' );
		}

		update_post_meta( $post_id, 'cfhg_c_summary', $island['summary'] );
		update_post_meta( $post_id, 'cfhg_c_label', $island['label'] );

		$image_id = cfhg_c_sideload_theme_image( $island['image'], $island['image_alt'], $post_id );
		if ( $image_id ) {
			set_post_thumbnail( $post_id, $image_id );
		}
	}

	flush_rewrite_rules();
	cfhg_c_setup_redirect( 'islands' );
}
add_action( 'admin_post_cfhg_c_seed_islands', 'cfhg_c_handle_seed_islands' );

/**
 * Create and assign the two menus.
 */
function cfhg_c_handle_seed_menus() {
	cfhg_c_setup_guard( 'cfhg_c_seed_menus' );

	$menus = array(
		'cfhg_c_primary' => array( __( 'Main navigation (Option C)', 'caribbean-family-history-c' ), 'header.primary' ),
		'cfhg_c_utility' => array( __( 'Header utility (Option C)', 'caribbean-family-history-c' ), 'header.utility' ),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $menus as $location => $menu ) {
		list( $name, $path ) = $menu;

		$existing = wp_get_nav_menu_object( $name );
		$menu_id  = $existing ? (int) $existing->term_id : wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			cfhg_c_setup_redirect( 'error' );
		}

		if ( ! $existing ) {
			$position = 0;
			foreach ( (array) cfhg_c_content( $path, array() ) as $item ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => $item['label'],
						'menu-item-url'      => cfhg_c_url( $item['url'] ),
						'menu-item-status'   => 'publish',
						'menu-item-type'     => 'custom',
						'menu-item-position' => ++$position,
					)
				);
			}
		}

		$locations[ $location ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
	cfhg_c_setup_redirect( 'menus' );
}
add_action( 'admin_post_cfhg_c_seed_menus', 'cfhg_c_handle_seed_menus' );

/**
 * Sync Elementor kit.
 */
function cfhg_c_handle_sync_kit() {
	cfhg_c_setup_guard( 'cfhg_c_sync_kit' );
	cfhg_c_setup_redirect( cfhg_c_sync_elementor_kit() ? 'kit' : 'error' );
}
add_action( 'admin_post_cfhg_c_sync_kit', 'cfhg_c_handle_sync_kit' );

/**
 * Build the Elementor homepage.
 */
function cfhg_c_handle_build_home() {
	cfhg_c_setup_guard( 'cfhg_c_build_home' );
	$result = cfhg_c_build_elementor_home( ! empty( $_POST['set_front'] ) );
	cfhg_c_setup_redirect( is_wp_error( $result ) ? 'error' : 'home' );
}
add_action( 'admin_post_cfhg_c_build_home', 'cfhg_c_handle_build_home' );

/**
 * Point new installs at the setup screen.
 */
function cfhg_c_setup_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'edit_theme_options' ) || ! $screen || 'themes' !== $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Caribbean Family History Group (Option C) is active.', 'caribbean-family-history-c' ),
		esc_url( admin_url( 'themes.php?page=cfhg-c-setup' ) ),
		esc_html__( 'Add starter islands, menus and the Elementor homepage.', 'caribbean-family-history-c' )
	);
}
add_action( 'admin_notices', 'cfhg_c_setup_notice' );
