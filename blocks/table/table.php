<?php
/**
 * Table: section header plus a core Table block with Floe styling. A cell
 * that contains only ✓ shows the check icon ("Included"); only — or –
 * shows the dash icon ("Not included").
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( false === strpos( $content, '<table' ) ) {
	return;
}

$check = Floe\icon( 'check', array( 'class' => 'table__check' ) ) . '<span class="screen-reader-text">' . esc_html__( 'Included', 'floe' ) . '</span>';
$dash  = Floe\icon( 'dash', array( 'class' => 'table__dash' ) ) . '<span class="screen-reader-text">' . esc_html__( 'Not included', 'floe' ) . '</span>';

$content = preg_replace_callback(
	'#(<td[^>]*>)\s*(✓|✔️?|—|–|-)\s*(</td>)#u',
	static fn( array $match ): string => $match[1] . ( in_array( $match[2], array( '—', '–', '-' ), true ) ? $dash : $check ) . $match[3],
	$content
);

$highlight = max( 0, min( 6, (int) $attributes['highlight'] ) );
$badge     = trim( wp_strip_all_tags( (string) $attributes['badge'] ) );
$frame     = array( 'class="table__frame' . ( $attributes['emphasiseLastRow'] ? ' table--emphasise-last' : '' ) . '"' );
if ( $highlight ) {
	$frame[] = 'data-highlight="' . $highlight . '"';
	if ( '' !== $badge ) {
		$frame[] = 'style="' . esc_attr( '--badge:"' . str_replace( array( '"', '\\' ), '', $badge ) . '"' ) . '"';
	}
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="table__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'intro'         => $attributes['intro'],
			)
		);
		?>
		<div <?php echo implode( ' ', $frame ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
