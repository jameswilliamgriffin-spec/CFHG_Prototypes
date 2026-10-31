<?php
/**
 * Islands post type.
 *
 * Each island is a post with a featured image, a short summary (shown on
 * cards) and a label (the bar above the card, e.g. "Sub-group"). Adding an
 * island is just adding a post; the homepage scroller picks it up.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the post type and its meta.
 */
function cfhg_c_register_islands() {
	register_post_type(
		'cfhg_c_island',
		array(
			'labels'       => array(
				'name'                  => __( 'Islands', 'caribbean-family-history-c' ),
				'singular_name'         => __( 'Island', 'caribbean-family-history-c' ),
				'add_new'               => __( 'Add island', 'caribbean-family-history-c' ),
				'add_new_item'          => __( 'Add new island', 'caribbean-family-history-c' ),
				'edit_item'             => __( 'Edit island', 'caribbean-family-history-c' ),
				'new_item'              => __( 'New island', 'caribbean-family-history-c' ),
				'view_item'             => __( 'View island', 'caribbean-family-history-c' ),
				'search_items'          => __( 'Search islands', 'caribbean-family-history-c' ),
				'not_found'             => __( 'No islands found', 'caribbean-family-history-c' ),
				'all_items'             => __( 'All islands', 'caribbean-family-history-c' ),
				'featured_image'        => __( 'Island image', 'caribbean-family-history-c' ),
				'set_featured_image'    => __( 'Set island image', 'caribbean-family-history-c' ),
				'remove_featured_image' => __( 'Remove island image', 'caribbean-family-history-c' ),
				'menu_name'             => __( 'Islands', 'caribbean-family-history-c' ),
			),
			'public'       => true,
			'has_archive'  => 'islands',
			'rewrite'      => array(
				'slug'       => 'islands',
				'with_front' => false,
			),
			'menu_icon'    => 'dashicons-location-alt',
			'menu_position' => 21,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions', 'elementor' ),
		)
	);

	$meta = array(
		'cfhg_c_summary' => __( 'One or two sentences shown on island cards.', 'caribbean-family-history-c' ),
		'cfhg_c_label'   => __( 'Short label shown in the bar above the card.', 'caribbean-family-history-c' ),
	);
	foreach ( $meta as $key => $description ) {
		register_post_meta(
			'cfhg_c_island',
			$key,
			array(
				'type'              => 'string',
				'description'       => $description,
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'cfhg_c_register_islands' );

/**
 * Flush permalinks once when the theme is switched on, so /islands/ works.
 */
function cfhg_c_flush_on_activate() {
	cfhg_c_register_islands();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'cfhg_c_flush_on_activate' );

/**
 * Summary and label fields on the island edit screen.
 */
function cfhg_c_island_meta_box() {
	add_meta_box(
		'cfhg_c_island_details',
		__( 'Island card', 'caribbean-family-history-c' ),
		'cfhg_c_island_meta_box_html',
		'cfhg_c_island',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cfhg_c_island_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post.
 */
function cfhg_c_island_meta_box_html( $post ) {
	wp_nonce_field( 'cfhg_c_island_save', 'cfhg_c_island_nonce' );
	$summary = get_post_meta( $post->ID, 'cfhg_c_summary', true );
	$label   = get_post_meta( $post->ID, 'cfhg_c_label', true );
	?>
	<p>
		<label for="cfhg-c-summary"><strong><?php esc_html_e( 'Summary', 'caribbean-family-history-c' ); ?></strong></label>
		<textarea id="cfhg-c-summary" name="cfhg_c_summary" rows="4" maxlength="200" style="width:100%"><?php echo esc_textarea( $summary ); ?></textarea>
		<span class="description"><?php esc_html_e( 'One or two sentences, under 200 characters. Shown on the homepage scroller.', 'caribbean-family-history-c' ); ?></span>
	</p>
	<p>
		<label for="cfhg-c-label"><strong><?php esc_html_e( 'Card label', 'caribbean-family-history-c' ); ?></strong></label>
		<input id="cfhg-c-label" name="cfhg_c_label" type="text" value="<?php echo esc_attr( $label ); ?>" maxlength="30" style="width:100%">
		<span class="description"><?php esc_html_e( 'For example: Sub-group, Research guide.', 'caribbean-family-history-c' ); ?></span>
	</p>
	<p class="description"><?php esc_html_e( 'Set the Island image in the panel below. The order of islands follows the Order field under Page Attributes.', 'caribbean-family-history-c' ); ?></p>
	<?php
}

/**
 * Save the meta box.
 *
 * @param int $post_id Post id.
 */
function cfhg_c_island_save( $post_id ) {
	if ( ! isset( $_POST['cfhg_c_island_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cfhg_c_island_nonce'] ) ), 'cfhg_c_island_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['cfhg_c_summary'] ) ) {
		update_post_meta( $post_id, 'cfhg_c_summary', sanitize_textarea_field( wp_unslash( $_POST['cfhg_c_summary'] ) ) );
	}
	if ( isset( $_POST['cfhg_c_label'] ) ) {
		update_post_meta( $post_id, 'cfhg_c_label', sanitize_text_field( wp_unslash( $_POST['cfhg_c_label'] ) ) );
	}
}
add_action( 'save_post_cfhg_c_island', 'cfhg_c_island_save' );

/**
 * Islands archive in menu order, all on one page.
 *
 * @param WP_Query $query Query.
 */
function cfhg_c_island_archive_order( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'cfhg_c_island' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$query->set( 'posts_per_page', 50 );
	}
}
add_action( 'pre_get_posts', 'cfhg_c_island_archive_order' );

/**
 * Islands as plain arrays for cards. Uses the Island posts when there are
 * any, and the five starter islands from content.json when there are none,
 * so the homepage never shows an empty scroller.
 *
 * @param int $exclude Post id to leave out (the current island).
 * @return array<int, array{name:string,url:string,summary:string,label:string,image:string,slug:string}>
 */
function cfhg_c_get_islands( $exclude = 0 ) {
	$posts = get_posts(
		array(
			'post_type'        => 'cfhg_c_island',
			'post_status'      => 'publish',
			'numberposts'      => 50,
			'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'exclude'          => $exclude ? array( (int) $exclude ) : array(),
			'suppress_filters' => false,
		)
	);

	$islands = array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$summary = get_post_meta( $post->ID, 'cfhg_c_summary', true );
			if ( '' === $summary ) {
				$summary = get_the_excerpt( $post );
			}
			$islands[] = array(
				'name'    => get_the_title( $post ),
				'url'     => get_permalink( $post ),
				'summary' => $summary,
				'label'   => get_post_meta( $post->ID, 'cfhg_c_label', true ),
				'image'   => get_the_post_thumbnail( $post, 'cfhg-c-card', array( 'class' => 'cfhg-c-island-card__img', 'sizes' => '(min-width: 64em) 22rem, 80vw', 'loading' => 'lazy' ) ),
				'slug'    => $post->post_name,
			);
		}
		return $islands;
	}

	$current = $exclude ? get_post_field( 'post_name', $exclude ) : '';
	foreach ( (array) cfhg_c_content( 'islands', array() ) as $island ) {
		if ( $current && $current === $island['slug'] ) {
			continue;
		}
		$islands[] = array(
			'name'    => $island['name'],
			'url'     => home_url( '/islands/' . $island['slug'] . '/' ),
			'summary' => $island['summary'],
			'label'   => $island['label'],
			'image'   => cfhg_c_theme_image( $island['image'], '', array( 'class' => 'cfhg-c-island-card__img', 'sizes' => '(min-width: 64em) 22rem, 80vw' ) ),
			'slug'    => $island['slug'],
		);
	}

	return $islands;
}

/**
 * Starter data for one island from content.json, by slug.
 *
 * @param string $slug Slug.
 * @return array|null
 */
function cfhg_c_starter_island( $slug ) {
	foreach ( (array) cfhg_c_content( 'islands', array() ) as $island ) {
		if ( $island['slug'] === $slug ) {
			return $island;
		}
	}
	return null;
}
