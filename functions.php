<?php
/**
 * Floe's entry point. Keep implementation in includes/.
 *
 * Files directly in includes/ are the theme's core setup. Each subfolder of
 * includes/ (admin/, media/, …) holds self-contained feature files that are
 * loaded automatically: delete a file to remove that feature.
 *
 * @package Floe
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/modules.php';
require_once __DIR__ . '/includes/theme.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/editor.php';
require_once __DIR__ . '/includes/templates.php';
require_once __DIR__ . '/includes/blocks.php';
require_once __DIR__ . '/includes/components.php';

foreach ( glob( __DIR__ . '/includes/*/*.php' ) ?: array() as $floe_include ) {
	require_once $floe_include;
}
unset( $floe_include );
