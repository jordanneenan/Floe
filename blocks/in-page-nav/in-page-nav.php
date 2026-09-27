<?php
/**
 * In-page navigation: a sticky bar of links to the page's sections. Items
 * come from the Floe blocks that have an HTML anchor (label: the block's
 * eyebrow, else its heading), unless entered by hand, including those inside
 * other blocks (a Background). Blocks the viewer won't see are left out.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

// The page's anchored sections in order, and the anchors of those this viewer
// won't see: hidden with WordPress's own Hide option, or by a module (such as
// Hidden from visitors), or inside a block that is.
$sections = array();
$hidden   = array();
$walk     = static function ( array $blocks, bool $gone ) use ( &$walk, &$sections, &$hidden ): void {
	foreach ( $blocks as $section ) {
		if ( ! str_starts_with( (string) $section['blockName'], 'floe/' ) ) {
			continue;
		}
		/**
		 * Filters whether a block will show to the current viewer.
		 *
		 * @param bool  $visible Whether the block shows. Default true.
		 * @param array $section The parsed block.
		 */
		$away   = $gone || false === ( $section['attrs']['metadata']['blockVisibility'] ?? null ) || ! apply_filters( 'floe_block_visible', true, $section );
		$anchor = sanitize_title( (string) ( $section['attrs']['anchor'] ?? '' ) );
		if ( $anchor && $away ) {
			$hidden[ $anchor ] = true;
		} elseif ( $anchor ) {
			$label = trim( wp_strip_all_tags( (string) ( $section['attrs']['eyebrow'] ?? '' ) ) );
			if ( '' === $label ) {
				$label = trim( wp_strip_all_tags( (string) ( $section['attrs']['heading'] ?? '' ) ) );
			}
			$sections[] = array( $anchor, '' !== $label ? $label : ucwords( str_replace( '-', ' ', $anchor ) ) );
		}
		$walk( (array) ( $section['innerBlocks'] ?? array() ), $away );
	}
};
$walk( get_post() ? parse_blocks( get_post()->post_content ) : array(), false );

// Links set by hand, leaving out any to a hidden section; else automatic.
$items  = array();
$manual = false;
foreach ( (array) $attributes['items'] as $item ) {
	$anchor = sanitize_title( (string) ( $item['anchor'] ?? '' ) );
	$label  = trim( wp_strip_all_tags( (string) ( $item['label'] ?? '' ) ) );
	if ( $anchor && $label ) {
		$manual = true;
		if ( ! isset( $hidden[ $anchor ] ) ) {
			$items[] = array( $anchor, $label );
		}
	}
}
if ( ! $manual ) {
	$items = $sections;
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
