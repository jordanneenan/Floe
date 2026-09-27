<?php
/**
 * Card (child of Cards). Feature, Icon or Media style comes from the parent.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$style = in_array( $block->context['floe/cardStyle'] ?? '', array( 'icon', 'media' ), true ) ? $block->context['floe/cardStyle'] : 'feature';
$link  = (array) $attributes['link'];

echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'card',
	array(
		'variant'       => $style,
		'number'        => 'auto',
		'title'         => $attributes['title'],
		'text'          => $attributes['text'],
		'url'           => $link['url'] ?? '',
		'new_tab'       => ! empty( $link['newTab'] ),
		'link_label'    => ! empty( $link['label'] ) ? $link['label'] : __( 'Learn more', 'floe' ),
		'media'         => (array) $attributes['media'],
		'heading_level' => min( 4, (int) ( $block->context['floe/headingLevel'] ?? 2 ) + 1 ),
	)
);
