<?php
/**
 * Article: WordPress's own blocks in an 860px reading column, plus an
 * optional button.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$action = component( 'button', Floe\Components\button_args_from_link( $attributes['action'] ) );
if ( '' === trim( wp_strip_all_tags( $content, true ) ) && false === strpos( $content, '<img' ) && ! $action ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="article__inner">
		<div class="article__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
		</div>
		<?php if ( $action ) : ?>
			<div class="article__action"><?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</section>
