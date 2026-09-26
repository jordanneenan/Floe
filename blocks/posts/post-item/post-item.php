<?php
/**
 * Post card (child of Posts, manual entry): the same card as a real post, with
 * a label, date, title, text, image and link typed in by the editor.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$link = (array) $attributes['link'];

echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'card',
	array(
		'variant'       => 'post',
		'title'         => $attributes['title'],
		'text'          => $attributes['text'],
		'url'           => $link['url'] ?? '',
		'new_tab'       => ! empty( $link['newTab'] ),
		'media'         => (array) $attributes['media'],
		'category'      => wp_strip_all_tags( (string) $attributes['label'] ),
		'date'          => wp_strip_all_tags( (string) $attributes['date'] ),
		'heading_level' => min( 4, (int) ( $block->context['floe/postsHeadingLevel'] ?? 2 ) + 1 ),
	)
);
