<?php
/**
 * Play control component (Figma 30:98): the YouTube facade button. Its
 * accessible name includes the video title.
 *
 *   Floe\component( 'play-control', array( 'title' => 'How Floe works', 'duration' => '2:14' ) )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function play_control( array $args ): string {
	$title    = trim( (string) ( $args['title'] ?? '' ) );
	$duration = trim( (string) ( $args['duration'] ?? '' ) );
	$label    = '' !== $title
		/* translators: %s: video title. */
		? sprintf( __( 'Play video: %s', 'floe' ), $title )
		: __( 'Play video', 'floe' );

	return sprintf(
		'<button type="button" class="play-control" aria-label="%1$s"><span class="play-control__circle">%2$s</span><span class="play-control__text" aria-hidden="true"><span class="play-control__label">%3$s</span>%4$s</span></button>',
		esc_attr( $label ),
		\Floe\icon( 'play' ),
		esc_html__( 'Play video', 'floe' ),
		'' !== $duration ? '<span class="play-control__duration">' . esc_html( $duration ) . '</span>' : ''
	);
}
