<?php
/**
 * Floe's entry point. Keep implementation in Config/.
 *
 * Files directly in Config/ are the theme's core setup. Each subfolder of
 * Config/ (Admin/, Media/, …) holds self-contained feature files that are
 * loaded automatically: delete a file to remove that feature.
 *
 * @package Floe
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Config/Theme.php';
require_once __DIR__ . '/Config/Assets.php';
require_once __DIR__ . '/Config/Editor.php';
require_once __DIR__ . '/Config/Templates.php';
require_once __DIR__ . '/Config/Blocks.php';

foreach ( glob( __DIR__ . '/Config/*/*.php' ) ?: array() as $floe_config_file ) {
	require_once $floe_config_file;
}
unset( $floe_config_file );
