<?php
/**
 * Sends WordPress email (form alerts, password resets) through an SMTP
 * server when wp-config.php sets FLOE_SMTP_HOST. Without it WordPress uses
 * the web server's own mail, which is often missing or lands in spam.
 * Credentials live in wp-config.php, never in the theme or the database.
 *
 *   define( 'FLOE_SMTP_HOST', 'smtp.gmail.com' );
 *   define( 'FLOE_SMTP_PORT', 587 );                  // optional, default 587
 *   define( 'FLOE_SMTP_USER', 'someone@gmail.com' );
 *   define( 'FLOE_SMTP_PASS', 'app password' );
 *   define( 'FLOE_SMTP_FROM', 'someone@gmail.com' );  // optional, default the user
 */

namespace Floe\Includes\Mail;

defined( 'ABSPATH' ) || exit;

function setting( string $name, $fallback = '' ) {
	$constant = 'FLOE_SMTP_' . $name;
	return defined( $constant ) && constant( $constant ) ? constant( $constant ) : $fallback;
}

function configured(): bool {
	return '' !== (string) setting( 'HOST' );
}

function smtp( $mailer ): void {
	if ( ! configured() ) {
		return;
	}
	$port = (int) setting( 'PORT', 587 );
	$mailer->isSMTP();
	$mailer->Host       = (string) setting( 'HOST' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->Port       = $port; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$mailer->SMTPSecure = 465 === $port ? 'ssl' : 'tls'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	if ( setting( 'USER' ) ) {
		$mailer->SMTPAuth = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$mailer->Username = (string) setting( 'USER' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$mailer->Password = (string) setting( 'PASS' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
}
add_action( 'phpmailer_init', __NAMESPACE__ . '\\smtp' );

// Most SMTP services only send from the account's own address.
function from( string $email ): string {
	return configured() ? (string) setting( 'FROM', setting( 'USER', $email ) ) : $email;
}
add_filter( 'wp_mail_from', __NAMESPACE__ . '\\from' );

// "Floe" rather than "WordPress" as the sender's name.
function from_name( string $name ): string {
	return 'WordPress' === $name ? wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) : $name;
}
add_filter( 'wp_mail_from_name', __NAMESPACE__ . '\\from_name' );
