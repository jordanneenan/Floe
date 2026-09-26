<?php
/**
 * Breadcrumb component: the core Breadcrumbs block (WordPress 6.9+), which
 * builds the trail from the page hierarchy, in Floe's styling.
 *
 *   Floe\component( 'breadcrumb' )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function breadcrumb( array $args = array() ): string {
	if ( ! \WP_Block_Type_Registry::get_instance()->is_registered( 'core/breadcrumbs' ) || is_front_page() ) {
		return '';
	}
	$html = render_block(
		array(
			'blockName'    => 'core/breadcrumbs',
			'attrs'        => array( 'separator' => '/' ),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	);
	return '' !== trim( $html ) ? '<div class="breadcrumb">' . $html . '</div>' : '';
}
