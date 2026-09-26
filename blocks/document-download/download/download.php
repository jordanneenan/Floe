<?php
/**
 * Download (child of Document Download): one file row.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$row = Floe\component(
	'file-row',
	array(
		'id'    => $attributes['file']['id'] ?? 0,
		'title' => $attributes['title'],
	)
);
if ( $row ) {
	echo '<li class="download">' . $row . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
