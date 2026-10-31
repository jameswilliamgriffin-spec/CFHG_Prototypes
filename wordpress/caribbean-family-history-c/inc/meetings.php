<?php
/**
 * Meeting dates.
 *
 * The group meets on the last Saturday of the month except May, August and
 * December, so dates are worked out rather than typed in. One date can be
 * moved from the Customizer.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upcoming meeting dates, today included.
 *
 * @param int $count How many.
 * @return DateTimeImmutable[]
 */
function cfhg_c_meeting_dates( $count = 3 ) {
	$tz    = wp_timezone();
	$today = new DateTimeImmutable( 'today', $tz );

	/**
	 * Months (1 to 12) with no regular meeting.
	 *
	 * @param int[] $months Months.
	 */
	$skip = array_map( 'intval', (array) apply_filters( 'cfhg_c_meeting_skip_months', array( 5, 8, 12 ) ) );

	$override = cfhg_c_sanitize_date( cfhg_c_mod( 'cfhg_c_meeting_override' ) );
	$moved    = $override ? DateTimeImmutable::createFromFormat( '!Y-m-d', $override, $tz ) : false;
	if ( $moved && $moved < $today ) {
		$moved = false;
	}

	$dates  = array();
	$cursor = $today->modify( 'first day of this month' );

	for ( $i = 0; $i < 24 && count( $dates ) < $count + 1; $i++ ) {
		$month = (int) $cursor->format( 'n' );

		if ( $moved && $moved->format( 'Y-m' ) === $cursor->format( 'Y-m' ) ) {
			$dates[] = $moved;
		} elseif ( ! in_array( $month, $skip, true ) ) {
			$last = $cursor->modify( 'last saturday of this month' );
			if ( $last >= $today ) {
				$dates[] = $last;
			}
		}

		$cursor = $cursor->modify( 'first day of next month' );
	}

	usort(
		$dates,
		function ( $a, $b ) {
			return $a <=> $b;
		}
	);

	return array_slice( $dates, 0, max( 1, (int) $count ) );
}

/**
 * The next meeting, formatted.
 *
 * @return array{date:string,iso:string,today:bool,time:string,where:string}
 */
function cfhg_c_next_meeting() {
	$dates = cfhg_c_meeting_dates( 1 );
	$next  = $dates[0];
	$today = new DateTimeImmutable( 'today', wp_timezone() );

	return array(
		'date'  => wp_date( 'l j F Y', $next->getTimestamp() ),
		'iso'   => $next->format( 'Y-m-d' ),
		'today' => $next->format( 'Y-m-d' ) === $today->format( 'Y-m-d' ),
		'time'  => cfhg_c_mod( 'cfhg_c_meeting_time' ),
		'where' => cfhg_c_mod( 'cfhg_c_meeting_where' ),
	);
}
