<?php
/**
 * Navigation component: a registered menu location, rendered with
 * wp_nav_menu(). Prints nothing when no menu is assigned (no fallback that
 * lists every page).
 *
 *   Floe\component( 'navigation', array(
 *       'location' => 'primary',
 *       'label'    => 'Main',      // aria-label for the <nav>
 *       'heading'  => false,       // show the menu's name as a heading (footer columns)
 *       'depth'    => 2,
 *       'as'       => 'menu',      // 'button': render one item as a Button (header/footer action)
 *       'item'     => 0,           // which item when 'as' is 'button' (0 = first)
 *       'style'    => 'primary',   // button style when 'as' is 'button'
 *       'class'    => '',
 *   ) );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

/** An item of a menu location (0 = the first) as { label, url, newTab }, or null. */
function navigation_item( string $location, int $index = 0 ): ?array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return null;
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items || ! isset( array_values( $items )[ $index ] ) ) {
		return null;
	}
	$item = array_values( $items )[ $index ];
	return array(
		'label'  => $item->title,
		'url'    => $item->url,
		'newTab' => '_blank' === $item->target,
	);
}

function navigation( array $args ): string {
	$location = (string) ( $args['location'] ?? 'primary' );
	if ( ! has_nav_menu( $location ) ) {
		return '';
	}

	if ( 'button' === ( $args['as'] ?? 'menu' ) ) {
		$item = navigation_item( $location, (int) ( $args['item'] ?? 0 ) );
		return $item ? \Floe\component(
			'button',
			button_args_from_link(
				$item,
				array(
					'style' => $args['style'] ?? 'primary',
					'arrow' => $args['arrow'] ?? false,
					'class' => $args['class'] ?? '',
				)
			)
		) : '';
	}

	$menu = wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => 'navigation__list',
			'depth'          => (int) ( $args['depth'] ?? 2 ),
			'fallback_cb'    => false,
			'echo'           => false,
		)
	);
	if ( ! $menu ) {
		return '';
	}

	$heading = '';
	$label   = (string) ( $args['label'] ?? '' );
	if ( ! empty( $args['heading'] ) ) {
		$name    = wp_get_nav_menu_name( $location );
		$id      = 'nav-heading-' . sanitize_html_class( $location );
		$heading = '<p class="navigation__heading" id="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</p>';
		$aria    = 'aria-labelledby="' . esc_attr( $id ) . '"';
	} else {
		$aria = 'aria-label="' . esc_attr( '' !== $label ? $label : wp_get_nav_menu_name( $location ) ) . '"';
	}

	return sprintf(
		'<nav class="%1$s" %2$s>%3$s%4$s</nav>',
		esc_attr( trim( 'navigation navigation--' . $location . ' ' . ( $args['class'] ?? '' ) ) ),
		$aria,
		$heading,
		$menu
	);
}
