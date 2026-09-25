<?php
/**
 * Code injection (following Made, built natively): Appearance → Customize →
 * Code injection has Head, Start of body and Footer fields for tracking and
 * chat scripts. Stored as site options so they survive a theme change.
 * Only users who can post unfiltered HTML (administrators) see the fields.
 */

namespace Floe\Config\Admin\CodeInjection;

defined( 'ABSPATH' ) || exit;

const FIELDS = array(
	'floe_code_head'   => 'Head',
	'floe_code_body'   => 'Start of body',
	'floe_code_footer' => 'Footer',
);

function sanitize( $value ): string {
	return current_user_can( 'unfiltered_html' ) ? (string) $value : '';
}

function register( \WP_Customize_Manager $customizer ): void {
	$customizer->add_section(
		'floe_code_injection',
		array(
			'title'       => __( 'Code injection', 'floe' ),
			'description' => __( 'Paste scripts or tags exactly as supplied, for example analytics or chat widgets. They are added to every page.', 'floe' ),
			'priority'    => 200,
			'capability'  => 'unfiltered_html',
		)
	);

	foreach ( FIELDS as $option => $label ) {
		$customizer->add_setting(
			$option,
			array(
				'type'              => 'option',
				'capability'        => 'unfiltered_html',
				'sanitize_callback' => __NAMESPACE__ . '\\sanitize',
				'transport'         => 'refresh',
			)
		);
		$customizer->add_control(
			$option,
			array(
				'label'   => $label,
				'section' => 'floe_code_injection',
				'type'    => 'textarea',
			)
		);
	}
}
add_action( 'customize_register', __NAMESPACE__ . '\\register' );

function output( string $option ): void {
	$code = (string) get_option( $option, '' );
	if ( '' !== trim( $code ) ) {
		echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- raw code entered by an administrator.
	}
}

add_action( 'wp_head', fn() => output( 'floe_code_head' ), 99 );
add_action( 'wp_body_open', fn() => output( 'floe_code_body' ), 1 );
add_action( 'wp_footer', fn() => output( 'floe_code_footer' ), 99 );
