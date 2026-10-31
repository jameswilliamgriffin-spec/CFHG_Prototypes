<?php
/**
 * Islands index.
 *
 * @package caribbean-family-history-c
 */

get_header();

cfhg_c_page_header(
	__( 'Islands', 'caribbean-family-history-c' ),
	cfhg_c_content( 'home.islands.eyebrow', '' ),
	cfhg_c_content( 'home.islands.lead', '' )
);
?>
<div class="cfhg-c-section cfhg-c-section--tight">
	<div class="cfhg-c-container">
		<ul class="cfhg-c-island-grid">
			<?php foreach ( cfhg_c_get_islands() as $cfhg_c_island ) : ?>
				<li><?php get_template_part( 'template-parts/components/island-card', null, $cfhg_c_island ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
get_footer();
