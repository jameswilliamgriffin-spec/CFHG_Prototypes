<?php
/**
 * Post list for the news archive, blog index and search: the first post on
 * page one leads with a large picture, the rest follow as ruled rows.
 *
 * @package caribbean-family-history-c
 */

$cfhg_c_lead_first = ! is_paged() && ! is_search();
?>
<div class="cfhg-c-section cfhg-c-section--tight">
	<div class="cfhg-c-container">
		<?php if ( have_posts() ) : ?>
			<div class="cfhg-c-archive">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/components/post-card',
						null,
						array(
							'variant' => $cfhg_c_lead_first ? 'lead' : 'row',
							'tag'     => 'h2',
						)
					);
					$cfhg_c_lead_first = false;
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'cfhg-c-pagination',
					'prev_text' => '<span aria-hidden="true">&larr;</span> ' . __( 'Newer', 'caribbean-family-history-c' ),
					'next_text' => __( 'Older', 'caribbean-family-history-c' ) . ' <span aria-hidden="true">&rarr;</span>',
				)
			);
			?>
		<?php else : ?>
			<div class="cfhg-c-prose">
				<p><?php esc_html_e( 'Nothing here yet.', 'caribbean-family-history-c' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
