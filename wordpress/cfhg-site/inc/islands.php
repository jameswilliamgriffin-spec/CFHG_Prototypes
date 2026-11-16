<?php
/**
 * Island guides: the parish explorer, "Where should I look?", where to look,
 * and the island's posts.
 *
 * Each island's facts live in data/islands/{slug}.json (parishes, record
 * sources, and the rules for "Where should I look?"). An optional map at
 * data/maps/{slug}.svg, with one path per parish (id "parish-{slug}"),
 * turns the parish list into a clickable map.
 *
 * Shortcodes, all with island="jamaica":
 *   [cfhg_parish_map]      Parishes: where to look and the surnames being researched.
 *   [cfhg_record_finder]   "Where should I look?"
 *   [cfhg_island_sources]  The main record sources, as cards.
 *   [cfhg_island_posts]    Posts tagged with the island.
 *
 * Everything reads without JavaScript; assets/js/cfhg-site.js adds the
 * clicking, filtering and suggestions.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the front-end script; shortcodes enqueue it when used.
 */
function cfhg_site_register_script() {
	wp_register_script(
		'cfhg-site',
		CFHG_SITE_URL . 'assets/js/cfhg-site.js',
		array(),
		CFHG_SITE_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cfhg_site_register_script' );

/**
 * One island's guide.
 *
 * @param string $slug Island slug.
 * @return array<string, mixed>|null
 */
function cfhg_site_island( $slug ) {
	static $cache = array();
	$slug = sanitize_title( $slug );
	if ( ! array_key_exists( $slug, $cache ) ) {
		$file           = CFHG_SITE_DIR . '/data/islands/' . $slug . '.json';
		$data           = file_exists( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local plugin file.
		$cache[ $slug ] = is_array( $data ) ? apply_filters( 'cfhg_site_island', $data, $slug ) : null;
	}
	return $cache[ $slug ];
}

/**
 * Islands that have a guide.
 *
 * @return array<int, string> Slugs.
 */
function cfhg_site_island_slugs() {
	return array_map(
		static function ( $file ) {
			return basename( $file, '.json' );
		},
		(array) glob( CFHG_SITE_DIR . '/data/islands/*.json' )
	);
}

/**
 * The words a parish is known by: its name, other spellings and towns, and
 * the old parishes merged into it.
 *
 * @param array $parish Parish.
 * @return array<int, string>
 */
function cfhg_site_parish_names( $parish ) {
	$names = array_merge( array( $parish['name'] ), (array) $parish['aliases'] );
	foreach ( (array) $parish['old'] as $old ) {
		$names[] = trim( preg_replace( '/\s*\(.*\)$/', '', $old ) );
	}
	return array_values( array_unique( array_filter( $names ) ) );
}

/**
 * Whether a typed parish or town ("Hanover/St James/Trelawney") mentions a
 * parish, by any of its names.
 *
 * @param string $typed  As typed on a surname entry.
 * @param array  $parish Parish.
 * @return bool
 */
function cfhg_site_mentions_parish( $typed, $parish ) {
	foreach ( cfhg_site_parish_names( $parish ) as $name ) {
		/* "St Thomas" must not match "St Thomas in the Vale" when it's another parish's old name. */
		if ( preg_match( '/(?<![a-z])' . preg_quote( $name, '/' ) . '(?![a-z])(?!\s+in\s+the)/i', $typed ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Surname entries for each parish of an island.
 *
 * @param string $island Island slug.
 * @return array<string, array<int, string>> Parish slug => surnames.
 */
function cfhg_site_surnames_by_parish( $island ) {
	$guide = cfhg_site_island( $island );
	$out   = array();
	if ( ! $guide || ! function_exists( 'cfhg_site_surnames' ) ) {
		return $out;
	}
	foreach ( cfhg_site_surnames( $island ) as $row ) {
		foreach ( $guide['parishes'] as $parish ) {
			if ( '' !== $row['parish'] && cfhg_site_mentions_parish( $row['parish'], $parish ) ) {
				$out[ $parish['slug'] ][] = $row['surnames'];
			}
		}
	}
	return $out;
}

/**
 * What an island calls its areas: parishes unless its guide says otherwise
 * ("area": {"one": "county", "many": "counties"} for Trinidad).
 *
 * @param array  $guide Island guide.
 * @param string $form  one or many.
 * @return string
 */
function cfhg_site_area_word( $guide, $form = 'one' ) {
	$words = array(
		'one'  => __( 'parish', 'cfhg-site' ),
		'many' => __( 'parishes', 'cfhg-site' ),
	);
	if ( ! empty( $guide['area'][ $form ] ) ) {
		return (string) $guide['area'][ $form ];
	}
	return $words[ 'many' === $form ? 'many' : 'one' ];
}

/**
 * The year an island's areas were redrawn (Jamaica: 1867), or 0.
 *
 * @param array $guide Island guide.
 * @return int
 */
function cfhg_site_areas_changed( $guide ) {
	return empty( $guide['areas_changed'] ) ? 0 : (int) $guide['areas_changed'];
}

/**
 * The "draft" note shown on a guide the group hasn't checked yet.
 *
 * @param array $guide Island guide.
 * @return string
 */
function cfhg_site_draft_note( $guide ) {
	/* Once per island on a page is enough: the first part that prints it wins. */
	static $shown = array();
	if ( isset( $shown[ $guide['slug'] ] ) ) {
		return '';
	}
	$shown[ $guide['slug'] ] = true;
	if ( empty( $guide['draft'] ) ) {
		return empty( $guide['checked'] ) ? '' : '<p class="cfhg-island__checked">' . esc_html( sprintf( /* translators: %s: date. */ __( 'Last checked %s.', 'cfhg-site' ), $guide['checked'] ) ) . '</p>';
	}
	return '<p class="cfhg-island__draft">' . esc_html__( 'The group is checking these details.', 'cfhg-site' ) . '</p>';
}

/**
 * [cfhg_parish_map island="jamaica"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_parish_map_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'island' => '' ), $atts, 'cfhg_parish_map' );
	$guide = cfhg_site_island( $atts['island'] );
	if ( ! $guide || empty( $guide['parishes'] ) ) {
		return '';
	}
	wp_enqueue_script( 'cfhg-site' );

	$surnames = cfhg_site_surnames_by_parish( $guide['slug'] );
	$map_file = CFHG_SITE_DIR . '/data/maps/' . $guide['slug'] . '.svg';
	$map      = file_exists( $map_file ) ? (string) file_get_contents( $map_file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local plugin file.
	$mail     = function_exists( 'cfhg_mailto' ) ? cfhg_mailto() : '';

	$changed = cfhg_site_areas_changed( $guide );
	$area    = cfhg_site_area_word( $guide );
	$buttons = '';
	$panels  = '';
	foreach ( $guide['parishes'] as $i => $parish ) {
		$slug   = $parish['slug'];
		$count  = isset( $surnames[ $slug ] ) ? count( $surnames[ $slug ] ) : 0;
		$panel  = 'cfhg-parish-' . $guide['slug'] . '-' . $slug;
		$buttons .= sprintf(
			'<li><button type="button" class="cfhg-parishes__btn" data-parish="%1$s" aria-controls="%2$s" aria-expanded="false"><span>%3$s</span>%4$s</button></li>',
			esc_attr( $slug ),
			esc_attr( $panel ),
			esc_html( $parish['name'] ),
			$count ? '<span class="cfhg-parishes__count" title="' . esc_attr__( 'Surnames being researched here', 'cfhg-site' ) . '">' . (int) $count . '</span>' : ''
		);

		$body = empty( $parish['town'] ) ? '' : '<p class="cfhg-parish__town">' . esc_html( sprintf( /* translators: %s: town. */ __( 'Main town: %s', 'cfhg-site' ), $parish['town'] ) ) . '</p>';
		if ( ! empty( $parish['old'] ) && $changed ) {
			/* translators: %d: year. */
			$body .= '<p class="cfhg-parish__old"><strong>' . esc_html( sprintf( __( 'Before %d, also search:', 'cfhg-site' ), $changed ) ) . '</strong> ' . esc_html( implode( ', ', $parish['old'] ) ) . '</p>';
		}
		if ( ! empty( $parish['note'] ) ) {
			$body .= '<p>' . esc_html( $parish['note'] ) . '</p>';
		}
		if ( ! empty( $guide['parish_tip'] ) ) {
			$body .= '<p>' . esc_html( str_replace( '{parish}', $parish['name'], (string) $guide['parish_tip'] ) ) . '</p>';
		}
		if ( $count ) {
			$body .= '<h4>' . esc_html__( 'Surnames members are researching here', 'cfhg-site' ) . '</h4><ul class="cfhg-parish__surnames">';
			foreach ( $surnames[ $slug ] as $names ) {
				$body .= '<li>' . esc_html( $names ) . '</li>';
			}
			$body .= '</ul><p class="cfhg-parish__ask">' . ( $mail ? '<a href="' . esc_url( $mail ) . '">' . esc_html__( 'Researching one of these? Email us', 'cfhg-site' ) . '</a>' : '' ) . '</p>';
		} else {
			$body .= '<p class="cfhg-parish__none">' . esc_html( sprintf( /* translators: %s: parish or county. */ __( 'No surnames listed for this %s yet. Researching here? Tell us and we’ll add your names.', 'cfhg-site' ), $area ) ) . '</p>';
		}
		$panels .= sprintf(
			'<section class="cfhg-parish" id="%1$s" data-parish="%2$s" aria-labelledby="%1$s-title"><h3 class="cfhg-parish__name" id="%1$s-title">%3$s</h3>%4$s</section>',
			esc_attr( $panel ),
			esc_attr( $slug ),
			esc_html( $parish['name'] ),
			$body
		);
	}

	return '<div class="cfhg-parishes' . ( $map ? ' has-map' : '' ) . '" data-cfhg-parishes>'
		. ( empty( $guide['parish_note'] ) ? '' : '<p class="cfhg-parishes__note">' . esc_html( $guide['parish_note'] ) . '</p>' )
		. '<div class="cfhg-parishes__layout">'
		. ( $map ? '<div class="cfhg-parishes__map">' . $map . ( $surnames ? '<p class="cfhg-parishes__key"><span aria-hidden="true"></span>' . esc_html__( 'Shaded: members are researching families here', 'cfhg-site' ) . '</p>' : '' ) . ( empty( $guide['map_credit'] ) ? '' : '<p class="cfhg-parishes__credit">' . esc_html( $guide['map_credit'] ) . '</p>' ) . '</div>' : '' ) // phpcs:ignore -- local SVG file.
		. '<div class="cfhg-parishes__side"><p class="cfhg-parishes__hint">' . esc_html( sprintf( /* translators: %s: parish or county. */ $map ? __( 'Choose a %s on the map or in the list.', 'cfhg-site' ) : __( 'Choose a %s.', 'cfhg-site' ), $area ) ) . '</p><ul class="cfhg-parishes__list">' . $buttons . '</ul>'
		. '<div class="cfhg-parishes__panels">' . $panels . '</div></div>'
		. '</div></div>';
}
add_shortcode( 'cfhg_parish_map', 'cfhg_site_parish_map_shortcode' );

/**
 * [cfhg_record_finder island="jamaica"]: "Where should I look?"
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_record_finder_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'island' => '' ), $atts, 'cfhg_record_finder' );
	$guide = cfhg_site_island( $atts['island'] );
	if ( ! $guide || empty( $guide['finder'] ) ) {
		return '';
	}
	wp_enqueue_script( 'cfhg-site' );
	$f  = $guide['finder'];
	$id = wp_unique_id( 'cfhg-finder-' );

	$data = array(
		'island'   => $guide['name'],
		'records'  => $f['records'],
		'periods'  => $f['periods'],
		'always'   => $f['always'],
		'parishes' => array_map(
			static function ( $p ) {
				return array(
					'slug' => $p['slug'],
					'name' => $p['name'],
					'old'  => $p['old'],
				);
			},
			$guide['parishes']
		),
		'changed'  => cfhg_site_areas_changed( $guide ),
		'text'     => array(
			'tryFirst'   => __( 'Try first', 'cfhg-site' ),
			'where'      => __( 'Where:', 'cfhg-site' ),
			'open'       => __( 'Go to', 'cfhg-site' ),
			'none'       => __( 'We don’t have a suggestion for that combination yet. Bring it to a meeting: it’s exactly what we’re here for.', 'cfhg-site' ),
			/* translators: 1: year, 2: old parish names. */
			'oldNames'   => __( 'Before %1$s the area was part of: %2$s. Search those names too.', 'cfhg-site' ),
			'needChoice' => __( 'Choose what you’re looking for first.', 'cfhg-site' ),
		),
	);

	$periods = '';
	foreach ( $f['periods'] as $p ) {
		$periods .= '<option value="' . esc_attr( $p['id'] ) . '">' . esc_html( $p['label'] ) . '</option>';
	}
	$looking = '';
	foreach ( $f['looking'] as $l ) {
		$looking .= '<label class="cfhg-finder__choice"><input type="radio" name="' . esc_attr( $id ) . '-looking" value="' . esc_attr( $l['id'] ) . '"> <span>' . esc_html( $l['label'] ) . '</span></label>';
	}
	$parishes = '<option value="">' . esc_html__( 'I don’t know', 'cfhg-site' ) . '</option>';
	foreach ( $guide['parishes'] as $p ) {
		$parishes .= '<option value="' . esc_attr( $p['slug'] ) . '">' . esc_html( $p['name'] ) . '</option>';
	}

	return '<div class="cfhg-finder" data-cfhg-finder>'
		. '<form class="cfhg-finder__form" novalidate>'
		. '<fieldset class="cfhg-finder__step"><legend>' . esc_html__( 'What are you looking for?', 'cfhg-site' ) . '</legend><div class="cfhg-finder__choices">' . $looking . '</div></fieldset>'
		. '<div class="cfhg-finder__row' . ( cfhg_site_areas_changed( $guide ) ? '' : ' cfhg-finder__row--one' ) . '">'
		. '<p class="cfhg-finder__step"><label for="' . esc_attr( $id ) . '-when">' . esc_html__( 'Roughly when?', 'cfhg-site' ) . '</label><select id="' . esc_attr( $id ) . '-when" name="when">' . $periods . '</select></p>'
		/* The area only matters where old names differ, so only ask for it there. */
		. ( cfhg_site_areas_changed( $guide ) ? '<p class="cfhg-finder__step"><label for="' . esc_attr( $id ) . '-parish">' . esc_html( sprintf( /* translators: %s: parish or county. */ __( 'Which %s?', 'cfhg-site' ), cfhg_site_area_word( $guide ) ) ) . '</label><select id="' . esc_attr( $id ) . '-parish" name="parish">' . $parishes . '</select></p>' : '' )
		. '</div>'
		. '<p class="cfhg-finder__go"><button type="submit" class="cfhg-btn cfhg-btn--mark"><span class="cfhg-btn__label">' . esc_html__( 'Show me where to look', 'cfhg-site' ) . '</span></button></p>'
		. '</form>'
		. '<div class="cfhg-finder__results" aria-live="polite"></div>'
		. cfhg_site_draft_note( $guide )
		. '<noscript><p>' . esc_html__( 'This needs JavaScript. The main record sources are listed further down the page.', 'cfhg-site' ) . '</p></noscript>'
		. '<script type="application/json" class="cfhg-finder__data">' . wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '</script>'
		. '</div>';
}
add_shortcode( 'cfhg_record_finder', 'cfhg_site_record_finder_shortcode' );

/**
 * [cfhg_island_sources island="jamaica"]: the main record sources.
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_island_sources_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'island' => '' ), $atts, 'cfhg_island_sources' );
	$guide = cfhg_site_island( $atts['island'] );
	if ( ! $guide || empty( $guide['resources'] ) ) {
		return '';
	}
	$html = '<ul class="cfhg-sources">';
	foreach ( $guide['resources'] as $r ) {
		$html .= '<li class="cfhg-source"><h3 class="cfhg-source__title">' . esc_html( $r['title'] ) . '</h3><p>' . esc_html( $r['text'] ) . '</p>'
			. ( $r['url'] ? '<p class="cfhg-source__link"><a href="' . esc_url( $r['url'] ) . '" rel="noopener">' . esc_html( ! empty( $r['link'] ) ? $r['link'] : ( $r['online'] ? __( 'Visit the website', 'cfhg-site' ) : __( 'Find out more', 'cfhg-site' ) ) ) . '</a></p>' : '' )
			. '</li>';
	}
	return $html . '</ul>' . cfhg_site_draft_note( $guide );
}
add_shortcode( 'cfhg_island_sources', 'cfhg_site_island_sources_shortcode' );

/**
 * [cfhg_record_timeline island="jamaica"]: which records survive, and from
 * when. One bar per record set in the island's "Where should I look?" list,
 * on a line from 1650 to today, with the island's key years marked.
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_record_timeline_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'island' => '' ), $atts, 'cfhg_record_timeline' );
	$guide = cfhg_site_island( $atts['island'] );
	if ( ! $guide || empty( $guide['finder']['records'] ) ) {
		return '';
	}
	wp_enqueue_script( 'cfhg-site' );

	$from = empty( $guide['timeline_from'] ) ? 1650 : (int) $guide['timeline_from'];
	$to   = (int) ( ceil( ( (int) gmdate( 'Y' ) + 1 ) / 10 ) * 10 );
	$pos  = static function ( $year ) use ( $from, $to ) {
		return round( ( max( $from, min( $to, (int) $year ) ) - $from ) / ( $to - $from ) * 100, 2 );
	};
	/* What each record set is mostly used for decides its colour. */
	$kinds = array(
		'enslaved' => array( 'enslaved', __( 'Enslaved ancestors', 'cfhg-site' ) ),
		'birth'    => array( 'vital', __( 'Births, marriages and deaths', 'cfhg-site' ) ),
		'marriage' => array( 'vital', '' ),
		'death'    => array( 'vital', '' ),
		'land'     => array( 'land', __( 'Land and property', 'cfhg-site' ) ),
		'britain'  => array( 'journey', __( 'Journeys and service', 'cfhg-site' ) ),
		'indenture' => array( 'journey', '' ),
		'military' => array( 'journey', '' ),
	);
	$records = $guide['finder']['records'];
	usort(
		$records,
		static function ( $a, $b ) {
			return array( (int) $a['from'], (int) $a['to'] ) <=> array( (int) $b['from'], (int) $b['to'] );
		}
	);

	$rows = '';
	$used = array();
	foreach ( $records as $n => $r ) {
		$kind = 'vital';
		foreach ( (array) $r['for'] as $for ) {
			if ( isset( $kinds[ $for ] ) ) {
				$kind = $kinds[ $for ][0];
				break;
			}
		}
		$used[ $kind ] = true;
		$open  = (int) $r['to'] >= $to;
		/* The title may already end with its years: keep the plain name. */
		$name  = trim( preg_replace( '/,\s*\d{4} to \d{4}$/', '', $r['title'] ) );
		$years = $open
			/* translators: %d: year. */
			? sprintf( __( '%d to today', 'cfhg-site' ), (int) $r['from'] )
			/* translators: 1: first year, 2: last year. */
			: sprintf( __( '%1$d to %2$d', 'cfhg-site' ), (int) $r['from'], (int) $r['to'] );
		if ( (int) $r['from'] === (int) $r['to'] ) {
			$years = (string) (int) $r['from'];
		}
		/* The years go after the bar, before it, or inside it: wherever there is room. */
		$place = $pos( $r['to'] ) <= 60 ? 'after' : ( $pos( $r['from'] ) >= 30 ? 'before' : 'inside' );
		$rows .= '<li class="cfhg-timeline__row cfhg-timeline__row--' . esc_attr( $kind ) . ' cfhg-timeline__row--' . $place . ( $open ? ' is-open' : '' ) . '" style="--s:' . $pos( $r['from'] ) . '%;--e:' . $pos( $r['to'] ) . '%;--n:' . (int) $n . '">'
			. '<span class="cfhg-timeline__name">' . esc_html( $name ) . '</span>'
			. '<span class="cfhg-timeline__lane"><span class="cfhg-timeline__bar"></span><span class="cfhg-timeline__years">' . esc_html( $years ) . '</span></span>'
			. '</li>';
	}

	$ticks = '';
	for ( $y = (int) ( ceil( ( $from + 1 ) / 50 ) * 50 ); $y < $to; $y += 50 ) {
		$ticks .= '<span class="cfhg-timeline__tick" style="--x:' . $pos( $y ) . '%">' . $y . '</span>';
	}
	$marks = '';
	$notes = '';
	foreach ( isset( $guide['timeline'] ) ? (array) $guide['timeline'] : array() as $i => $m ) {
		$marks .= '<span class="cfhg-timeline__mark' . ( $i % 2 ? ' is-left' : '' ) . '" style="--x:' . $pos( $m['year'] ) . '%;--i:' . (int) $i . '"><b>' . (int) $m['year'] . '</b></span>';
		$notes .= '<li><b>' . (int) $m['year'] . '</b> ' . esc_html( $m['label'] ) . '</li>';
	}
	$legend = '';
	foreach ( $kinds as $k ) {
		if ( $k[1] && isset( $used[ $k[0] ] ) ) {
			$legend .= '<li class="cfhg-timeline__key cfhg-timeline__key--' . esc_attr( $k[0] ) . '">' . esc_html( $k[1] ) . '</li>';
		}
	}

	return '<div class="cfhg-timeline" data-cfhg-timeline>'
		. '<ul class="cfhg-timeline__legend">' . $legend . '</ul>'
		. '<div class="cfhg-timeline__chart">'
		. '<div class="cfhg-timeline__plot" aria-hidden="true">' . $ticks . $marks . '</div>'
		. '<ol class="cfhg-timeline__rows">' . $rows . '</ol>'
		. '</div>'
		. ( $notes ? '<ul class="cfhg-timeline__notes">' . $notes . '</ul>' : '' )
		. '</div>' . cfhg_site_draft_note( $guide );
}
add_shortcode( 'cfhg_record_timeline', 'cfhg_site_record_timeline_shortcode' );

/**
 * [cfhg_island_posts island="jamaica" limit="3"]: posts tagged with the island.
 *
 * @param array $atts Attributes.
 * @return string
 */
function cfhg_site_island_posts_shortcode( $atts ) {
	$atts  = shortcode_atts(
		array(
			'island' => '',
			'limit'  => 3,
			'empty'  => '',
			'not_in' => '',
		),
		$atts,
		'cfhg_island_posts'
	);
	$skip  = $atts['not_in'] ? get_category_by_slug( sanitize_title( $atts['not_in'] ) ) : null;
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => (int) $atts['limit'],
			'category__not_in' => $skip ? array( (int) $skip->term_id ) : array(),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'cfhg_island',
					'field'    => 'slug',
					'terms'    => array( sanitize_title( $atts['island'] ) ),
				),
			),
		)
	);
	if ( ! $posts ) {
		return '' === $atts['empty'] ? '' : '<p class="cfhg-events__empty">' . esc_html( $atts['empty'] ) . '</p>';
	}
	$html = '<ul class="cfhg-island-posts">';
	foreach ( $posts as $post ) {
		$html .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '"><span class="cfhg-island-posts__title">' . esc_html( get_the_title( $post ) ) . '</span><span class="cfhg-island-posts__date">' . esc_html( get_the_date( '', $post ) ) . '</span></a></li>';
	}
	return $html . '</ul>';
}
add_shortcode( 'cfhg_island_posts', 'cfhg_site_island_posts_shortcode' );
