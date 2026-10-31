<?php
/**
 * Homepage: the group in numbers, on scattered coloured notes.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfhg-section cfhg-numbers" aria-labelledby="cfhg-numbers-title">
	<div class="cfhg-container">
		<h2 class="cfhg-h2 cfhg-numbers__title" id="cfhg-numbers-title" data-reveal><?php echo esc_html( cfhg_mod( 'numbers_title' ) ); ?></h2>
		<?php echo cfhg_component_numbers(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
