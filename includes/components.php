<?php
/**
 * Component discovery. Each enabled folder in components/ is loaded:
 *
 *   components/<name>/<name>.php     defines Floe\Components\<name>( array $args ): string
 *   components/<name>/assets/<name>.css  loaded on the front end and in the editor
 *   components/<name>/assets/<name>.js   registered as "floe-<name>" for front-end use
 *   components/<name>/assets/<name>-editor-script.js  loaded in the block editor
 *        (for a component that adds editor behaviour to every block)
 *
 * Call components through Floe\component( 'button', $args ). If a component
 * has been deleted or switched off, that returns an empty string (and logs
 * when WP_DEBUG is on) instead of causing a fatal error.
 */

namespace Floe\Includes\Components;

use Floe\Includes\Modules;

defined( 'ABSPATH' ) || exit;

function load(): void {
	foreach ( Modules\enabled( 'component' ) as $module ) {
		$file = $module['dir'] . '/' . $module['name'] . '.php';
		if ( is_file( $file ) ) {
			require_once $file;
		}
	}
}
load();

function url( array $module, string $file ): string {
	return get_template_directory_uri() . '/components/' . $module['name'] . '/assets/' . $file;
}

function register_assets(): void {
	foreach ( Modules\enabled( 'component' ) as $module ) {
		$css = $module['dir'] . '/assets/' . $module['name'] . '.css';
		if ( is_file( $css ) ) {
			wp_register_style( 'floe-' . $module['name'], url( $module, $module['name'] . '.css' ), array(), (string) filemtime( $css ) );
		}
		register_script(
			$module,
			'',
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		register_script( $module, '-editor-script', array( 'in_footer' => true ) );
	}
}
add_action( 'init', __NAMESPACE__ . '\\register_assets' );

// Registers assets/<name><suffix>.js as "floe-<name><suffix>", with the
// dependencies the build wrote to its .asset.php.
function register_script( array $module, string $suffix, array $args ): void {
	$file  = $module['name'] . $suffix;
	$js    = $module['dir'] . '/assets/' . $file . '.js';
	$asset = $module['dir'] . '/assets/' . $file . '.asset.php';
	if ( ! is_file( $js ) ) {
		return;
	}
	$meta = is_file( $asset ) ? require $asset : array( 'dependencies' => array(), 'version' => (string) filemtime( $js ) );
	wp_register_script( 'floe-' . $file, url( $module, $file . '.js' ), $meta['dependencies'], $meta['version'], $args );
}

// Component styles are small and used everywhere (buttons, eyebrows, header),
// so they load on every page and in the editor canvas.
function enqueue_styles(): void {
	foreach ( Modules\enabled( 'component' ) as $module ) {
		if ( wp_style_is( 'floe-' . $module['name'], 'registered' ) ) {
			wp_enqueue_style( 'floe-' . $module['name'] );
		}
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_styles' );
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_styles' );

// A component's editor script extends every block, so it loads on every
// block editor screen.
function enqueue_editor_scripts(): void {
	foreach ( Modules\enabled( 'component' ) as $module ) {
		if ( wp_script_is( 'floe-' . $module['name'] . '-editor-script', 'registered' ) ) {
			wp_enqueue_script( 'floe-' . $module['name'] . '-editor-script' );
		}
	}
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_scripts' );

namespace Floe;

/**
 * Render a component, e.g. component( 'button', array( 'label' => 'Go', 'url' => '/' ) ).
 * Returns escaped HTML, or '' when the component is missing or disabled.
 */
function component( string $name, array $args = array() ): string {
	$function = 'Floe\\Components\\' . str_replace( '-', '_', $name );
	if ( ! function_exists( $function ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( sprintf( 'Floe: component "%s" is missing or disabled.', $name ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
		return '';
	}
	return (string) $function( $args );
}

/** Shorthand for the icon component: icon( 'arrow' ). */
function icon( string $name, array $args = array() ): string {
	return component( 'icon', array( 'name' => $name ) + $args );
}
