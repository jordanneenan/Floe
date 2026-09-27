<?php
/**
 * Header component (Figma 30:2): logo, primary menu and one action. Below
 * 1280px the menu and action move into a panel opened by a disclosure button.
 * Appearance → Customize → Header sets whether it sticks to the top of the
 * window (on by default), whether it shows the button, and where the menu
 * sits.
 *
 *   echo Floe\component( 'header' );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function header( array $args = array() ): string {
	$sticky = (bool) get_theme_mod( 'floe_header_sticky', true );
	$button = (bool) get_theme_mod( 'floe_header_button', true );
	$align  = 'right' === get_theme_mod( 'floe_header_menu_align', 'center' ) ? 'right' : 'center';

	$nav    = \Floe\component( 'navigation', array( 'location' => 'primary', 'label' => __( 'Main', 'floe' ), 'class' => 'site-header__nav' ) );
	$action = $button ? \Floe\component( 'navigation', array( 'location' => 'action', 'as' => 'button', 'class' => 'site-header__action' ) ) : '';

	if ( $nav || $action ) {
		wp_enqueue_script( 'floe-header' );
	}

	$toggle = ( $nav || $action ) ? sprintf(
		'<button type="button" class="site-header__toggle" aria-expanded="false" aria-controls="site-header-panel"><span class="screen-reader-text">%1$s</span>%2$s%3$s</button>',
		esc_html__( 'Menu', 'floe' ),
		\Floe\icon( 'menu', array( 'class' => 'site-header__icon-open' ) ),
		\Floe\icon( 'close', array( 'class' => 'site-header__icon-close' ) )
	) : '';

	$class = 'site-header site-header--menu-' . $align . ( $sticky ? ' site-header--sticky' : '' ) . ' surface-base';

	return sprintf(
		'<header class="%1$s"><div class="site-header__inner">%2$s%3$s<div class="site-header__panel" id="site-header-panel">%4$s%5$s</div></div></header>',
		esc_attr( $class ),
		\Floe\component( 'logo', array( 'class' => 'site-header__logo' ) ),
		$toggle,
		$nav,
		$action
	);
}

function header_sanitize_align( $value ): string {
	return 'right' === $value ? 'right' : 'center';
}

/** Appearance → Customize → Header: sticky, the button and the menu position. */
function header_customizer( \WP_Customize_Manager $customizer ): void {
	$customizer->add_section(
		'floe_header',
		array(
			'title'       => __( 'Header', 'floe' ),
			'description' => __( 'Menus set the header menu and the button.', 'floe' ),
			'priority'    => 110,
		)
	);

	$customizer->add_setting(
		'floe_header_sticky',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$customizer->add_control(
		'floe_header_sticky',
		array(
			'label'       => __( 'Sticky header', 'floe' ),
			'description' => __( 'The header stays at the top of the window while the page scrolls.', 'floe' ),
			'section'     => 'floe_header',
			'type'        => 'checkbox',
		)
	);

	$customizer->add_setting(
		'floe_header_button',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$customizer->add_control(
		'floe_header_button',
		array(
			'label'       => __( 'Show the button', 'floe' ),
			'description' => __( 'The first item of the “Header and footer button” menu. The footer keeps its button either way.', 'floe' ),
			'section'     => 'floe_header',
			'type'        => 'checkbox',
		)
	);

	$customizer->add_setting(
		'floe_header_menu_align',
		array(
			'default'           => 'center',
			'sanitize_callback' => __NAMESPACE__ . '\\header_sanitize_align',
		)
	);
	$customizer->add_control(
		'floe_header_menu_align',
		array(
			'label'       => __( 'Menu position', 'floe' ),
			'description' => __( 'From 1280px wide. Below that the menu is behind the menu button.', 'floe' ),
			'section'     => 'floe_header',
			'type'        => 'radio',
			'choices'     => array(
				'center' => __( 'Centred', 'floe' ),
				'right'  => __( 'Right', 'floe' ),
			),
		)
	);
}
add_action( 'customize_register', __NAMESPACE__ . '\\header_customizer' );
