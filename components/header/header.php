<?php
/**
 * Header component (Figma 30:2): logo, primary menu and one action. Below
 * 768px the menu and action move into a panel opened by a disclosure button.
 *
 *   echo Floe\component( 'header' );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function header( array $args = array() ): string {
	$nav    = \Floe\component( 'navigation', array( 'location' => 'primary', 'label' => __( 'Main', 'floe' ), 'class' => 'site-header__nav' ) );
	$action = \Floe\component( 'navigation', array( 'location' => 'action', 'as' => 'button', 'class' => 'site-header__action' ) );

	if ( $nav || $action ) {
		wp_enqueue_script( 'floe-header' );
	}

	$toggle = ( $nav || $action ) ? sprintf(
		'<button type="button" class="site-header__toggle" aria-expanded="false" aria-controls="site-header-panel"><span class="screen-reader-text">%1$s</span>%2$s%3$s</button>',
		esc_html__( 'Menu', 'floe' ),
		\Floe\icon( 'menu', array( 'class' => 'site-header__icon-open' ) ),
		\Floe\icon( 'close', array( 'class' => 'site-header__icon-close' ) )
	) : '';

	return sprintf(
		'<header class="site-header surface-base"><div class="site-header__inner">%1$s%2$s<div class="site-header__panel" id="site-header-panel">%3$s%4$s</div></div></header>',
		\Floe\component( 'logo', array( 'class' => 'site-header__logo' ) ),
		$toggle,
		$nav,
		$action
	);
}
