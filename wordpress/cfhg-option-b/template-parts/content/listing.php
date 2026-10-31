<?php
/**
 * Lists of posts: the blog, archives and search results.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

if ( is_search() ) {
	/* translators: %s: search terms. */
	$cfhg_title = sprintf( __( 'Results for “%s”', 'cfhg' ), get_search_query() );
	$cfhg_intro = '';
} elseif ( is_home() && ! is_front_page() ) {
	$cfhg_title = single_post_title( '', false );
	$cfhg_intro = '';
} elseif ( is_archive() ) {
	$cfhg_title = wp_strip_all_tags( get_the_archive_title() );
	$cfhg_intro = get_the_archive_description();
} else {
	$cfhg_title = __( 'Latest', 'cfhg' );
	$cfhg_intro = '';
}
?>
<main id="main" class="cfhg-main">
	<header class="cfhg-page-hero">
		<div class="cfhg-container">
			<h1 class="cfhg-h1 cfhg-page-hero__title"><?php echo esc_html( $cfhg_title ); ?></h1>
			<?php if ( $cfhg_intro ) : ?>
				<div class="cfhg-lead cfhg-page-hero__lead"><?php echo wp_kses_post( $cfhg_intro ); ?></div>
			<?php endif; ?>
			<?php if ( is_search() ) : ?>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</header>

	<div class="cfhg-container cfhg-listing">
		<?php if ( have_posts() ) : ?>
			<div class="cfhg-stories__list">
				<?php
				$cfhg_index = 0;
				$cfhg_total = (int) $GLOBALS['wp_query']->post_count;
				while ( have_posts() ) :
					the_post();
					$cfhg_tags = array();
					foreach ( (array) get_the_category() as $cfhg_cat ) {
						$cfhg_tags[] = $cfhg_cat->name;
					}
					if ( 'page' === get_post_type() ) {
						$cfhg_tags = array( __( 'Page', 'cfhg' ) );
					}
					echo cfhg_component_story_slide( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in component.
						array(
							'tags'  => array_slice( $cfhg_tags, 0, 2 ),
							'meta'  => 'post' === get_post_type() ? get_the_date() : '',
							'title' => get_the_title(),
							'text'  => wp_strip_all_tags( get_the_excerpt() ),
							'url'   => get_permalink(),
							'image' => has_post_thumbnail() ? get_the_post_thumbnail( null, 'cfhg-card', array( 'sizes' => '(min-width: 1024px) 34vw, 90vw' ) ) : '',
						),
						$cfhg_index++,
						$cfhg_total
					);
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'cfhg-pagination',
					'prev_text' => __( 'Newer', 'cfhg' ),
					'next_text' => __( 'Older', 'cfhg' ),
				)
			);
			?>
		<?php else : ?>
			<div class="cfhg-prose cfhg-empty">
				<p class="cfhg-lead"><?php esc_html_e( 'Nothing here yet.', 'cfhg' ); ?></p>
				<p><?php esc_html_e( 'Try a different search, or come and ask us at a meeting.', 'cfhg' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
