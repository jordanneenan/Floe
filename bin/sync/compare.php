<?php
/**
 * floe-sync: lists what differs between two fingerprints (see fingerprint.php).
 *
 *   php compare.php <from.json> <to.json> [<other.json>]
 *
 * Prints one line per difference, as the change that turns "from" into "to":
 * "+" only in "to", "~" changed, "-" only in "from". With a third fingerprint,
 * only lists items where "to" also differs from "other" (for example, live's
 * changes since the last sync that local doesn't already have). Exits 1 when
 * there are differences and 0 when there are none.
 */

$from = json_decode( (string) file_get_contents( $argv[1] ), true );
$to   = json_decode( (string) file_get_contents( $argv[2] ), true );
$other = isset( $argv[3] ) ? json_decode( (string) file_get_contents( $argv[3] ), true ) : null;
if ( ! is_array( $from ) || ! is_array( $to ) || ( isset( $argv[3] ) && ! is_array( $other ) ) ) {
	fwrite( STDERR, "compare.php: couldn't read a fingerprint\n" );
	exit( 2 );
}

$labels = array(
	'nav_menu_item'    => 'menu item',
	'nav_menu'         => 'menu',
	'wp_global_styles' => 'global styles',
	'wp_block'         => 'pattern',
	'wp_navigation'    => 'navigation',
);
$label  = static function ( array $item, string $id ) use ( $labels ): string {
	$type = $labels[ $item['type'] ] ?? str_replace( '_', ' ', $item['type'] );
	return sprintf( '%s "%s" (#%s)', $type, $item['title'], $id );
};

// True when "to" and "other" agree on this item, so it isn't worth listing.
$same_as_other = static function ( string $group, $key ) use ( $to, $other ): bool {
	if ( null === $other ) {
		return false;
	}
	$a = $to[ $group ][ $key ] ?? null;
	$b = $other[ $group ][ $key ] ?? null;
	return ( is_array( $a ) ? $a['hash'] : $a ) === ( is_array( $b ) ? $b['hash'] : $b );
};

$lines = array();
foreach ( array( 'posts', 'terms' ) as $group ) {
	$a = array_filter( $from[ $group ] ?? array(), static fn( $id ) => ! $same_as_other( $group, $id ), ARRAY_FILTER_USE_KEY );
	$b = array_filter( $to[ $group ] ?? array(), static fn( $id ) => ! $same_as_other( $group, $id ), ARRAY_FILTER_USE_KEY );
	foreach ( $b as $id => $item ) {
		if ( ! isset( $a[ $id ] ) ) {
			$lines[] = '  + ' . $label( $item, (string) $id );
		} elseif ( $a[ $id ]['hash'] !== $item['hash'] ) {
			$when = isset( $item['modified'], $a[ $id ]['modified'] ) && $item['modified'] !== $a[ $id ]['modified']
				? sprintf( ' [%s UTC, was %s]', $item['modified'], $a[ $id ]['modified'] )
				: '';
			$lines[] = '  ~ ' . $label( $item, (string) $id ) . $when;
		}
	}
	foreach ( $a as $id => $item ) {
		if ( ! isset( $b[ $id ] ) ) {
			$lines[] = '  - ' . $label( $item, (string) $id );
		}
	}
}
$a = array_filter( $from['options'] ?? array(), static fn( $name ) => ! $same_as_other( 'options', $name ), ARRAY_FILTER_USE_KEY );
$b = array_filter( $to['options'] ?? array(), static fn( $name ) => ! $same_as_other( 'options', $name ), ARRAY_FILTER_USE_KEY );
foreach ( $b as $name => $hash ) {
	if ( ! isset( $a[ $name ] ) ) {
		$lines[] = "  + setting \"$name\"";
	} elseif ( $a[ $name ] !== $hash ) {
		$lines[] = "  ~ setting \"$name\"";
	}
}
foreach ( array_diff_key( $a, $b ) as $name => $hash ) {
	$lines[] = "  - setting \"$name\"";
}

echo implode( "\n", $lines ), $lines ? "\n" : '';
exit( $lines ? 1 : 0 );
