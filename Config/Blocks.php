<?php
/** Register each one-level block directory through core's block metadata API. */

namespace Floe\Config\Blocks;

defined( 'ABSPATH' ) || exit;

function register(): void {
	$root = get_template_directory() . '/Blocks';

	if ( ! is_dir( $root ) ) {
		return;
	}

	$entries = new \DirectoryIterator( $root );
	foreach ( $entries as $entry ) {
		if ( ! $entry->isDir() || $entry->isDot() ) {
			continue;
		}

		$directory = $entry->getPathname();
		if ( is_file( $directory . '/block.json' ) ) {
			register_block_type( $directory );
		}
	}
}
add_action( 'init', __NAMESPACE__ . '\\register' );
