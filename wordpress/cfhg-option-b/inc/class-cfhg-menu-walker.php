<?php
/**
 * Menu walker for the side-panel menu.
 *
 * Works with any menu built in Appearance > Menus. Top-level items get a large
 * label with a colour bar (drawn in CSS, one colour per item). Second-level items are listed underneath as smaller
 * links. Deeper levels are flattened into the second level.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Walker for the primary menu.
 */
class CFHG_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Start a sub-list.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '<ul class="cfhg-menu__sub">';
		}
	}

	/**
	 * End a sub-list.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '</ul>';
		}
	}

	/**
	 * Start an item.
	 *
	 * @param string   $output            Output.
	 * @param WP_Post  $data_object       Menu item.
	 * @param int      $depth             Depth.
	 * @param stdClass $args              Arguments.
	 * @param int      $current_object_id Current object ID.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item    = $data_object;
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = array_filter( $classes );
		$classes[] = 0 === $depth ? 'cfhg-menu__item' : 'cfhg-menu__subitem';

		$is_current = in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true );

		$output .= '<li class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '">';

		$atts = array(
			'href'         => ! empty( $item->url ) ? $item->url : '',
			'target'       => ! empty( $item->target ) ? $item->target : '',
			'rel'          => ! empty( $item->xfn ) ? $item->xfn : '',
			'title'        => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'aria-current' => $is_current ? 'page' : '',
			'class'        => 0 === $depth ? 'cfhg-menu__link' : 'cfhg-menu__sublink',
		);
		if ( '_blank' === $atts['target'] && false === strpos( $atts['rel'], 'noopener' ) ) {
			$atts['rel'] = trim( $atts['rel'] . ' noopener' );
		}
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}
			$value       = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$output .= '<a' . $attributes . '>';
		$output .= '<span class="cfhg-menu__label">' . wp_kses( $title, cfhg_inline_kses() ) . '</span>';
		$output .= '</a>';
	}

	/**
	 * End an item.
	 *
	 * @param string   $output      Output.
	 * @param WP_Post  $data_object Menu item.
	 * @param int      $depth       Depth.
	 * @param stdClass $args        Arguments.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

/**
 * Default menu items, used until a menu is assigned in Appearance > Menus.
 * ASSUMPTION: page addresses match the agreed site structure.
 *
 * @return array<int, array{0: string, 1: string}>
 */
function cfhg_default_menu_items() {
	return array(
		array( __( 'Research', 'cfhg' ), '/research/' ),
		array( __( 'What’s On', 'cfhg' ), '/whats-on/' ),
		array( __( 'Stories', 'cfhg' ), '/stories/' ),
		array( __( 'Who We Are', 'cfhg' ), '/who-we-are/' ),
		array( __( 'Join the group', 'cfhg' ), '/join/' ),
		array( __( 'Contact', 'cfhg' ), '/contact/' ),
	);
}

/**
 * Fallback for the primary menu: the same markup as the walker produces.
 *
 * @param array $args wp_nav_menu arguments.
 * @return string
 */
function cfhg_primary_menu_fallback( $args = array() ) {
	$html = '<ul class="cfhg-menu__list">';
	foreach ( cfhg_default_menu_items() as $item ) {
		$html .= sprintf(
			'<li class="cfhg-menu__item"><a class="cfhg-menu__link" href="%1$s"><span class="cfhg-menu__label">%2$s</span></a></li>',
			esc_url( cfhg_url( $item[1] ) ),
			esc_html( $item[0] )
		);
	}
	$html .= '</ul>';

	if ( ! empty( $args['echo'] ) ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	return $html;
}

/**
 * Fallback for the footer menu.
 *
 * @param array $args wp_nav_menu arguments.
 * @return string
 */
function cfhg_footer_menu_fallback( $args = array() ) {
	$html = '<ul class="cfhg-footer__links">';
	foreach ( cfhg_default_menu_items() as $item ) {
		$html .= sprintf(
			'<li><a href="%1$s"><span>%2$s</span>%3$s</a></li>',
			esc_url( cfhg_url( $item[1] ) ),
			esc_html( $item[0] ),
			cfhg_arrow()
		);
	}
	$html .= '</ul>';

	if ( ! empty( $args['echo'] ) ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	return $html;
}

/**
 * Add an arrow to footer menu links so they match the panel.
 *
 * @param string   $title Item title.
 * @param WP_Post  $item  Menu item.
 * @param stdClass $args  Arguments.
 * @param int      $depth Depth.
 * @return string
 */
function cfhg_footer_menu_arrow( $title, $item, $args, $depth ) {
	if ( isset( $args->theme_location ) && 'footer' === $args->theme_location && 0 === $depth ) {
		return '<span>' . $title . '</span>' . cfhg_arrow();
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'cfhg_footer_menu_arrow', 10, 4 );
