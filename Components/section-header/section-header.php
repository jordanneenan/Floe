<?php
/**
 * Section header component: eyebrow, heading, optional intro and optional
 * action, so every section's heading group is laid out the same way.
 *
 *   Floe\component( 'section-header', array(
 *       'eyebrow'       => 'Latest posts',
 *       'heading'       => 'Ideas and updates',
 *       'heading_level' => 2,                 // 1–4
 *       'intro'         => 'Optional supporting line.',
 *       'action'        => array( 'label' => 'View all', 'url' => '/news/', 'style' => 'secondary' ),
 *       'aside'         => '',                // extra HTML for the right-hand side (e.g. slider controls)
 *       'layout'        => 'split',           // split: heading left, intro/action right. stacked: one column.
 *   ) );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function section_header( array $args ): string {
	$has_text = static fn( $value ): bool => '' !== trim( wp_strip_all_tags( (string) $value ) );

	$level   = max( 1, min( 4, (int) ( $args['heading_level'] ?? 2 ) ) );
	$layout  = 'stacked' === ( $args['layout'] ?? 'split' ) ? 'stacked' : 'split';
	$eyebrow = \Floe\component( 'eyebrow', array( 'text' => $args['eyebrow'] ?? '' ) );
	$heading = $has_text( $args['heading'] ?? '' )
		? sprintf( '<h%1$d class="section-header__heading">%2$s</h%1$d>', $level, wp_kses_post( $args['heading'] ) )
		: '';
	$intro   = $has_text( $args['intro'] ?? '' )
		? '<p class="section-header__intro">' . wp_kses_post( $args['intro'] ) . '</p>'
		: '';
	$action  = ! empty( $args['action'] ) ? \Floe\component( 'button', (array) $args['action'] ) : '';
	$aside   = (string) ( $args['aside'] ?? '' );

	if ( ! $eyebrow && ! $heading && ! $intro && ! $action && ! $aside ) {
		return '';
	}

	if ( 'stacked' === $layout ) {
		$actions = $action ? '<div class="section-header__action">' . $action . '</div>' : '';
		return '<div class="section-header section-header--stacked">' . $eyebrow . $heading . $intro . $actions . $aside . '</div>';
	}

	$side = $intro . ( $action ? '<div class="section-header__action">' . $action . '</div>' : '' ) . $aside;
	return '<div class="section-header section-header--split"><div class="section-header__main">' . $eyebrow . $heading . '</div>'
		. ( $side ? '<div class="section-header__aside">' . $side . '</div>' : '' ) . '</div>';
}
