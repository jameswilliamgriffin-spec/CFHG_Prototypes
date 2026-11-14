<?php
/**
 * Events > Add a series: create a year of meetings in one go.
 *
 * Pick a pattern ("last Saturday of each month", "third Sunday of each
 * month") or paste in a list of dates, see the dates it will make, untick
 * any that are wrong, then create them. Dates the group already has an event
 * on are skipped, so running it twice does no harm.
 *
 * @package CFHG_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the screen under Events.
 */
function cfhg_site_series_menu() {
	add_submenu_page( 'edit.php?post_type=cfhg_event', __( 'Add a series of events', 'cfhg-site' ), __( 'Add a series', 'cfhg-site' ), 'edit_posts', 'cfhg-event-series', 'cfhg_site_series_page' );
}
add_action( 'admin_menu', 'cfhg_site_series_menu', 15 );

/**
 * Read and tidy the form.
 *
 * @param array $in Raw input.
 * @return array<string, mixed>
 */
function cfhg_site_series_input( $in ) {
	$in   = (array) $in;
	$skip = isset( $in['skip'] ) ? array_map( 'intval', (array) $in['skip'] ) : array( 5, 8, 12 );
	return array(
		'title'   => isset( $in['title'] ) && '' !== trim( $in['title'] ) ? sanitize_text_field( $in['title'] ) : __( 'Monthly meeting', 'cfhg-site' ),
		'group'   => isset( $in['group'] ) ? absint( $in['group'] ) : 0,
		'type'    => isset( $in['type'] ) ? absint( $in['type'] ) : 0,
		'island'  => isset( $in['island'] ) ? absint( $in['island'] ) : 0,
		'pattern' => isset( $in['pattern'] ) && in_array( $in['pattern'], array( 'nth', 'list' ), true ) ? $in['pattern'] : 'nth',
		'nth'     => isset( $in['nth'] ) && in_array( $in['nth'], array( 'first', 'second', 'third', 'fourth', 'last' ), true ) ? $in['nth'] : 'last',
		'weekday' => isset( $in['weekday'] ) && in_array( $in['weekday'], array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ), true ) ? $in['weekday'] : 'saturday',
		'from'    => isset( $in['from'] ) && preg_match( '/^\d{4}-\d{2}$/', $in['from'] ) ? $in['from'] : current_time( 'Y-m' ),
		'to'      => isset( $in['to'] ) && preg_match( '/^\d{4}-\d{2}$/', $in['to'] ) ? $in['to'] : ( (int) current_time( 'Y' ) + 1 ) . '-12',
		'skip'    => array_values( array_filter( $skip, static fn( $m ) => $m >= 1 && $m <= 12 ) ),
		'dates'   => isset( $in['dates'] ) ? sanitize_textarea_field( $in['dates'] ) : '',
		'start'   => isset( $in['start'] ) && preg_match( '/^\d{2}:\d{2}$/', $in['start'] ) ? $in['start'] : '',
		'end'     => isset( $in['end'] ) && preg_match( '/^\d{2}:\d{2}$/', $in['end'] ) ? $in['end'] : '',
		'attend'  => isset( $in['attend'] ) && array_key_exists( $in['attend'], cfhg_site_attend_options() ) ? $in['attend'] : 'both',
		'venue'   => isset( $in['venue'] ) ? sanitize_text_field( $in['venue'] ) : '',
	);
}

/**
 * Turn one typed date into Y-m-d. Accepts 2026-11-28, 28/11/2026,
 * 28 November 2026, Saturday 28th November 2026 and similar.
 *
 * @param string $text Date as typed.
 * @return string Y-m-d, or '' if it can't be read.
 */
function cfhg_site_parse_date( $text ) {
	$text = trim( preg_replace( '/(\d)(st|nd|rd|th)\b/i', '$1', (string) $text ) );
	$text = trim( preg_replace( '/^(mon|tues|wednes|thurs|fri|satur|sun)day,?\s+/i', '', $text ) );
	if ( '' === $text ) {
		return '';
	}
	$tz = wp_timezone();
	foreach ( array( '!Y-m-d', '!d/m/Y', '!j/n/Y', '!j F Y', '!j M Y', '!d F Y', '!d M Y' ) as $format ) {
		$date = DateTimeImmutable::createFromFormat( $format, $text, $tz );
		if ( $date && $date->format( ltrim( $format, '!' ) ) === $text ) {
			return $date->format( 'Y-m-d' );
		}
	}
	return '';
}

/**
 * The dates a series would create.
 *
 * @param array $s Tidied input.
 * @return array{dates: array<int, string>, unread: array<int, string>, mismatch: array<int, string>}
 */
function cfhg_site_series_dates( $s ) {
	$dates    = array();
	$unread   = array();
	$mismatch = array();
	if ( 'list' === $s['pattern'] ) {
		foreach ( preg_split( '/\r\n|\r|\n/', $s['dates'] ) as $line ) {
			if ( '' === trim( $line ) ) {
				continue;
			}
			$date = cfhg_site_parse_date( $line );
			if ( $date ) {
				$dates[] = $date;
				/* "Sunday 18 January 2027" when the 18th is a Monday: usually the wrong year. */
				if ( preg_match( '/^\s*(monday|tuesday|wednesday|thursday|friday|saturday|sunday)/i', $line, $typed ) && strtolower( $typed[1] ) !== strtolower( gmdate( 'l', strtotime( $date . ' 12:00 UTC' ) ) ) ) {
					/* translators: 1: date as typed, 2: real day of the week. */
					$mismatch[] = sprintf( __( '%1$s (that date is a %2$s)', 'cfhg-site' ), trim( $line ), gmdate( 'l', strtotime( $date . ' 12:00 UTC' ) ) );
				}
			} else {
				$unread[] = trim( $line );
			}
		}
	} else {
		$tz    = wp_timezone();
		$month = DateTimeImmutable::createFromFormat( '!Y-m-d', $s['from'] . '-01', $tz );
		$last  = DateTimeImmutable::createFromFormat( '!Y-m-d', $s['to'] . '-01', $tz );
		for ( $i = 0; $month && $last && $month <= $last && $i < 60; $i++, $month = $month->modify( '+1 month' ) ) {
			if ( in_array( (int) $month->format( 'n' ), $s['skip'], true ) ) {
				continue;
			}
			$date = $month->modify( $s['nth'] . ' ' . $s['weekday'] . ' of this month' );
			if ( $date && $date->format( 'Y-m' ) === $month->format( 'Y-m' ) ) {
				$dates[] = $date->format( 'Y-m-d' );
			}
		}
	}
	$dates = array_values( array_unique( $dates ) );
	sort( $dates );
	return array(
		'dates'    => $dates,
		'unread'   => $unread,
		'mismatch' => $mismatch,
	);
}

/**
 * Create the ticked dates, then come back with a count.
 */
function cfhg_site_series_create() {
	if ( ! isset( $_POST['cfhg_series_create'] ) ) {
		return;
	}
	check_admin_referer( 'cfhg_series' );
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'cfhg-site' ) );
	}
	$s       = cfhg_site_series_input( isset( $_POST['cfhg'] ) ? wp_unslash( $_POST['cfhg'] ) : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- tidied in cfhg_site_series_input().
	$chosen  = isset( $_POST['cfhg_dates'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['cfhg_dates'] ) ) : array();
	$made    = 0;
	$skipped = 0;
	foreach ( $chosen as $date ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			continue;
		}
		if ( cfhg_site_event_exists( $date, $s['group'] ) ) {
			++$skipped;
			continue;
		}
		$id = cfhg_site_create_event(
			array(
				'title'  => $s['title'],
				'date'   => $date,
				'start'  => $s['start'],
				'end'    => $s['end'],
				'attend' => $s['attend'],
				'venue'  => $s['venue'],
				'group'  => $s['group'],
				'type'   => $s['type'],
				'island' => $s['island'],
			)
		);
		if ( ! is_wp_error( $id ) ) {
			++$made;
		}
	}
	wp_safe_redirect(
		add_query_arg(
			array(
				'post_type'    => 'cfhg_event',
				'cfhg_when'    => 'upcoming',
				'cfhg_made'    => $made,
				'cfhg_skipped' => $skipped,
			),
			admin_url( 'edit.php' )
		)
	);
	exit;
}
add_action( 'admin_init', 'cfhg_site_series_create' );

/**
 * Say how many were made, on the events list.
 */
function cfhg_site_series_notice() {
	if ( ! isset( $_GET['cfhg_made'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$made    = absint( $_GET['cfhg_made'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$skipped = isset( $_GET['cfhg_skipped'] ) ? absint( $_GET['cfhg_skipped'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	/* translators: %d: number of events. */
	$text = sprintf( _n( '%d event added.', '%d events added.', $made, 'cfhg-site' ), $made );
	if ( $skipped ) {
		/* translators: %d: number of dates. */
		$text .= ' ' . sprintf( _n( '%d date was already in the calendar and was left alone.', '%d dates were already in the calendar and were left alone.', $skipped, 'cfhg-site' ), $skipped );
	}
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
}
add_action( 'admin_notices', 'cfhg_site_series_notice' );

/**
 * A select of terms.
 *
 * @param string $taxonomy Taxonomy.
 * @param string $name     Field name.
 * @param int    $current  Selected term ID.
 * @param string $none     Label for "none", or '' for no such option.
 * @param string $default  Slug to pick when nothing is selected yet.
 */
function cfhg_site_term_select( $taxonomy, $name, $current, $none = '', $default = '' ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);
	echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( sanitize_key( $name ) ) . '">';
	if ( $none ) {
		echo '<option value="0">' . esc_html( $none ) . '</option>';
	}
	foreach ( (array) $terms as $term ) {
		$selected = $current ? (int) $current === (int) $term->term_id : $default === $term->slug;
		printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $term->term_id, selected( $selected, true, false ), esc_html( $term->name ) );
	}
	echo '</select>';
}

/**
 * The screen.
 */
function cfhg_site_series_page() {
	$posted  = isset( $_POST['cfhg_series_preview'] ) && check_admin_referer( 'cfhg_series' );
	$s       = cfhg_site_series_input( $posted && isset( $_POST['cfhg'] ) ? wp_unslash( $_POST['cfhg'] ) : array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- tidied in cfhg_site_series_input().
	$months  = array();
	for ( $m = 1; $m <= 12; $m++ ) {
		$months[ $m ] = wp_date( 'F', mktime( 0, 0, 0, $m, 1 ) );
	}
	$ordinal = array(
		'first'  => __( 'First', 'cfhg-site' ),
		'second' => __( 'Second', 'cfhg-site' ),
		'third'  => __( 'Third', 'cfhg-site' ),
		'fourth' => __( 'Fourth', 'cfhg-site' ),
		'last'   => __( 'Last', 'cfhg-site' ),
	);
	$days    = array();
	foreach ( array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ) as $day ) {
		$days[ $day ] = wp_date( 'l', strtotime( 'next ' . $day ) );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Add a series of events', 'cfhg-site' ); ?></h1>
		<p><?php esc_html_e( 'Use this when the year’s dates are agreed. You’ll see the dates before anything is created. Each event can still be changed or cancelled on its own afterwards.', 'cfhg-site' ); ?></p>
		<style>
			.cfhg-series th{width:200px}
			.cfhg-series fieldset label{margin-right:14px;display:inline-block}
			.cfhg-series-months label{display:inline-block;width:120px}
			.cfhg-series-preview{margin:24px 0;padding:16px 20px;background:#fff;border:1px solid #c3c4c7;border-left:4px solid #2271b1}
			.cfhg-series-preview li{margin:4px 0}
			.cfhg-series-preview .is-taken{color:#646970}
		</style>
		<form method="post">
			<?php wp_nonce_field( 'cfhg_series' ); ?>
			<table class="form-table cfhg-series" role="presentation">
				<tr><th scope="row"><label for="cfhg_title"><?php esc_html_e( 'Event name', 'cfhg-site' ); ?></label></th><td><input type="text" class="regular-text" id="cfhg_title" name="cfhg[title]" value="<?php echo esc_attr( $s['title'] ); ?>"><p class="description"><?php esc_html_e( 'Each event gets this name, e.g. Monthly meeting, or Jamaica sub-group meeting.', 'cfhg-site' ); ?></p></td></tr>
				<tr><th scope="row"><label for="cfhggroup"><?php esc_html_e( 'Group', 'cfhg-site' ); ?></label></th><td><?php cfhg_site_term_select( 'cfhg_event_group', 'cfhg[group]', $s['group'], '', 'main' ); ?></td></tr>
				<tr><th scope="row"><label for="cfhgtype"><?php esc_html_e( 'Type', 'cfhg-site' ); ?></label></th><td><?php cfhg_site_term_select( 'cfhg_event_type', 'cfhg[type]', $s['type'], '', 'meeting' ); ?></td></tr>
				<tr><th scope="row"><label for="cfhgisland"><?php esc_html_e( 'Island', 'cfhg-site' ); ?></label></th><td><?php cfhg_site_term_select( 'cfhg_island', 'cfhg[island]', $s['island'], __( 'None (the whole group)', 'cfhg-site' ) ); ?><p class="description"><?php esc_html_e( 'For a sub-group, pick its island so the events also show on that island’s page.', 'cfhg-site' ); ?></p></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Dates', 'cfhg-site' ); ?></th><td>
					<fieldset>
						<label><input type="radio" name="cfhg[pattern]" value="nth" <?php checked( 'nth', $s['pattern'] ); ?>> <?php esc_html_e( 'A pattern', 'cfhg-site' ); ?></label>
						<label><input type="radio" name="cfhg[pattern]" value="list" <?php checked( 'list', $s['pattern'] ); ?>> <?php esc_html_e( 'A list of dates', 'cfhg-site' ); ?></label>
					</fieldset>
					<p>
						<select name="cfhg[nth]" aria-label="<?php esc_attr_e( 'Which week', 'cfhg-site' ); ?>"><?php foreach ( $ordinal as $k => $label ) { printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $s['nth'], $k, false ), esc_html( $label ) ); } ?></select>
						<select name="cfhg[weekday]" aria-label="<?php esc_attr_e( 'Day', 'cfhg-site' ); ?>"><?php foreach ( $days as $k => $label ) { printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $s['weekday'], $k, false ), esc_html( $label ) ); } ?></select>
						<?php esc_html_e( 'of each month, from', 'cfhg-site' ); ?>
						<input type="month" name="cfhg[from]" value="<?php echo esc_attr( $s['from'] ); ?>" aria-label="<?php esc_attr_e( 'First month', 'cfhg-site' ); ?>">
						<?php esc_html_e( 'to', 'cfhg-site' ); ?>
						<input type="month" name="cfhg[to]" value="<?php echo esc_attr( $s['to'] ); ?>" aria-label="<?php esc_attr_e( 'Last month', 'cfhg-site' ); ?>">
					</p>
					<p class="cfhg-series-months"><strong><?php esc_html_e( 'Skip these months:', 'cfhg-site' ); ?></strong><br>
						<?php foreach ( $months as $num => $name ) { printf( '<label><input type="checkbox" name="cfhg[skip][]" value="%1$d"%2$s> %3$s</label>', (int) $num, checked( in_array( $num, $s['skip'], true ), true, false ), esc_html( $name ) ); } ?>
					</p>
					<p><label for="cfhg_dates"><strong><?php esc_html_e( 'Or a list of dates, one per line:', 'cfhg-site' ); ?></strong></label><br>
						<textarea id="cfhg_dates" name="cfhg[dates]" rows="6" cols="40" placeholder="<?php esc_attr_e( "Sunday 18 January 2026\n22 February 2026\n22/03/2026", 'cfhg-site' ); ?>"><?php echo esc_textarea( $s['dates'] ); ?></textarea>
					</p>
				</td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Time', 'cfhg-site' ); ?></th><td><input type="time" name="cfhg[start]" value="<?php echo esc_attr( $s['start'] ); ?>" aria-label="<?php esc_attr_e( 'Starts', 'cfhg-site' ); ?>"> <?php esc_html_e( 'to', 'cfhg-site' ); ?> <input type="time" name="cfhg[end]" value="<?php echo esc_attr( $s['end'] ); ?>" aria-label="<?php esc_attr_e( 'Finishes', 'cfhg-site' ); ?>"></td></tr>
				<tr><th scope="row"><label for="cfhg_attend"><?php esc_html_e( 'Where', 'cfhg-site' ); ?></label></th><td>
					<select id="cfhg_attend" name="cfhg[attend]"><?php foreach ( cfhg_site_attend_options() as $k => $label ) { printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $s['attend'], $k, false ), esc_html( $label ) ); } ?></select>
					<p><input type="text" class="regular-text" name="cfhg[venue]" value="<?php echo esc_attr( $s['venue'] ); ?>" placeholder="<?php echo esc_attr( cfhg_site_settings()['venue'] ); ?>" aria-label="<?php esc_attr_e( 'Venue', 'cfhg-site' ); ?>"> <span class="description"><?php esc_html_e( 'Leave blank for the usual venue.', 'cfhg-site' ); ?></span></p>
				</td></tr>
			</table>
			<?php submit_button( __( 'Show me the dates', 'cfhg-site' ), 'secondary', 'cfhg_series_preview', false ); ?>

			<?php if ( $posted ) : ?>
				<?php $result = cfhg_site_series_dates( $s ); ?>
				<div class="cfhg-series-preview">
					<?php if ( $result['unread'] ) : ?>
						<p><strong><?php esc_html_e( 'These lines couldn’t be read as dates:', 'cfhg-site' ); ?></strong> <?php echo esc_html( implode( '; ', $result['unread'] ) ); ?></p>
					<?php endif; ?>
					<?php if ( $result['mismatch'] ) : ?>
						<p><strong><?php esc_html_e( 'Check these: the day of the week doesn’t match the date, so the year may be wrong.', 'cfhg-site' ); ?></strong> <?php echo esc_html( implode( '; ', $result['mismatch'] ) ); ?></p>
					<?php endif; ?>
					<?php if ( ! $result['dates'] ) : ?>
						<p><?php esc_html_e( 'No dates match. Check the months and the pattern.', 'cfhg-site' ); ?></p>
					<?php else : ?>
						<p><strong>
							<?php
							/* translators: 1: event name, 2: time. */
							echo esc_html( sprintf( __( '“%1$s” %2$s on:', 'cfhg-site' ), $s['title'], cfhg_site_time_range( $s['start'], $s['end'] ) ) );
							?>
						</strong></p>
						<ul>
							<?php foreach ( $result['dates'] as $date ) : ?>
								<?php $taken = cfhg_site_event_exists( $date, $s['group'] ); ?>
								<li class="<?php echo $taken ? 'is-taken' : ''; ?>"><label>
									<input type="checkbox" name="cfhg_dates[]" value="<?php echo esc_attr( $date ); ?>" <?php checked( ! $taken ); ?> <?php disabled( $taken ); ?>>
									<?php echo esc_html( wp_date( 'l j F Y', strtotime( $date . ' 12:00' ) ) ); ?>
									<?php if ( $taken ) : ?>
										<em><?php esc_html_e( '(already in the calendar)', 'cfhg-site' ); ?></em>
									<?php endif; ?>
								</label></li>
							<?php endforeach; ?>
						</ul>
						<?php submit_button( __( 'Create these events', 'cfhg-site' ), 'primary', 'cfhg_series_create', false ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</form>
	</div>
	<?php
}
