<?php
/**
 * Template Name: CFHG What's On
 * Template Post Type: page
 *
 * What the group is doing next and what it has done: upcoming events, a
 * link to subscribe to the calendar, then past events with their write-ups.
 * The events come from the CFHG Site plugin (Events in the admin menu). The
 * page's excerpt is the opening line and anything typed into the page
 * replaces the short welcome beside the photo; a featured image replaces the
 * photo of a talk at the library.
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
		<?php
		the_post();
		$cfhg_mail = cfhg_mailto();
		/* A featured image replaces the theme's photo of a meeting. */
		$cfhg_image = has_post_thumbnail()
			? get_the_post_thumbnail( null, 'large', array( 'sizes' => '(min-width: 1024px) 42vw, 92vw', 'loading' => 'eager' ) )
			: cfhg_theme_image( 'a-talk-at-the-library', array( 'sizes' => '(min-width: 1024px) 42vw, 92vw', 'loading' => 'eager' ) );
		?>
		<header class="cfhg-page-hero cfhg-whats-on__hero">
			<div class="cfhg-container cfhg-whats-on__hero-grid">
				<div class="cfhg-whats-on__copy">
					<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
					<p class="cfhg-lead cfhg-page-hero__lead"><?php echo esc_html( has_excerpt() ? get_the_excerpt() : __( 'Meetings, talks and exhibitions: what the group is doing next, and what we’ve been up to.', 'cfhg' ) ); ?></p>
					<div class="cfhg-whats-on__intro">
						<?php if ( '' !== trim( get_the_content() ) ) : ?>
							<?php the_content(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Most months we meet on a Saturday afternoon at Solihull Core Library, with members joining on Zoom from across the UK and further afield. Bring a name, a photograph or a question: someone at the table will usually know where to look next.', 'cfhg' ); ?></p>
						<?php endif; ?>
					</div>
					<ul class="cfhg-whats-on__glance">
						<li class="cfhg-whats-on__fact cfhg-whats-on__fact--ticket"><?php echo esc_html( cfhg_mod( 'meeting_heading' ) ); ?></li>
						<li class="cfhg-whats-on__fact cfhg-whats-on__fact--signpost"><?php echo esc_html( cfhg_mod( 'meeting_short_where' ) ); ?></li>
						<li class="cfhg-whats-on__fact cfhg-whats-on__fact--tree"><?php esc_html_e( 'Beginners always welcome', 'cfhg' ); ?></li>
					</ul>
					<div class="cfhg-actions">
						<?php echo cfhg_button( __( 'See what’s coming up', 'cfhg' ), '#cfhg-whats-on-next', 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $cfhg_mail ? cfhg_button( cfhg_mod( 'meeting_secondary_label' ), $cfhg_mail, 'text' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<?php if ( $cfhg_image ) : ?>
					<figure class="cfhg-postcard cfhg-postcard--sea cfhg-whats-on__art"><?php echo $cfhg_image; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
				<?php endif; ?>
			</div>
		</header>
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
