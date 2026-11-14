<?php
/**
 * Template Name: CFHG What's On
 * Template Post Type: page
 *
 * What the group is doing next and what it has done: upcoming events, a
 * link to subscribe to the calendar, then past events with their write-ups.
 * The events come from the CFHG Site plugin (Events in the admin menu). Any
 * words typed into the page itself appear above the list.
 *
 * In Elementor, build the same with Shortcode widgets:
 * [cfhg_events when="upcoming" limit="20"], [cfhg_calendar_links] and
 * [cfhg_events when="past" limit="12"].
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="cfhg-main cfhg-whats-on">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<header class="cfhg-page-hero">
			<div class="cfhg-container">
				<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
				<p class="cfhg-lead cfhg-page-hero__lead"><?php echo esc_html( has_excerpt() ? get_the_excerpt() : __( 'Meetings, talks and exhibitions: what the group is doing next, and what we’ve been up to.', 'cfhg' ) ); ?></p>
			</div>
		</header>

		<?php if ( '' !== trim( get_the_content() ) ) : ?>
			<div class="cfhg-container"><div class="cfhg-prose"><?php the_content(); ?></div></div>
		<?php endif; ?>
	<?php endwhile; ?>

	<?php if ( shortcode_exists( 'cfhg_events' ) ) : ?>
		<section class="cfhg-section cfhg-whats-on__upcoming" aria-labelledby="cfhg-whats-on-next">
			<div class="cfhg-container">
				<h2 class="cfhg-h2" id="cfhg-whats-on-next"><?php esc_html_e( 'Coming up', 'cfhg' ); ?></h2>
				<?php echo do_shortcode( '[cfhg_events when="upcoming" limit="20"]' ); ?>
				<?php echo do_shortcode( '[cfhg_calendar_links]' ); ?>
			</div>
		</section>
		<section class="cfhg-section cfhg-whats-on__past" aria-labelledby="cfhg-whats-on-past">
			<div class="cfhg-container">
				<h2 class="cfhg-h2" id="cfhg-whats-on-past"><?php esc_html_e( 'Recent events', 'cfhg' ); ?></h2>
				<?php echo do_shortcode( '[cfhg_events when="past" limit="12"]' ); ?>
			</div>
		</section>
	<?php else : ?>
		<div class="cfhg-container"><p class="cfhg-lead"><?php esc_html_e( 'Turn on the CFHG Site plugin to list events here.', 'cfhg' ); ?></p></div>
	<?php endif; ?>
</main>
<?php
get_footer();
