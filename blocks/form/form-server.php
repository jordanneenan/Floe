<?php
/**
 * Form handling. Submissions post to admin-post.php (action "floe_form"),
 * are saved as private "Enquiries" (a post type with no public URL), and are
 * emailed to the site's administration email address (Settings → General,
 * or the floe_form_recipient filter) with Reply-To set to the sender. The
 * entry is saved first, so nothing is lost when email isn't working.
 *
 * Spam: a hidden field people never fill in, a signed timestamp that rejects
 * forms sent within two seconds of loading, and five sends per visitor every
 * ten minutes. There's no nonce, so cached pages keep working.
 */

namespace Floe\Blocks\Form;

defined( 'ABSPATH' ) || exit;

const POST_TYPE = 'floe_enquiry';
const ACTION    = 'floe_form';
const TRAP      = 'floe_website';
const KINDS     = array( 'enquiry', 'signup' );

function kind( $value ): string {
	return in_array( $value, KINDS, true ) ? $value : 'enquiry';
}

function kind_label( string $kind ): string {
	return 'signup' === $kind ? __( 'Newsletter', 'floe' ) : __( 'Enquiry', 'floe' );
}

/** A unique element id per form on the page: form-enquiry, form-enquiry-2, … */
function next_id( string $kind ): string {
	static $used = array();
	$used[ $kind ] = ( $used[ $kind ] ?? 0 ) + 1;
	return 'form-' . $kind . ( $used[ $kind ] > 1 ? '-' . $used[ $kind ] : '' );
}

/** The result shown after a send without JavaScript ("sent" or "error"). */
function status( string $kind ): string {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	if ( ! isset( $_GET['floe-form'], $_GET['floe-status'] ) || sanitize_key( $_GET['floe-form'] ) !== $kind ) {
		return '';
	}
	$status = sanitize_key( $_GET['floe-status'] );
	// phpcs:enable
	return in_array( $status, array( 'sent', 'error' ), true ) ? $status : '';
}

function token(): string {
	$time = (string) time();
	return $time . '.' . wp_hash( ACTION . '|' . $time );
}

/** 'ok', 'fast' (sent too soon after loading) or 'bad' (missing or forged). */
function check_token( string $token ): string {
	$parts = explode( '.', $token );
	if ( 2 !== count( $parts ) || ! hash_equals( wp_hash( ACTION . '|' . $parts[0] ), $parts[1] ) ) {
		return 'bad';
	}
	return time() - (int) $parts[0] < 2 ? 'fast' : 'ok';
}

function visitor_ip(): string {
	// Behind Cloudflare every request comes from its servers; it passes the visitor's address on.
	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	return (string) filter_var( wp_unslash( $ip ), FILTER_VALIDATE_IP );
}

/** Counts a send and says whether this visitor is over the limit. */
function rate_limited(): bool {
	$key   = 'floe_form_' . md5( visitor_ip() );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return true;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

function field( string $name, int $max = 200 ): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form, see the file comment.
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( (string) $_POST[ $name ] ) ) : '';
	return mb_substr( $value, 0, $max );
}

function handle(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form, see the file comment.
	$kind  = kind( field( 'floe_kind', 20 ) );
	$page  = absint( field( 'floe_page', 20 ) );
	$check = check_token( field( 'floe_token' ) );

	// Bots that fill in the hidden field: say it worked and keep nothing.
	if ( '' !== field( TRAP ) ) {
		respond( $kind, $page, true );
	}
	if ( 'bad' === $check ) {
		respond( $kind, $page, false, __( 'Sorry, this form has expired. Please reload the page and try again.', 'floe' ) );
	}
	if ( 'fast' === $check ) {
		respond( $kind, $page, false, __( 'Sorry, that was a little too quick. Please send it again.', 'floe' ) );
	}

	$values = array(
		'email'        => sanitize_email( field( 'email' ) ),
		'first_name'   => field( 'first_name', 100 ),
		'last_name'    => field( 'last_name', 100 ),
		'organisation' => field( 'organisation', 150 ),
		'message'      => isset( $_POST['message'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( (string) $_POST['message'] ) ), 0, 5000 ) : '',
		'consent'      => ! empty( $_POST['consent'] ),
	);
	// phpcs:enable

	$invalid = array();
	if ( ! is_email( $values['email'] ) ) {
		$invalid[] = 'email';
	}
	if ( 'enquiry' === $kind ) {
		foreach ( array( 'first_name', 'message' ) as $name ) {
			if ( '' === trim( $values[ $name ] ) ) {
				$invalid[] = $name;
			}
		}
	}
	if ( $invalid ) {
		respond( $kind, $page, false, __( 'Please fill in the highlighted fields.', 'floe' ), $invalid );
	}
	if ( rate_limited() ) {
		respond( $kind, $page, false, __( 'Sorry, too many messages have been sent from here. Please try again in a few minutes.', 'floe' ) );
	}

	$name = trim( $values['first_name'] . ' ' . $values['last_name'] );
	$id   = wp_insert_post(
		array(
			'post_type'    => POST_TYPE,
			'post_status'  => 'private',
			'post_title'   => $name ?: $values['email'],
			'post_content' => $values['message'],
			'meta_input'   => array(
				'_floe_kind'         => $kind,
				'_floe_email'        => $values['email'],
				'_floe_first_name'   => $values['first_name'],
				'_floe_last_name'    => $values['last_name'],
				'_floe_organisation' => $values['organisation'],
				'_floe_consent'      => $values['consent'] ? 1 : 0,
				'_floe_page'         => $page,
			),
		),
		true
	);
	$saved = ! is_wp_error( $id );
	$sent  = notify( $kind, $values, $page, $saved ? (int) $id : 0 );
	if ( $saved ) {
		update_post_meta( $id, '_floe_emailed', $sent ? 1 : 0 );
	}
	if ( ! $saved && ! $sent ) {
		respond( $kind, $page, false );
	}
	respond( $kind, $page, true );
}
add_action( 'admin_post_nopriv_' . ACTION, __NAMESPACE__ . '\\handle' );
add_action( 'admin_post_' . ACTION, __NAMESPACE__ . '\\handle' );

/**
 * JSON for form.js; otherwise back to the page with the result in the query
 * string, which form.php reads.
 */
function respond( string $kind, int $page, bool $ok, string $message = '', array $fields = array() ): void {
	if ( wp_is_json_request() ) {
		wp_send_json(
			array(
				'ok'      => $ok,
				'message' => $message,
				'fields'  => $fields,
			),
			$ok ? 200 : 400
		);
	}
	$url = ( $page ? get_permalink( $page ) : '' ) ?: ( wp_get_referer() ?: home_url( '/' ) );
	$url = add_query_arg(
		array(
			'floe-form'   => $kind,
			'floe-status' => $ok ? 'sent' : 'error',
		),
		remove_query_arg( array( 'floe-form', 'floe-status' ), $url )
	);
	wp_safe_redirect( $url . '#form-' . $kind, 303 );
	exit;
}

function notify( string $kind, array $values, int $page, int $id ): bool {
	/**
	 * Filters where form entries are emailed. Defaults to the site's
	 * administration email address (Settings → General).
	 *
	 * @param string $recipient Email address(es).
	 * @param string $kind      "enquiry" or "signup".
	 */
	$to   = (string) apply_filters( 'floe_form_recipient', get_option( 'admin_email' ), $kind );
	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$name = trim( $values['first_name'] . ' ' . $values['last_name'] );
	$from = $page ? get_the_title( $page ) : '';

	if ( 'signup' === $kind ) {
		/* translators: 1: site name, 2: email address. */
		$subject = sprintf( __( '[%1$s] Newsletter signup: %2$s', 'floe' ), $site, $values['email'] );
		$lines   = array(
			__( 'Someone joined the newsletter.', 'floe' ),
			'',
			__( 'Email:', 'floe' ) . ' ' . $values['email'],
		);
	} else {
		/* translators: 1: site name, 2: sender's name. */
		$subject = sprintf( __( '[%1$s] New enquiry from %2$s', 'floe' ), $site, $name );
		$lines   = array(
			__( 'Name:', 'floe' ) . ' ' . $name,
			__( 'Email:', 'floe' ) . ' ' . $values['email'],
			__( 'Organisation:', 'floe' ) . ' ' . ( $values['organisation'] ?: '–' ),
			__( 'Agreed to be contacted:', 'floe' ) . ' ' . ( $values['consent'] ? __( 'Yes', 'floe' ) : __( 'No', 'floe' ) ),
			'',
			$values['message'],
		);
	}
	if ( $from ) {
		$lines[] = '';
		/* translators: %s: page title. */
		$lines[] = sprintf( __( 'Sent from the %s page.', 'floe' ), $from );
	}
	if ( $id ) {
		$lines[] = __( 'In WordPress:', 'floe' ) . ' ' . admin_url( 'post.php?post=' . $id . '&action=edit' );
	}

	// Replying to the email goes straight to the sender. wp_mail() reads a
	// comma as a second address, so the name loses commas and quotes.
	$reply = trim( str_replace( array( '"', ',', '<', '>', "\r", "\n" ), '', $name ) );
	return wp_mail( $to, $subject, implode( "\n", $lines ), array( 'Reply-To: ' . ( $reply ? $reply . ' ' : '' ) . '<' . $values['email'] . '>' ) );
}

// ---------------------------------------------------------------------------
// Enquiries in the admin
// ---------------------------------------------------------------------------

function register_post_type(): void {
	\register_post_type(
		POST_TYPE,
		array(
			'labels'              => array(
				'name'               => __( 'Enquiries', 'floe' ),
				'singular_name'      => __( 'Enquiry', 'floe' ),
				'all_items'          => __( 'All enquiries', 'floe' ),
				'edit_item'          => __( 'Enquiry', 'floe' ),
				'search_items'       => __( 'Search enquiries', 'floe' ),
				'not_found'          => __( 'No enquiries yet.', 'floe' ),
				'not_found_in_trash' => __( 'No enquiries in the bin.', 'floe' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 25,
			'supports'            => false,
			'rewrite'             => false,
			'query_var'           => false,
			// Editors and administrators; entries only come from the form.
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
		)
	);
}
// This file is loaded while blocks register on init, so register right away.
if ( did_action( 'init' ) ) {
	register_post_type();
} else {
	add_action( 'init', __NAMESPACE__ . '\\register_post_type' );
}

// Enquiries sits after Posts in Floe's menu order (includes/admin/menu.php).
function menu_order( $order ) {
	if ( ! is_array( $order ) ) {
		return $order;
	}
	$item  = 'edit.php?post_type=' . POST_TYPE;
	$order = array_values( array_diff( $order, array( $item ) ) );
	$after = array_search( 'edit.php', $order, true );
	array_splice( $order, false === $after ? count( $order ) : $after + 1, 0, array( $item ) );
	return $order;
}
add_filter( 'menu_order', __NAMESPACE__ . '\\menu_order', 20 );

function meta( int $id, string $key ): string {
	return (string) get_post_meta( $id, '_floe_' . $key, true );
}

function columns(): array {
	return array(
		'cb'    => '<input type="checkbox">',
		'title' => __( 'Name', 'floe' ),
		'email' => __( 'Email', 'floe' ),
		'kind'  => __( 'Form', 'floe' ),
		'page'  => __( 'Page', 'floe' ),
		'date'  => __( 'Received', 'floe' ),
	);
}
add_filter( 'manage_' . POST_TYPE . '_posts_columns', __NAMESPACE__ . '\\columns' );

function column( string $column, int $id ): void {
	if ( 'email' === $column ) {
		$email = meta( $id, 'email' );
		printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
		if ( '0' === meta( $id, 'emailed' ) ) {
			echo '<br><span class="description">' . esc_html__( 'Email alert not sent', 'floe' ) . '</span>';
		}
	} elseif ( 'kind' === $column ) {
		echo esc_html( kind_label( meta( $id, 'kind' ) ) );
	} elseif ( 'page' === $column ) {
		$page = (int) meta( $id, 'page' );
		echo $page ? esc_html( get_the_title( $page ) ) : '–';
	}
}
add_action( 'manage_' . POST_TYPE . '_posts_custom_column', __NAMESPACE__ . '\\column', 10, 2 );

// Row actions: open and bin only (there's nothing to quick-edit).
function row_actions( array $actions, \WP_Post $post ): array {
	if ( POST_TYPE !== $post->post_type ) {
		return $actions;
	}
	unset( $actions['inline hide-if-no-js'] );
	if ( isset( $actions['edit'] ) ) {
		$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $post->ID ) ), esc_html__( 'Open', 'floe' ) );
	}
	return $actions;
}
add_filter( 'post_row_actions', __NAMESPACE__ . '\\row_actions', 10, 2 );
add_filter( 'page_row_actions', __NAMESPACE__ . '\\row_actions', 10, 2 );

// "Enquiry" and "Newsletter" views above the list.
function views( array $views ): array {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current = isset( $_GET['floe_kind'] ) ? sanitize_key( $_GET['floe_kind'] ) : '';
	foreach ( KINDS as $kind ) {
		$url            = add_query_arg( array( 'post_type' => POST_TYPE, 'floe_kind' => $kind ), admin_url( 'edit.php' ) );
		$views[ $kind ] = sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), $current === $kind ? ' class="current" aria-current="page"' : '', esc_html( kind_label( $kind ) ) );
	}
	return $views;
}
add_filter( 'views_edit-' . POST_TYPE, __NAMESPACE__ . '\\views' );

function filter_list( \WP_Query $query ): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$kind = isset( $_GET['floe_kind'] ) ? sanitize_key( $_GET['floe_kind'] ) : '';
	if ( is_admin() && $query->is_main_query() && POST_TYPE === $query->get( 'post_type' ) && in_array( $kind, KINDS, true ) ) {
		$query->set( 'meta_key', '_floe_kind' );
		$query->set( 'meta_value', $kind );
	}
}
add_action( 'pre_get_posts', __NAMESPACE__ . '\\filter_list' );

// The enquiry screen: the entry, read-only, with Reply and Move to bin.
function meta_boxes(): void {
	remove_meta_box( 'submitdiv', POST_TYPE, 'side' );
	add_meta_box( 'floe-enquiry', __( 'Details', 'floe' ), __NAMESPACE__ . '\\details', POST_TYPE, 'normal', 'high' );
}
add_action( 'add_meta_boxes_' . POST_TYPE, __NAMESPACE__ . '\\meta_boxes' );

function details( \WP_Post $post ): void {
	$email = meta( $post->ID, 'email' );
	$page  = (int) meta( $post->ID, 'page' );
	$rows  = array(
		__( 'Form', 'floe' )     => esc_html( kind_label( meta( $post->ID, 'kind' ) ) ),
		__( 'Name', 'floe' )     => esc_html( trim( meta( $post->ID, 'first_name' ) . ' ' . meta( $post->ID, 'last_name' ) ) ),
		__( 'Email', 'floe' )    => sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) ),
		__( 'Organisation', 'floe' ) => esc_html( meta( $post->ID, 'organisation' ) ),
		__( 'Agreed to be contacted', 'floe' ) => meta( $post->ID, 'consent' ) ? esc_html__( 'Yes', 'floe' ) : esc_html__( 'No', 'floe' ),
		__( 'Page', 'floe' )     => $page ? sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink( $page ) ), esc_html( get_the_title( $page ) ) ) : '',
		__( 'Received', 'floe' ) => esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post ) ),
		__( 'Email alert', 'floe' ) => '0' === meta( $post->ID, 'emailed' ) ? esc_html__( 'Not sent (check the site’s email settings)', 'floe' ) : esc_html__( 'Sent', 'floe' ),
	);
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( array_filter( $rows, 'strlen' ) as $label => $value ) {
		printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', esc_html( $label ), $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	if ( '' !== trim( $post->post_content ) ) {
		printf( '<tr><th scope="row">%1$s</th><td>%2$s</td></tr>', esc_html__( 'Message', 'floe' ), wp_kses_post( wpautop( esc_html( $post->post_content ) ) ) );
	}
	echo '</tbody></table>';
	printf(
		'<p><a class="button button-primary" href="mailto:%1$s">%2$s</a> <a class="button-link-delete" href="%3$s">%4$s</a></p>',
		esc_attr( $email ),
		esc_html__( 'Reply by email', 'floe' ),
		esc_url( (string) get_delete_post_link( $post->ID ) ),
		esc_html__( 'Move to bin', 'floe' )
	);
}
