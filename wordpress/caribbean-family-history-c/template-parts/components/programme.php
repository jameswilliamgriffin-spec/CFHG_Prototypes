<?php
/**
 * Programme list: the next three meetings (worked out from the meeting
 * rule) followed by the dated extras in content.json.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_wo    = (array) cfhg_c_content( 'home.whats_on', array() );
$cfhg_c_where = cfhg_c_mod( 'cfhg_c_meeting_where' );
$cfhg_c_time  = cfhg_c_mod( 'cfhg_c_meeting_time' );
?>
<ol class="cfhg-c-programme">
	<?php foreach ( cfhg_c_meeting_dates( 3 ) as $cfhg_c_date ) : ?>
		<li class="cfhg-c-programme__item">
			<p class="cfhg-c-programme__date">
				<time datetime="<?php echo esc_attr( $cfhg_c_date->format( 'Y-m-d' ) ); ?>">
					<span class="cfhg-c-programme__day"><?php echo esc_html( wp_date( 'j', $cfhg_c_date->getTimestamp() ) ); ?></span>
					<span class="cfhg-c-programme__month"><?php echo esc_html( wp_date( 'M Y', $cfhg_c_date->getTimestamp() ) ); ?></span>
				</time>
			</p>
			<div class="cfhg-c-programme__body">
				<p class="cfhg-c-programme__label"><?php echo esc_html( $cfhg_c_wo['meeting_label'] ); ?></p>
				<h3 class="cfhg-c-programme__title"><?php echo esc_html( $cfhg_c_wo['meeting_title'] ); ?></h3>
				<p class="cfhg-c-programme__where"><?php echo esc_html( wp_date( 'l', $cfhg_c_date->getTimestamp() ) . ( $cfhg_c_time ? ', ' . $cfhg_c_time : '' ) . '. ' . $cfhg_c_where ); ?></p>
			</div>
		</li>
	<?php endforeach; ?>
	<?php foreach ( (array) $cfhg_c_wo['extras'] as $cfhg_c_extra ) : ?>
		<li class="cfhg-c-programme__item cfhg-c-programme__item--tbc">
			<p class="cfhg-c-programme__date"><span class="cfhg-c-programme__month"><?php echo esc_html( $cfhg_c_extra['when'] ); ?></span></p>
			<div class="cfhg-c-programme__body">
				<p class="cfhg-c-programme__label"><?php echo esc_html( $cfhg_c_extra['label'] ); ?></p>
				<h3 class="cfhg-c-programme__title"><?php echo esc_html( $cfhg_c_extra['title'] ); ?></h3>
				<p class="cfhg-c-programme__where"><?php echo esc_html( $cfhg_c_extra['where'] ); ?></p>
			</div>
		</li>
	<?php endforeach; ?>
</ol>
