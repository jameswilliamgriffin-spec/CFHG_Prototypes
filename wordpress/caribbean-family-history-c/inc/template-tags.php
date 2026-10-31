<?php
/**
 * Small output helpers used by the templates.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A square, museum-label style button.
 *
 * @param string $label   Text.
 * @param string $url     Link (site-relative paths are allowed).
 * @param string $variant 'outline', 'solid', 'gold', 'light' or 'text'.
 * @param array  $args    size ('sm' or 'lg'), arrow (bool), class.
 * @return string
 */
function cfhg_c_button( $label, $url, $variant = 'outline', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'  => 'lg',
			'arrow' => true,
			'class' => '',
		)
	);

	$classes = trim( 'cfhg-c-btn cfhg-c-btn--' . $variant . ' cfhg-c-btn--' . $args['size'] . ' ' . $args['class'] );
	$arrow   = $args['arrow'] ? '<span class="cfhg-c-btn__arrow" aria-hidden="true">&rarr;</span>' : '';

	return sprintf(
		'<a class="%1$s" href="%2$s"><span>%3$s</span>%4$s</a>',
		esc_attr( $classes ),
		esc_url( cfhg_c_url( $url ) ),
		esc_html( $label ),
		$arrow
	);
}

/**
 * Small uppercase label above a heading.
 *
 * @param string $text Text.
 * @param string $tag  Element.
 */
function cfhg_c_eyebrow( $text, $tag = 'p' ) {
	if ( '' === (string) $text ) {
		return;
	}
	printf( '<%1$s class="cfhg-c-eyebrow">%2$s</%1$s>', tag_escape( $tag ), esc_html( $text ) );
}

/**
 * Six-colour rule taken from the logo ring.
 *
 * @param string $class Extra class.
 */
function cfhg_c_ring_rule( $class = '' ) {
	echo '<div class="' . esc_attr( trim( 'cfhg-c-ring ' . $class ) ) . '" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span></div>';
}

/**
 * Site logo: the custom logo when one is set, otherwise the palm logo that
 * ships with the theme.
 *
 * @param string $tone 'dark' for light backgrounds, 'light' for dark ones.
 */
function cfhg_c_logo( $tone = 'dark' ) {
	if ( 'dark' === $tone && has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$file = 'light' === $tone ? 'cfhg-c-logo-light.png' : 'cfhg-c-logo.png';

	printf(
		'<a class="cfhg-c-logo" href="%1$s" rel="home"><img src="%2$s" alt="%3$s" width="271" height="80"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( CFHG_C_URI . '/assets/images/' . $file ),
		esc_attr( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : cfhg_c_content( 'site.name', '' ) )
	);
}

/**
 * Donate and Become a member buttons. Labels and links come from the
 * Customizer (Appearance > Customize > Caribbean Family History Group).
 *
 * @param string $context 'header' or 'panel'.
 */
function cfhg_c_header_buttons( $context = 'header' ) {
	$size = 'header' === $context ? 'sm' : 'lg';

	echo '<div class="cfhg-c-header-buttons">';
	echo cfhg_c_button( cfhg_c_mod( 'cfhg_c_donate_label' ), cfhg_c_mod( 'cfhg_c_donate_url' ), 'outline', array( 'size' => $size, 'arrow' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo cfhg_c_button( cfhg_c_mod( 'cfhg_c_member_label' ), cfhg_c_mod( 'cfhg_c_member_url' ), 'gold', array( 'size' => $size, 'arrow' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

/**
 * A- / A / A+ text-size control. The script in assets/js/main.js scales the
 * root font size and remembers the choice in localStorage.
 */
function cfhg_c_text_size_control() {
	?>
	<div class="cfhg-c-textsize" role="group" aria-label="<?php esc_attr_e( 'Text size', 'caribbean-family-history-c' ); ?>" data-cfhg-c-textsize>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--down" data-cfhg-c-size="down">
			<span aria-hidden="true">A-</span><span class="screen-reader-text"><?php esc_html_e( 'Smaller text', 'caribbean-family-history-c' ); ?></span>
		</button>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--reset" data-cfhg-c-size="reset">
			<span aria-hidden="true">A</span><span class="screen-reader-text"><?php esc_html_e( 'Default text size', 'caribbean-family-history-c' ); ?></span>
		</button>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--up" data-cfhg-c-size="up">
			<span aria-hidden="true">A+</span><span class="screen-reader-text"><?php esc_html_e( 'Larger text', 'caribbean-family-history-c' ); ?></span>
		</button>
		<span class="screen-reader-text" aria-live="polite" data-cfhg-c-size-status data-label="<?php esc_attr_e( 'Text size %s', 'caribbean-family-history-c' ); ?>"></span>
	</div>
	<?php
}

/**
 * Menu with a fallback built from content.json, so a fresh install has
 * working navigation before any menus are created.
 *
 * @param string $location 'cfhg_c_primary' or 'cfhg_c_utility'.
 * @param string $menu_id  Unique id for the <ul>.
 * @param string $class    Class for the <ul>.
 * @param int    $depth    Depth.
 */
function cfhg_c_menu( $location, $menu_id, $class, $depth = 2 ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'menu_id'        => $menu_id,
			'menu_class'     => $class,
			'container'      => false,
			'depth'          => $depth,
			'fallback_cb'    => 'cfhg_c_menu_fallback',
		)
	);
}

/**
 * Fallback for cfhg_c_menu().
 *
 * @param array $args wp_nav_menu arguments.
 */
function cfhg_c_menu_fallback( $args ) {
	$key   = ( isset( $args['theme_location'] ) && 'cfhg_c_utility' === $args['theme_location'] ) ? 'header.utility' : 'header.primary';
	$items = (array) cfhg_c_content( $key, array() );

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';

	printf( '<ul id="%1$s" class="%2$s">', esc_attr( $args['menu_id'] ), esc_attr( $args['menu_class'] ) );
	foreach ( $items as $item ) {
		$path    = wp_parse_url( cfhg_c_url( $item['url'] ), PHP_URL_PATH );
		$current = $path && '/' !== $path && $request && 0 === strpos( (string) $request, $path );
		printf(
			'<li class="menu-item%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			$current ? ' current-menu-item' : '',
			esc_url( cfhg_c_url( $item['url'] ) ),
			$current ? ' aria-current="page"' : '',
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Date and category line for posts.
 */
function cfhg_c_post_meta() {
	$categories = get_the_category();
	printf(
		'<p class="cfhg-c-meta"><time datetime="%1$s">%2$s</time>%3$s</p>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'j F Y' ) ),
		$categories ? '<span class="cfhg-c-meta__sep" aria-hidden="true"></span><span>' . esc_html( $categories[0]->name ) . '</span>' : ''
	);
}

/**
 * The news page URL: the posts page when one is set, otherwise /news/.
 *
 * @return string
 */
function cfhg_c_news_url() {
	$page_for_posts = (int) get_option( 'page_for_posts' );
	return $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/news/' );
}

/**
 * Newsletter sign-up form. Posts to the mailing list provider when one is
 * set in the Customizer, otherwise opens an email to the group.
 *
 * @param string $id_prefix Unique prefix for ids.
 */
function cfhg_c_newsletter_form( $id_prefix = 'cfhg-c-news' ) {
	$action = cfhg_c_mod( 'cfhg_c_newsletter_action' );
	$field  = cfhg_c_mod( 'cfhg_c_newsletter_field' );
	$email  = cfhg_c_mod( 'cfhg_c_email' );

	if ( ! $action && $email ) {
		$action = 'mailto:' . $email . '?subject=' . rawurlencode( __( 'Newsletter sign-up', 'caribbean-family-history-c' ) );
	}
	?>
	<form class="cfhg-c-newsletter" action="<?php echo esc_url( $action, array( 'http', 'https', 'mailto' ) ); ?>" method="post"<?php echo 0 === strpos( $action, 'mailto:' ) ? ' enctype="text/plain"' : ''; ?>>
		<label for="<?php echo esc_attr( $id_prefix ); ?>-email"><?php esc_html_e( 'Email address', 'caribbean-family-history-c' ); ?></label>
		<div class="cfhg-c-newsletter__row">
			<input id="<?php echo esc_attr( $id_prefix ); ?>-email" type="email" name="<?php echo esc_attr( $field ? $field : 'email' ); ?>" autocomplete="email" required>
			<button type="submit" class="cfhg-c-btn cfhg-c-btn--gold cfhg-c-btn--sm"><?php echo esc_html( cfhg_c_content( 'footer.newsletter_button', __( 'Sign up', 'caribbean-family-history-c' ) ) ); ?></button>
		</div>
	</form>
	<?php
}

/**
 * Page heading block used by inner templates.
 *
 * @param string $title      Heading.
 * @param string $eyebrow    Label above.
 * @param string $standfirst Paragraph below.
 */
function cfhg_c_page_header( $title, $eyebrow = '', $standfirst = '' ) {
	?>
	<header class="cfhg-c-pagehead">
		<div class="cfhg-c-container">
			<?php cfhg_c_eyebrow( $eyebrow ); ?>
			<h1 class="cfhg-c-pagehead__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $standfirst ) : ?>
				<p class="cfhg-c-pagehead__standfirst"><?php echo esc_html( $standfirst ); ?></p>
			<?php endif; ?>
		</div>
		<?php cfhg_c_ring_rule( 'cfhg-c-ring--thin' ); ?>
	</header>
	<?php
}
