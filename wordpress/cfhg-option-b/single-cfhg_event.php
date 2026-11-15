<?php
/**
 * One event (from the CFHG Site plugin): when and where, a short
 * description, "Add to my calendar", and afterwards a write-up.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$cfhg_e = function_exists( 'cfhg_site_event' ) ? cfhg_site_event( get_the_ID() ) : null;
	if ( ! $cfhg_e ) {
		get_template_part( 'template-parts/content/single' );
		continue;
	}
	$cfhg_whats_on = get_page_by_path( 'whats-on' );
	$cfhg_mail     = cfhg_mailto();
	$cfhg_sep      = ', ';
	$cfhg_meta     = array( '<time datetime="' . esc_attr( $cfhg_e['date_iso'] ) . '">' . esc_html( $cfhg_e['date_text'] ) . '</time>' );
	if ( $cfhg_e['time_text'] ) {
		$cfhg_meta[] = esc_html( $cfhg_e['time_text'] );
	}
	/* Name the group only when it isn't the main one. */
	if ( $cfhg_e['group'] && function_exists( 'cfhg_site_settings' ) && cfhg_site_settings()['next_group'] !== $cfhg_e['group']->slug ) {
		$cfhg_meta[] = esc_html( $cfhg_e['group']->name );
	}
	?>
<main id="main" class="cfhg-main cfhg-event-page<?php echo $cfhg_e['cancelled'] ? ' is-cancelled' : ''; ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cfhg-entry' ); ?>>
		<header class="cfhg-page-hero">
			<div class="cfhg-container">
				<?php the_title( '<h1 class="cfhg-h1 cfhg-page-hero__title">', '</h1>' ); ?>
				<p class="cfhg-page-hero__meta"><?php echo implode( $cfhg_sep, $cfhg_meta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></p>
			</div>
		</header>

		<div class="cfhg-container cfhg-event-page__grid">
			<div class="cfhg-event-page__main">
				<?php if ( $cfhg_e['cancelled'] ) : ?>
					<p class="cfhg-event-page__cancelled"><strong><?php esc_html_e( 'This event has been cancelled.', 'cfhg' ); ?></strong> <?php esc_html_e( 'See What’s On for the next one.', 'cfhg' ); ?></p>
				<?php endif; ?>
				<?php if ( $cfhg_e['summary'] ) : ?>
					<p class="cfhg-lead"><?php echo esc_html( $cfhg_e['summary'] ); ?></p>
				<?php endif; ?>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="cfhg-entry__media cfhg-rounded"><?php the_post_thumbnail( 'cfhg-wide', array( 'sizes' => '(min-width: 1024px) 60vw, 92vw' ) ); ?></figure>
				<?php endif; ?>
				<?php if ( $cfhg_e['has_writeup'] ) : ?>
					<div class="cfhg-prose">
						<?php if ( $cfhg_e['is_past'] ) : ?>
							<h2><?php esc_html_e( 'What happened', 'cfhg' ); ?></h2>
						<?php endif; ?>
						<?php the_content(); ?>
					</div>
				<?php elseif ( ! $cfhg_e['summary'] && ! $cfhg_e['is_past'] ) : ?>
					<p class="cfhg-lead"><?php esc_html_e( 'Bring a name, a photograph or a question. Everyone is welcome, and your first visit is free.', 'cfhg' ); ?></p>
				<?php endif; ?>
			</div>

			<aside class="cfhg-card cfhg-event-page__card" aria-labelledby="cfhg-event-details">
				<h2 class="cfhg-h3" id="cfhg-event-details"><?php esc_html_e( 'When and where', 'cfhg' ); ?></h2>
				<dl class="cfhg-facts">
					<div class="cfhg-facts__row"><dt><?php esc_html_e( 'When', 'cfhg' ); ?></dt><dd><?php echo esc_html( $cfhg_e['date_text'] . ( $cfhg_e['time_text'] ? ', ' . $cfhg_e['time_text'] : '' ) ); ?></dd></div>
					<?php if ( 'online' !== $cfhg_e['attend'] && $cfhg_e['venue'] ) : ?>
						<div class="cfhg-facts__row"><dt><?php esc_html_e( 'Where', 'cfhg' ); ?></dt><dd><?php echo esc_html( $cfhg_e['venue'] ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $cfhg_e['online'] ) : ?>
						<div class="cfhg-facts__row"><dt><?php esc_html_e( 'Online', 'cfhg' ); ?></dt><dd><?php echo esc_html( $cfhg_e['online'] ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $cfhg_e['islands'] ) : ?>
						<div class="cfhg-facts__row"><dt><?php esc_html_e( 'Island', 'cfhg' ); ?></dt><dd><?php echo esc_html( implode( ', ', wp_list_pluck( $cfhg_e['islands'], 'name' ) ) ); ?></dd></div>
					<?php endif; ?>
				</dl>
				<div class="cfhg-actions">
					<?php
					if ( ! $cfhg_e['is_past'] && ! $cfhg_e['cancelled'] ) {
						echo cfhg_button( __( 'Add to my calendar', 'cfhg' ), cfhg_site_ics_url( $cfhg_e['id'] ), 'mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						if ( $cfhg_e['online'] && $cfhg_mail ) {
							echo cfhg_button( __( 'Ask for the Zoom link', 'cfhg' ), $cfhg_mail, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
					if ( $cfhg_whats_on ) {
						echo cfhg_button( __( 'All events', 'cfhg' ), get_permalink( $cfhg_whats_on ), 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</aside>
		</div>
	</article>
</main>
	<?php
endwhile;

get_footer();
