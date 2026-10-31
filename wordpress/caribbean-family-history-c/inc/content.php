<?php
/**
 * Default copy, links and images.
 *
 * The words live in inc/content.json so the static preview can use exactly
 * the same text. Anything an editor changes in WordPress (Customizer, menus,
 * posts, Elementor) takes priority over these defaults.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a value from content.json by dot path, e.g. 'home.hero.title'.
 *
 * @param string $path     Dot path. Empty returns everything.
 * @param mixed  $fallback Returned when the path does not exist.
 * @return mixed
 */
function cfhg_c_content( $path = '', $fallback = null ) {
	static $data = null;

	if ( null === $data ) {
		$file = CFHG_C_DIR . '/inc/content.json';
		$data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $data ) ) {
			$data = array();
		}
		/**
		 * Filter the default theme copy before it is used.
		 *
		 * @param array $data Decoded content.json.
		 */
		$data = apply_filters( 'cfhg_c_content', $data );
	}

	if ( '' === $path ) {
		return $data;
	}

	$node = $data;
	foreach ( explode( '.', $path ) as $key ) {
		if ( is_array( $node ) && array_key_exists( $key, $node ) ) {
			$node = $node[ $key ];
		} else {
			return $fallback;
		}
	}

	return $node;
}

/**
 * Turn a site-relative path from content.json into a full URL.
 *
 * @param string $path '/membership/', '#newsletter' or a full URL.
 * @return string
 */
function cfhg_c_url( $path ) {
	$path = (string) $path;

	if ( '' === $path || '/' === $path ) {
		return home_url( '/' );
	}
	if ( '#' === $path[0] || preg_match( '#^(https?:|mailto:|tel:)#i', $path ) ) {
		return $path;
	}

	return home_url( $path );
}

/**
 * Image dimensions for the theme's own images.
 *
 * @return array<string, array{w:int,h:int}>
 */
function cfhg_c_image_manifest() {
	static $manifest = null;

	if ( null === $manifest ) {
		$file     = CFHG_C_DIR . '/assets/images/manifest.json';
		$manifest = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $manifest ) ) {
			$manifest = array();
		}
	}

	return $manifest;
}

/**
 * URL of a theme image by key, e.g. 'generated/island-jamaica-blue-mountains'.
 *
 * @param string $key Image key without extension.
 * @return string
 */
function cfhg_c_image_url( $key ) {
	return CFHG_C_URI . '/assets/images/' . $key . '.webp';
}

/**
 * An <img> for one of the theme's own images, with width, height, srcset and
 * lazy loading. Smaller variants are picked up automatically when a file
 * named {key}-{width}.webp exists in the manifest.
 *
 * @param string $key  Image key.
 * @param string $alt  Alternative text. Pass '' for decorative images.
 * @param array  $args class, sizes, loading ('lazy' or 'eager'), fetchpriority.
 * @return string
 */
function cfhg_c_theme_image( $key, $alt, $args = array() ) {
	$manifest = cfhg_c_image_manifest();
	if ( empty( $manifest[ $key ] ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class'         => '',
			'sizes'         => '100vw',
			'loading'       => 'lazy',
			'fetchpriority' => '',
		)
	);

	$srcset = array();
	foreach ( $manifest as $candidate => $size ) {
		$is_variant = 0 === strpos( $candidate, $key . '-' ) && ctype_digit( (string) substr( $candidate, strlen( $key ) + 1 ) );
		if ( $candidate === $key || $is_variant ) {
			$srcset[ (int) $size['w'] ] = cfhg_c_image_url( $candidate ) . ' ' . (int) $size['w'] . 'w';
		}
	}
	ksort( $srcset );

	$attrs = array(
		'src'      => cfhg_c_image_url( $key ),
		'alt'      => $alt,
		'width'    => (int) $manifest[ $key ]['w'],
		'height'   => (int) $manifest[ $key ]['h'],
		'loading'  => $args['loading'],
		'decoding' => 'async',
	);
	if ( count( $srcset ) > 1 ) {
		$attrs['srcset'] = implode( ', ', $srcset );
		$attrs['sizes']  = $args['sizes'];
	}
	if ( $args['class'] ) {
		$attrs['class'] = $args['class'];
	}
	if ( $args['fetchpriority'] ) {
		$attrs['fetchpriority'] = $args['fetchpriority'];
	}

	$html = '<img';
	foreach ( $attrs as $name => $value ) {
		$html .= ' ' . $name . '="' . ( 'src' === $name ? esc_url( $value ) : esc_attr( $value ) ) . '"';
	}

	return $html . '>';
}

/**
 * Render body blocks from content.json: ["p", text], ["h2", text, id],
 * ["ul", [items]] and ["quote", text].
 *
 * @param array $blocks Blocks.
 * @param bool  $gutenberg Wrap in block comments so the editor opens them as blocks.
 * @return string
 */
function cfhg_c_render_blocks( $blocks, $gutenberg = false ) {
	$html = '';

	foreach ( (array) $blocks as $block ) {
		if ( ! is_array( $block ) || count( $block ) < 2 ) {
			continue;
		}
		list( $type, $value ) = $block;

		switch ( $type ) {
			case 'h2':
				$id    = isset( $block[2] ) ? ' id="' . esc_attr( $block[2] ) . '"' : '';
				$inner = '<h2 class="wp-block-heading"' . $id . '>' . esc_html( $value ) . '</h2>';
				$html .= $gutenberg ? "<!-- wp:heading -->\n{$inner}\n<!-- /wp:heading -->\n\n" : $inner . "\n";
				break;

			case 'ul':
				$items = '';
				foreach ( (array) $value as $item ) {
					$items .= $gutenberg
						? "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->\n"
						: '<li>' . esc_html( $item ) . '</li>';
				}
				$inner = '<ul class="wp-block-list">' . $items . '</ul>';
				$html .= $gutenberg ? "<!-- wp:list -->\n{$inner}\n<!-- /wp:list -->\n\n" : $inner . "\n";
				break;

			case 'quote':
				$inner = '<blockquote class="wp-block-quote"><p>' . esc_html( $value ) . '</p></blockquote>';
				$html .= $gutenberg ? "<!-- wp:quote -->\n{$inner}\n<!-- /wp:quote -->\n\n" : $inner . "\n";
				break;

			default:
				$inner = '<p>' . esc_html( $value ) . '</p>';
				$html .= $gutenberg ? "<!-- wp:paragraph -->\n{$inner}\n<!-- /wp:paragraph -->\n\n" : $inner . "\n";
		}
	}

	return $html;
}
