<?php
/**
 * Homepage: stories, as a carousel with a counter on each slide.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-stories" aria-labelledby="cfhg-stories-title">
	<div class="cfhg-container">
		<div class="cfhg-stories__head" data-reveal>
			<div>
				<p class="cfhg-label"><?php echo esc_html( cfhg_mod( 'stories_kicker' ) ); ?></p>
				<h2 class="cfhg-h2" id="cfhg-stories-title"><?php echo wp_kses( cfhg_mod( 'stories_heading' ), cfhg_inline_kses() ); ?></h2>
			</div>
			<?php echo cfhg_button( cfhg_mod( 'stories_link_label' ), cfhg_mod( 'stories_link_url' ), 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php echo cfhg_component_stories( 3 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</section>
