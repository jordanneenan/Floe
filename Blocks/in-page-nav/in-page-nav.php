<?php
/**
 * In-page navigation: a sticky bar of links to the page's sections. Items
 * come from the top-level sections that have an HTML anchor (label: the
 * section's eyebrow, else its heading), unless entered by hand.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$items = array();
foreach ( (array) $attributes['items'] as $item ) {
	$anchor = sanitize_title( (string) ( $item['anchor'] ?? '' ) );
	$label  = trim( wp_strip_all_tags( (string) ( $item['label'] ?? '' ) ) );
	if ( $anchor && $label ) {
		$items[] = array( $anchor, $label );
	}
}

if ( ! $items && get_post() ) {
	foreach ( parse_blocks( get_post()->post_content ) as $section ) {
		$anchor = sanitize_title( (string) ( $section['attrs']['anchor'] ?? '' ) );
		if ( ! $anchor || ! str_starts_with( (string) $section['blockName'], 'floe/' ) ) {
			continue;
		}
		$label = trim( wp_strip_all_tags( (string) ( $section['attrs']['eyebrow'] ?? '' ) ) );
		if ( '' === $label ) {
			$label = trim( wp_strip_all_tags( (string) ( $section['attrs']['heading'] ?? '' ) ) );
		}
		$items[] = array( $anchor, '' !== $label ? $label : ucwords( str_replace( '-', ' ', $anchor ) ) );
	}
}

if ( ! $items ) {
	return;
}

$label  = trim( wp_strip_all_tags( (string) $attributes['label'] ) );
$action = Floe\component( 'button', Floe\Components\button_args_from_link( $attributes['action'], array( 'arrow' => false ) ) );
?>
<nav <?php echo Floe\block_attributes( $block, array( 'aria-label' => '' !== $label ? $label : __( 'On this page', 'floe' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="in-page-nav__inner">
		<div class="in-page-nav__links">
			<?php if ( '' !== $label ) : ?>
				<p class="in-page-nav__label" aria-hidden="true"><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<ul class="in-page-nav__list">
				<?php foreach ( $items as $item ) : ?>
					<li><a class="in-page-nav__link" href="#<?php echo esc_attr( $item[0] ); ?>"><?php echo esc_html( $item[1] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php if ( $action ) : ?>
			<div class="in-page-nav__action"><?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</nav>
