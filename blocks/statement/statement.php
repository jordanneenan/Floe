<?php
/**
 * Statement: one big line in large type, with an optional eyebrow and button.
 * With "Light up as you scroll" on, statement.js dims the words and lights
 * them in reading order as the statement moves up the screen.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$statement = trim( (string) $attributes['statement'] );
if ( '' === trim( wp_strip_all_tags( $statement ) ) ) {
	return;
}
$action  = component( 'button', Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => 'secondary' ) ) );
$wrapper = array();
if ( ! empty( $attributes['lightUp'] ) ) {
	$wrapper['data-light-up'] = '';
}
?>
<section <?php echo Floe\block_attributes( $block, $wrapper ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="statement__inner">
		<?php echo component( 'eyebrow', array( 'text' => $attributes['eyebrow'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="statement__text"><?php echo wp_kses_post( $statement ); ?></p>
		<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
