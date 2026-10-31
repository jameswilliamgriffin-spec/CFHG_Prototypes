<?php
/**
 * Homepage: who we are. A large statement with a small photo set into the text.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

$cfhg_tile = '<span class="cfhg-inline-tile">' . cfhg_setting_image(
	'intro_tile_image',
	array(
		'sizes' => '120px',
		'alt'   => '',
	)
) . '</span>';

$cfhg_statement = str_replace(
	'{tile}',
	$cfhg_tile,
	wp_kses( cfhg_mod( 'intro_heading' ), cfhg_inline_kses() )
);
?>
<section class="cfhg-section cfhg-intro" id="cfhg-intro">
	<div class="cfhg-container">
		<p class="cfhg-kicker" data-reveal><?php echo esc_html( cfhg_mod( 'intro_kicker' ) ); ?></p>
		<h2 class="cfhg-h1 cfhg-statement" data-reveal><?php echo $cfhg_statement; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses above, image escaped in helper. ?></h2>
		<div class="cfhg-intro__aside" data-reveal>
			<p><?php echo esc_html( cfhg_mod( 'intro_text' ) ); ?></p>
			<?php echo cfhg_button( cfhg_mod( 'intro_link_label' ), cfhg_mod( 'intro_link_url' ), 'disc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
