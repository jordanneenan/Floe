<?php
/**
 * Module discovery. Every block (a folder under blocks/ with a block.json, one
 * or two levels deep) and every component (a folder under components/) is a
 * module. Nothing lists module names: adding a folder adds the module and
 * deleting it removes it on the next page load. Folders starting with "_" are
 * ignored.
 *
 * A child theme can add modules the same way: its own blocks/ and components/
 * folders are scanned after Floe's, and a child folder with the same name as
 * one of Floe's replaces it. Child blocks use the floe/ namespace too, so the
 * editor treats them as Floe sections and parts.
 *
 * Each module can be switched off without deleting it. Disabled modules are
 * stored in the `floe_disabled_modules` option as ids such as
 * "block:page-banner" or "component:button", and the final list passes
 * through the `floe_enabled_modules` filter. A future admin screen only has to
 * write that option.
 */

namespace Floe\Includes\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, array{id:string,type:string,name:string,dir:string,uri:string,parent:?string,enabled:bool}>
 */
function all(): array {
	static $modules = null;
	if ( null !== $modules ) {
		return $modules;
	}

	$found   = array();
	$folders = static function ( string $dir ): array {
		$list = array();
		foreach ( is_dir( $dir ) ? ( scandir( $dir ) ?: array() ) : array() as $entry ) {
			if ( '.' === $entry[0] || '_' === $entry[0] || 'assets' === $entry || ! is_dir( $dir . '/' . $entry ) ) {
				continue;
			}
			$list[ $entry ] = $dir . '/' . $entry;
		}
		return $list;
	};

	// Floe first, then the child theme (if any), so a child module replaces
	// Floe's module of the same name.
	$roots = array( get_template_directory() => get_template_directory_uri() );
	if ( get_stylesheet_directory() !== get_template_directory() ) {
		$roots[ get_stylesheet_directory() ] = get_stylesheet_directory_uri();
	}

	foreach ( $roots as $root => $root_uri ) {
		foreach ( $folders( $root . '/blocks' ) as $name => $dir ) {
			if ( is_file( $dir . '/block.json' ) ) {
				$found[ 'block:' . $name ] = array( 'type' => 'block', 'name' => $name, 'dir' => $dir, 'uri' => $root_uri . '/blocks/' . $name, 'parent' => null );
			}
			foreach ( $folders( $dir ) as $child => $child_dir ) {
				if ( is_file( $child_dir . '/block.json' ) ) {
					$found[ 'block:' . $child ] = array( 'type' => 'block', 'name' => $child, 'dir' => $child_dir, 'uri' => $root_uri . '/blocks/' . $name . '/' . $child, 'parent' => 'block:' . $name );
				}
			}
		}
		foreach ( $folders( $root . '/components' ) as $name => $dir ) {
			$found[ 'component:' . $name ] = array( 'type' => 'component', 'name' => $name, 'dir' => $dir, 'uri' => $root_uri . '/components/' . $name, 'parent' => null );
		}
	}

	$disabled = (array) get_option( 'floe_disabled_modules', array() );
	$enabled  = array();
	foreach ( $found as $id => $module ) {
		$enabled[ $id ] = ! in_array( $id, $disabled, true );
	}

	/**
	 * Filters which modules are enabled.
	 *
	 * @param array<string,bool> $enabled Module id => enabled.
	 * @param array              $found   Discovered modules.
	 */
	$enabled = (array) apply_filters( 'floe_enabled_modules', $enabled, $found );

	$modules = array();
	foreach ( $found as $id => $module ) {
		$is_enabled = ! empty( $enabled[ $id ] );
		// A child block is off whenever its parent is off.
		if ( $module['parent'] && empty( $enabled[ $module['parent'] ] ) ) {
			$is_enabled = false;
		}
		$modules[ $id ] = array( 'id' => $id ) + $module + array( 'enabled' => $is_enabled );
	}
	return $modules;
}

/** Enabled modules of one type ("block" or "component"). */
function enabled( string $type ): array {
	return array_filter( all(), static fn( array $module ): bool => $module['type'] === $type && $module['enabled'] );
}

/** Is a module present and switched on? e.g. is_enabled( 'component:button' ). */
function is_enabled( string $id ): bool {
	$modules = all();
	return isset( $modules[ $id ] ) && $modules[ $id ]['enabled'];
}
