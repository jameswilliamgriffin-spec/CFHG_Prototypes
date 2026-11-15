<?php
/**
 * Editing surname entries: the details box and the list.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Title placeholder.
 *
 * @param string  $text Placeholder.
 * @param WP_Post $post Post.
 * @return string
 */
function cfhg_site_surname_title_hint( $text, $post ) {
	return 'cfhg_surname' === $post->post_type ? __( 'Surnames, e.g. Cox / Blackman / Forde', 'cfhg-site' ) : $text;
}
add_filter( 'enter_title_here', 'cfhg_site_surname_title_hint', 10, 2 );

/**
 * The two boxes.
 */
function cfhg_site_surname_boxes() {
	add_meta_box( 'cfhg_surname_place', __( 'Where', 'cfhg-site' ), 'cfhg_site_surname_place_box', 'cfhg_surname', 'normal', 'high' );
	add_meta_box( 'cfhg_surname_member', __( 'Researched by (private)', 'cfhg-site' ), 'cfhg_site_surname_member_box', 'cfhg_surname', 'normal', 'high' );
}
add_action( 'add_meta_boxes_cfhg_surname', 'cfhg_site_surname_boxes' );

/**
 * Island is chosen in the Islands box (right). This box holds the parish.
 *
 * @param WP_Post $post Entry.
 */
function cfhg_site_surname_place_box( $post ) {
	wp_nonce_field( 'cfhg_surname_save', 'cfhg_surname_nonce' );
	?>
	<p><label for="cfhg_parish"><strong><?php esc_html_e( 'Parish or town', 'cfhg-site' ); ?></strong></label><br>
	<input type="text" class="large-text" id="cfhg_parish" name="cfhg_s[parish]" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cfhg_parish', true ) ); ?>" placeholder="<?php esc_attr_e( 'e.g. Christ Church, or Hanover / St James', 'cfhg-site' ); ?>">
	<span class="description"><?php esc_html_e( 'Leave blank if anywhere on the island. Tick the island in the Islands box on the right. Parish names here link the entry to the parish map on the island page.', 'cfhg-site' ); ?></span></p>
	<?php
}

/**
 * Private member details.
 *
 * @param WP_Post $post Entry.
 */
function cfhg_site_surname_member_box( $post ) {
	$get = static function ( $key ) use ( $post ) {
		return (string) get_post_meta( $post->ID, '_cfhg_' . $key, true );
	};
	?>
	<p class="description" style="margin-top:0"><?php esc_html_e( 'Only people who can edit the site see this. It never appears on the website. It tells whoever answers an enquiry who to put the enquirer in touch with.', 'cfhg-site' ); ?></p>
	<p><label for="cfhg_member_name"><strong><?php esc_html_e( 'Member’s name', 'cfhg-site' ); ?></strong></label><br>
	<input type="text" class="regular-text" id="cfhg_member_name" name="cfhg_s[member_name]" value="<?php echo esc_attr( $get( 'member_name' ) ); ?>" autocomplete="off"></p>
	<p><label for="cfhg_member_email"><strong><?php esc_html_e( 'Member’s email', 'cfhg-site' ); ?></strong></label><br>
	<input type="email" class="regular-text" id="cfhg_member_email" name="cfhg_s[member_email]" value="<?php echo esc_attr( $get( 'member_email' ) ); ?>" autocomplete="off"></p>
	<p><label><input type="checkbox" name="cfhg_s[consent]" value="yes" <?php checked( 'yes', $get( 'consent' ) ); ?>> <?php esc_html_e( 'This member has agreed to be put in touch with other people researching these names.', 'cfhg-site' ); ?></label></p>
	<?php
}

/**
 * Save.
 *
 * @param int $post_id Entry.
 */
function cfhg_site_surname_save( $post_id ) {
	if ( ! isset( $_POST['cfhg_surname_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cfhg_surname_nonce'] ) ), 'cfhg_surname_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$in = isset( $_POST['cfhg_s'] ) && is_array( $_POST['cfhg_s'] ) ? wp_unslash( $_POST['cfhg_s'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised below.
	update_post_meta( $post_id, '_cfhg_parish', isset( $in['parish'] ) ? sanitize_text_field( $in['parish'] ) : '' );
	update_post_meta( $post_id, '_cfhg_member_name', isset( $in['member_name'] ) ? sanitize_text_field( $in['member_name'] ) : '' );
	update_post_meta( $post_id, '_cfhg_member_email', isset( $in['member_email'] ) ? sanitize_email( $in['member_email'] ) : '' );
	update_post_meta( $post_id, '_cfhg_consent', empty( $in['consent'] ) ? '' : 'yes' );
}
add_action( 'save_post_cfhg_surname', 'cfhg_site_surname_save' );

/**
 * Tidy the surnames as they are saved: "Cox/Blackman" becomes "Cox / Blackman".
 *
 * @param array $data Post data.
 * @return array
 */
function cfhg_site_surname_tidy_title( $data ) {
	if ( 'cfhg_surname' === $data['post_type'] && '' !== $data['post_title'] ) {
		$data['post_title'] = cfhg_site_tidy_surnames( $data['post_title'] );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'cfhg_site_surname_tidy_title' );

/**
 * List columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function cfhg_site_surname_columns( $columns ) {
	return array(
		'cb'                   => $columns['cb'],
		'title'                => __( 'Surnames', 'cfhg-site' ),
		'taxonomy-cfhg_island' => __( 'Island', 'cfhg-site' ),
		'cfhg_parish'          => __( 'Parish or town', 'cfhg-site' ),
		'cfhg_member'          => __( 'Researched by (private)', 'cfhg-site' ),
	);
}
add_filter( 'manage_cfhg_surname_posts_columns', 'cfhg_site_surname_columns' );

/**
 * Fill the columns.
 *
 * @param string $column  Column.
 * @param int    $post_id Entry.
 */
function cfhg_site_surname_column( $column, $post_id ) {
	if ( 'cfhg_parish' === $column ) {
		$parish = (string) get_post_meta( $post_id, '_cfhg_parish', true );
		echo '' === $parish ? '<em>' . esc_html__( 'Anywhere', 'cfhg-site' ) . '</em>' : esc_html( $parish );
	}
	if ( 'cfhg_member' === $column ) {
		$name    = (string) get_post_meta( $post_id, '_cfhg_member_name', true );
		$consent = 'yes' === get_post_meta( $post_id, '_cfhg_consent', true );
		if ( '' === $name ) {
			echo '<em>' . esc_html__( 'Not recorded', 'cfhg-site' ) . '</em>';
		} else {
			echo esc_html( $name ) . ( $consent ? '' : ' <em>(' . esc_html__( 'no consent recorded', 'cfhg-site' ) . ')</em>' );
		}
	}
}
add_action( 'manage_cfhg_surname_posts_custom_column', 'cfhg_site_surname_column', 10, 2 );

/**
 * Sort the list A to Z by surname unless asked otherwise.
 *
 * @param WP_Query $query Query.
 */
function cfhg_site_surname_admin_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'cfhg_surname' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cfhg_site_surname_admin_order' );
