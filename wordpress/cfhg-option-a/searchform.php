<?php
/**
 * Search form.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_search_id = wp_unique_id( 'cfhg-search-' );
?>
<form role="search" method="get" class="cfhg-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $cfhg_search_id ); ?>"><?php esc_html_e( 'Search the site', 'cfhg' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $cfhg_search_id ); ?>" class="cfhg-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search for a name, island or topic', 'cfhg' ); ?>">
	<button type="submit" class="cfhg-search__button">
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'cfhg' ); ?></span>
		<span class="cfhg-btn__disc" aria-hidden="true"><?php echo cfhg_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
	</button>
</form>
