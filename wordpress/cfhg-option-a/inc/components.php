<?php
/**
 * Reusable components.
 *
 * Each function returns markup. Template parts use them directly, and the
 * shortcodes in inc/shortcodes.php expose the same output to Elementor, so a
 * page built in Elementor stays identical to the theme's own homepage.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Next meeting in one of three styles.
 *
 * - inline: one line for the hero and the menu panel.
 * - big:    the large date used in the meeting section.
 * - facts:  where, online and when as a definition list.
 *
 * @param string $style inline, big or facts.
 * @return string
 */
function cfhg_component_next_meeting( $style = 'inline' ) {
	$date = cfhg_next_meeting();
	$time = trim( (string) cfhg_mod( 'meeting_time' ) );

	if ( 'big' === $style ) {
		if ( ! $date ) {
			return '';
		}
		$ts = $date->getTimestamp();
		$tz = wp_timezone();
		return sprintf(
			'<p class="cfhg-date"><span class="cfhg-date__day">%1$s</span><time class="cfhg-date__num" datetime="%2$s">%3$s</time><span class="cfhg-date__month">%4$s</span>%5$s</p>',
			esc_html( wp_date( 'l', $ts, $tz ) ),
			esc_attr( wp_date( 'Y-m-d', $ts, $tz ) ),
			esc_html( wp_date( 'j', $ts, $tz ) ),
			esc_html( wp_date( 'F Y', $ts, $tz ) ),
			$time ? '<span class="cfhg-date__time">' . esc_html( $time ) . '</span>' : ''
		);
	}

	if ( 'facts' === $style ) {
		$rows = array(
			__( 'Where', 'cfhg' )  => cfhg_mod( 'meeting_venue' ),
			__( 'Online', 'cfhg' ) => cfhg_mod( 'meeting_online' ),
			__( 'When', 'cfhg' )   => cfhg_mod( 'meeting_pattern' ),
			__( 'Time', 'cfhg' )   => $time,
		);
		$html = '<dl class="cfhg-facts">';
		foreach ( $rows as $label => $value ) {
			if ( '' === trim( (string) $value ) ) {
				continue;
			}
			$html .= '<div class="cfhg-facts__row"><dt>' . esc_html( $label ) . '</dt><dd>' . cfhg_text( $value ) . '</dd></div>';
		}
		$html .= '</dl>';
		$note  = cfhg_mod( 'meeting_note' );
		if ( $note ) {
			$html .= '<p class="cfhg-facts__note">' . cfhg_text( $note ) . '</p>';
		}
		return $html;
	}

	if ( ! $date ) {
		return '';
	}
	return sprintf(
		'<p class="cfhg-next"><span class="cfhg-next__label">%1$s</span> <strong>%2$s</strong>%3$s</p>',
		esc_html( cfhg_mod( 'meeting_kicker' ) ),
		esc_html( cfhg_next_meeting_date( 'l j F' ) ),
		esc_html( $time ? ', ' . $time : '' ) . '<span class="cfhg-next__where">' . esc_html( cfhg_mod( 'meeting_short_where' ) ) . '</span>'
	);
}

/**
 * Prices as a ledger with dotted leaders.
 *
 * @return string
 */
function cfhg_component_prices() {
	$rows = array(
		array( cfhg_mod( 'price_first_label' ), cfhg_mod( 'price_first' ), 'is-free' ),
		array( cfhg_mod( 'price_visit_label' ), cfhg_mod( 'price_visit' ), '' ),
		array( cfhg_mod( 'price_year_label' ), cfhg_mod( 'price_year' ), '' ),
	);
	$html = '<dl class="cfhg-ledger">';
	foreach ( $rows as $row ) {
		if ( '' === trim( (string) $row[0] ) ) {
			continue;
		}
		$html .= sprintf(
			'<div class="cfhg-ledger__row %3$s"><dt>%1$s</dt><dd>%2$s</dd></div>',
			esc_html( $row[0] ),
			esc_html( $row[1] ),
			esc_attr( $row[2] )
		);
	}
	return $html . '</dl>';
}

/**
 * Island list with hairline rows.
 *
 * Plain list markup (arrows are drawn in CSS) so the same HTML can be pasted
 * into Elementor's Text Editor widget and edited there.
 *
 * @return string
 */
function cfhg_component_islands() {
	$html = '<ul class="cfhg-places">';
	foreach ( cfhg_lines( 'islands_list', 3 ) as $row ) {
		list( $name, $url, $note ) = $row;
		$inner = '<strong class="cfhg-places__name">' . esc_html( $name ) . '</strong>';
		if ( $note ) {
			$inner .= ' <span class="cfhg-places__note">' . esc_html( $note ) . '</span>';
		}
		$html .= $url
			? '<li><a class="cfhg-places__link" href="' . esc_url( cfhg_url( $url ) ) . '">' . $inner . '</a></li>'
			: '<li><span class="cfhg-places__link">' . $inner . '</span></li>';
	}
	return $html . '</ul>';
}

/**
 * Numbered steps.
 *
 * @return string
 */
function cfhg_component_steps() {
	$html = '<ol class="cfhg-steps">';
	foreach ( cfhg_lines( 'start_steps', 2 ) as $row ) {
		$html .= sprintf(
			'<li class="cfhg-steps__item"><h3 class="cfhg-steps__title">%1$s</h3><p class="cfhg-steps__text">%2$s</p></li>',
			esc_html( $row[0] ),
			esc_html( $row[1] )
		);
	}
	return $html . '</ol>';
}

/**
 * One story card.
 *
 * @param array $card {
 *     @type string[] $tags  Tag labels.
 *     @type string   $meta  Small line above the title.
 *     @type string   $title Title.
 *     @type string   $text  Summary.
 *     @type string   $url   Link.
 *     @type string   $image Image markup (already escaped).
 * }
 * @param int   $index Position, used to vary the background tone.
 * @return string
 */
function cfhg_component_story_card( $card, $index = 0 ) {
	$tones   = array( 'sand', 'sand-deep', 'paper' );
	$tone    = $card['image'] ? $tones[ $index % 3 ] : 'gold';
	$classes = 'cfhg-story cfhg-story--' . $tone . ( $card['image'] ? '' : ' cfhg-story--text' );

	$tags = '';
	foreach ( (array) $card['tags'] as $i => $tag ) {
		$tags .= '<li class="cfhg-tag' . ( 0 === $i ? ' cfhg-tag--first' : '' ) . '">' . esc_html( $tag ) . '</li>';
	}

	return sprintf(
		'<article class="%1$s"%2$s>%3$s%4$s<div class="cfhg-story__body">%5$s<h3 class="cfhg-story__title"><a href="%6$s">%7$s</a></h3>%8$s<span class="cfhg-story__disc" aria-hidden="true">%9$s</span></div></article>',
		esc_attr( $classes ),
		'',
		$tags ? '<ul class="cfhg-story__tags" aria-label="' . esc_attr__( 'Topics', 'cfhg' ) . '">' . $tags . '</ul>' : '',
		$card['image'] ? '<div class="cfhg-story__media">' . $card['image'] . '</div>' : '',
		$card['meta'] ? '<p class="cfhg-story__meta">' . esc_html( $card['meta'] ) . '</p>' : '',
		esc_url( cfhg_url( $card['url'] ) ),
		esc_html( $card['title'] ),
		$card['text'] ? '<p class="cfhg-story__text">' . esc_html( $card['text'] ) . '</p>' : '',
		cfhg_arrow()
	);
}

/**
 * Latest stories. Uses real posts from the Stories category when there are
 * any, otherwise the starter cards written from the group's own sitemap.
 *
 * @param int $count How many.
 * @return string
 */
function cfhg_component_stories( $count = 3 ) {
	$cards    = array();
	$category = sanitize_title( cfhg_mod( 'stories_category' ) );
	$query    = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => max( 1, (int) $count ),
			'category_name'       => $category,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $category && $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$tags = array();
			foreach ( (array) get_the_category() as $cat ) {
				if ( $cat->slug !== $category ) {
					$tags[] = $cat->name;
				}
			}
			if ( ! $tags ) {
				$term   = get_category_by_slug( $category );
				$tags[] = $term ? $term->name : __( 'Story', 'cfhg' );
			}
			$cards[] = array(
				'tags'  => array_slice( $tags, 0, 2 ),
				'meta'  => get_the_date(),
				'title' => get_the_title(),
				'text'  => wp_strip_all_tags( get_the_excerpt() ),
				'url'   => get_permalink(),
				'image' => has_post_thumbnail()
					? get_the_post_thumbnail( null, 'cfhg-card', array( 'sizes' => '(min-width: 1024px) 30vw, 90vw' ) )
					: '',
			);
		}
		wp_reset_postdata();
	} else {
		foreach ( array_slice( cfhg_default_stories(), 0, max( 1, (int) $count ) ) as $story ) {
			$story['image'] = $story['image']
				? cfhg_theme_image( $story['image'], array( 'sizes' => '(min-width: 1024px) 30vw, 90vw' ) )
				: '';
			$cards[]        = $story;
		}
	}

	$html = '<div class="cfhg-stories__list">';
	foreach ( $cards as $i => $card ) {
		$html .= cfhg_component_story_card( $card, $i );
	}
	return $html . '</div>';
}

/**
 * Partners as ruled rows. Plain list markup, Elementor-friendly.
 *
 * @return string
 */
function cfhg_component_partners() {
	$html = '<ul class="cfhg-partners__list">';
	foreach ( cfhg_lines( 'partners_list', 3 ) as $row ) {
		list( $name, $text, $url ) = $row;
		$name_html = $url
			? '<a href="' . esc_url( cfhg_url( $url ) ) . '" rel="noopener">' . esc_html( $name ) . '</a>'
			: esc_html( $name );
		$html     .= '<li class="cfhg-partner"><h3 class="cfhg-partner__name">' . $name_html . '</h3><p class="cfhg-partner__text">' . esc_html( $text ) . '</p></li>';
	}
	return $html . '</ul>';
}

/**
 * Social links. Hidden entirely when none are set.
 *
 * @return string
 */
function cfhg_component_social() {
	$networks = array(
		'social_instagram' => __( 'Instagram', 'cfhg' ),
		'social_x'         => __( 'X', 'cfhg' ),
		'social_facebook'  => __( 'Facebook', 'cfhg' ),
		'social_youtube'   => __( 'YouTube', 'cfhg' ),
	);
	$items = '';
	foreach ( $networks as $key => $label ) {
		$url = esc_url( cfhg_mod( $key ) );
		if ( $url ) {
			$items .= '<li><a href="' . $url . '" rel="noopener me">' . esc_html( $label ) . '</a></li>';
		}
	}
	return $items ? '<ul class="cfhg-social" aria-label="' . esc_attr__( 'Social media', 'cfhg' ) . '">' . $items . '</ul>' : '';
}
