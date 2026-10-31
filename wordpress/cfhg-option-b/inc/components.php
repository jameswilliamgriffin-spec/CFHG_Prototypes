<?php
/**
 * Reusable components (Option B).
 *
 * Each function returns markup. Template parts use them directly, and the
 * shortcodes in inc/shortcodes.php expose the same output to Elementor.
 * List components use plain list markup so the same HTML can also be pasted
 * into Elementor's Text Editor widget and edited there.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Next meeting.
 *
 * - inline: one line, used in the opening panel, menu and footer.
 * - title:  the date set large, e.g. "Saturday 31 October".
 * - facts:  where, online, when and time as a definition list.
 *
 * @param string $style inline, title or facts.
 * @return string
 */
function cfhg_component_next_meeting( $style = 'inline' ) {
	$date = cfhg_next_meeting();
	$time = trim( (string) cfhg_mod( 'meeting_time' ) );

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

	$ts = $date->getTimestamp();
	$tz = wp_timezone();

	if ( 'title' === $style ) {
		return sprintf(
			'<p class="cfhg-date"><time datetime="%1$s"><span class="cfhg-date__day">%2$s</span> <span class="cfhg-date__rest">%3$s</span></time>%4$s</p>',
			esc_attr( wp_date( 'Y-m-d', $ts, $tz ) ),
			esc_html( wp_date( 'l', $ts, $tz ) ),
			esc_html( wp_date( 'j F', $ts, $tz ) ),
			$time ? ' <span class="cfhg-date__time">' . esc_html( $time ) . '</span>' : ''
		);
	}

	return sprintf(
		'<p class="cfhg-next"><span class="cfhg-next__label">%1$s</span> <strong>%2$s</strong>%3$s<span class="cfhg-next__where">%4$s</span></p>',
		esc_html( cfhg_mod( 'meeting_kicker' ) ),
		esc_html( wp_date( 'l j F', $ts, $tz ) ),
		$time ? esc_html( ', ' . $time ) : '',
		esc_html( cfhg_mod( 'meeting_short_where' ) )
	);
}

/**
 * Prices as a simple ruled list.
 *
 * @return string
 */
function cfhg_component_prices() {
	$rows = array(
		array( cfhg_mod( 'price_first_label' ), cfhg_mod( 'price_first' ), 'is-free' ),
		array( cfhg_mod( 'price_visit_label' ), cfhg_mod( 'price_visit' ), '' ),
		array( cfhg_mod( 'price_year_label' ), cfhg_mod( 'price_year' ), '' ),
	);
	$html = '<dl class="cfhg-prices">';
	foreach ( $rows as $row ) {
		if ( '' === trim( (string) $row[0] ) ) {
			continue;
		}
		$html .= sprintf(
			'<div class="cfhg-prices__row %3$s"><dt>%1$s</dt><dd>%2$s</dd></div>',
			esc_html( $row[0] ),
			esc_html( $row[1] ),
			esc_attr( $row[2] )
		);
	}
	return $html . '</dl>';
}

/**
 * The group in numbers: scattered coloured notes, after the reference's
 * "year in numbers". Colours are assigned in CSS by position.
 *
 * @return string
 */
function cfhg_component_numbers() {
	$html = '<ul class="cfhg-notes">';
	foreach ( cfhg_lines( 'numbers_list', 2 ) as $row ) {
		$html .= sprintf(
			'<li class="cfhg-note"><span class="cfhg-note__num">%1$s</span> <span class="cfhg-note__text">%2$s</span></li>',
			esc_html( $row[0] ),
			esc_html( $row[1] )
		);
	}
	return $html . '</ul>';
}

/**
 * Five steps as coloured tiles. Numbers come from a CSS counter.
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
 * Island list. Each row gets a colour bar, assigned in CSS by position.
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
 * A photograph as a tilted snapshot in a coloured frame, with the CFHG badge
 * as a small sticker.
 *
 * @param string $setting Customizer image setting.
 * @param string $tone    Frame colour: coral, gold, green, sea or blue.
 * @param string $shape   portrait or landscape.
 * @return string
 */
function cfhg_component_snapshot( $setting, $tone = 'coral', $shape = 'portrait' ) {
	return sprintf(
		'<figure class="cfhg-snap cfhg-snap--%1$s cfhg-snap--%2$s"><span class="cfhg-snap__photo">%3$s</span><span class="cfhg-snap__sticker"><img src="%4$s" width="511" height="512" alt="" loading="lazy" decoding="async"></span></figure>',
		esc_attr( $tone ),
		esc_attr( $shape ),
		cfhg_setting_image( $setting, array( 'sizes' => '(min-width: 1024px) 22vw, 60vw' ) ),
		esc_url( CFHG_URI . '/assets/images/cfhg-logo-badge.png' )
	);
}

/**
 * Opening panel: a row of small round portraits ending in the palm badge,
 * beside one line about the members. The portraits are decorative.
 *
 * @return string
 */
function cfhg_component_hero_proof() {
	$text = cfhg_mod( 'hero_proof_text' );
	if ( ! $text ) {
		return '';
	}
	$faces = '';
	foreach ( array( 'avatar-flat-cap', 'avatar-album', 'avatar-smiling' ) as $slug ) {
		$faces .= '<span class="cfhg-avatars__face">' . cfhg_theme_image(
			$slug,
			array(
				'sizes' => '3rem',
				'alt'   => '',
			)
		) . '</span>';
	}
	return sprintf(
		'<div class="cfhg-hero__proof"><span class="cfhg-avatars" aria-hidden="true">%1$s<span class="cfhg-avatars__badge"><img src="%2$s" width="511" height="512" alt="" loading="lazy" decoding="async"></span></span><p>%3$s</p></div>',
		$faces,
		esc_url( CFHG_URI . '/assets/images/cfhg-logo-badge.png' ),
		esc_html( $text )
	);
}

/**
 * One slide in the story carousel.
 *
 * @param array $card  Tags, meta, title, text, url, image (escaped markup).
 * @param int   $index Position.
 * @param int   $total Number of slides.
 * @return string
 */
function cfhg_component_story_slide( $card, $index, $total ) {
	$tones = array( 'sand', 'sea', 'gold', 'green', 'coral' );
	$tone  = $tones[ $index % count( $tones ) ];

	$tags = '';
	foreach ( (array) $card['tags'] as $tag ) {
		$tags .= '<li class="cfhg-tag">' . esc_html( $tag ) . '</li>';
	}

	return sprintf(
		'<article class="cfhg-slide cfhg-slide--%1$s%2$s" aria-roledescription="%3$s" aria-label="%4$s">%5$s<div class="cfhg-slide__body"><div class="cfhg-slide__top">%6$s<span class="cfhg-slide__count">%7$s</span></div>%8$s<h3 class="cfhg-slide__title">%9$s</h3>%10$s%11$s</div></article>',
		esc_attr( $tone ),
		$card['image'] ? '' : ' cfhg-slide--text',
		esc_attr__( 'slide', 'cfhg' ),
		/* translators: 1: slide number, 2: number of slides. */
		esc_attr( sprintf( __( '%1$d of %2$d', 'cfhg' ), $index + 1, $total ) ),
		$card['image'] ? '<div class="cfhg-slide__media">' . $card['image'] . '</div>' : '',
		$tags ? '<ul class="cfhg-slide__tags" aria-label="' . esc_attr__( 'Topics', 'cfhg' ) . '">' . $tags . '</ul>' : '<span></span>',
		esc_html( ( $index + 1 ) . '/' . $total ),
		$card['meta'] ? '<p class="cfhg-slide__meta">' . esc_html( $card['meta'] ) . '</p>' : '',
		esc_html( $card['title'] ),
		$card['text'] ? '<p class="cfhg-slide__text">' . esc_html( $card['text'] ) . '</p>' : '',
		cfhg_button( cfhg_mod( 'stories_read_label' ), $card['url'], 'mark', 'cfhg-slide__cta' )
	);
}

/**
 * Story cards: real posts from the Stories category when there are any,
 * otherwise starter cards written from the group's sitemap.
 *
 * @param int $count How many.
 * @return array<int, array<string, mixed>>
 */
function cfhg_story_cards( $count = 3 ) {
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
			$cards[] = array(
				'tags'  => array_slice( $tags, 0, 2 ),
				'meta'  => get_the_date(),
				'title' => get_the_title(),
				'text'  => wp_strip_all_tags( get_the_excerpt() ),
				'url'   => get_permalink(),
				'image' => has_post_thumbnail()
					? get_the_post_thumbnail( null, 'cfhg-card', array( 'sizes' => '(min-width: 1024px) 34vw, 90vw' ) )
					: '',
			);
		}
		wp_reset_postdata();
		return $cards;
	}

	foreach ( array_slice( cfhg_default_stories(), 0, max( 1, (int) $count ) ) as $story ) {
		$story['image'] = $story['image']
			? cfhg_theme_image( $story['image'], array( 'sizes' => '(min-width: 1024px) 34vw, 90vw' ) )
			: '';
		$cards[]        = $story;
	}
	return $cards;
}

/**
 * The story carousel: scroll-snap slides with previous and next buttons.
 * Works without JavaScript as a sideways-scrolling row.
 *
 * @param int $count How many stories.
 * @return string
 */
function cfhg_component_stories( $count = 3 ) {
	$cards = cfhg_story_cards( $count );
	$total = count( $cards );
	$html  = '';
	foreach ( $cards as $i => $card ) {
		$html .= cfhg_component_story_slide( $card, $i, $total );
	}
	return sprintf(
		'<div class="cfhg-carousel" data-cfhg-carousel><div class="cfhg-carousel__track" tabindex="0" role="region" aria-roledescription="%1$s" aria-label="%2$s">%3$s</div><div class="cfhg-carousel__nav"><button type="button" class="cfhg-carousel__btn" data-cfhg-prev aria-label="%4$s">%5$s</button><button type="button" class="cfhg-carousel__btn" data-cfhg-next aria-label="%6$s">%7$s</button></div></div>',
		esc_attr__( 'carousel', 'cfhg' ),
		esc_attr( cfhg_mod( 'stories_heading' ) ),
		$html,
		esc_attr__( 'Previous story', 'cfhg' ),
		'<span class="cfhg-flip">' . cfhg_arrow() . '</span>',
		esc_attr__( 'Next story', 'cfhg' ),
		cfhg_arrow()
	);
}

/**
 * Partners as white cards with a colour bar. Plain list markup.
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
	$items    = '';
	foreach ( $networks as $key => $label ) {
		$url = esc_url( cfhg_mod( $key ) );
		if ( $url ) {
			$items .= '<li><a href="' . $url . '" rel="noopener me">' . esc_html( $label ) . '</a></li>';
		}
	}
	return $items ? '<ul class="cfhg-social" aria-label="' . esc_attr__( 'Social media', 'cfhg' ) . '">' . $items . '</ul>' : '';
}
