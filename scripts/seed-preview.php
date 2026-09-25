<?php
/**
 * Floe preview content. Builds the brochure site and the Block Preview page
 * on any WordPress site running Floe, and is safe to run again: everything is
 * found by slug, name or file before it is created, and updated in place.
 *
 *   wp eval-file wp-content/themes/floe/scripts/seed-preview.php
 *   wp eval-file wp-content/themes/floe/scripts/seed-preview.php /path/to/images
 *
 * With a folder argument, the JPG/PNG/WebP files in it are uploaded to the
 * media library first (through WordPress's normal upload processing), unless
 * a file of the same name is already there. Patterns find images by file
 * name (floe-hero, floe-dawn, …), so slots stay empty until they exist.
 *
 * It also:
 * - uploads the placeholder client logos from Assets/PreviewImagery/logos
 * - generates four small placeholder documents for Document Download
 * - creates three sample journal posts with categories and featured images
 * - creates Home, Platform, Pricing, About, Contact, Journal, Privacy,
 *   Accessibility and Block Preview pages from the theme's patterns
 * - sets Home as the front page and Journal as the posts page
 * - builds the header, button and footer menus
 * - sets the site title, tagline, date format and footer legal line
 * - moves WordPress's "Hello world!" post and "Sample Page" to the trash
 */

defined( 'ABSPATH' ) || exit;

use function Floe\Config\Patterns\{block, image_id, link, paragraph, heading, items};

$floe_log = static function ( string $message ): void {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::log( $message );
	} else {
		echo esc_html( $message ) . "\n";
	}
};

if ( 'floe' !== get_template() ) {
	$floe_log( 'Floe is not the active theme. Nothing done.' );
	return;
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// New pattern files are picked up straight away.
wp_get_theme()->delete_pattern_cache();

/** Upload a local file to the media library as if it had been uploaded in the editor. */
$floe_import = static function ( string $path, string $title = '', string $alt = '' ) use ( $floe_log ): int {
	$name = pathinfo( $path, PATHINFO_FILENAME );
	$id   = image_id( $name );
	if ( $id ) {
		return $id;
	}
	$tmp = wp_tempnam( basename( $path ) );
	copy( $path, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => basename( $path ),
			'tmp_name' => $tmp,
		),
		0,
		'' !== $title ? $title : $name
	);
	if ( is_wp_error( $id ) ) {
		$floe_log( 'Could not upload ' . basename( $path ) . ': ' . $id->get_error_message() );
		wp_delete_file( $tmp );
		return 0;
	}
	if ( '' !== $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	$floe_log( 'Uploaded ' . basename( $path ) . " (#{$id})" );
	return (int) $id;
};

// ---------------------------------------------------------------------------
// 1. Media
// ---------------------------------------------------------------------------
$floe_alt = array(
	'floe-hero'      => 'Aerial view of white ice floes scattered across deep navy water',
	'floe-dawn'      => 'Soft white ice floes on pale lilac-blue water',
	'floe-drift'     => 'Sparse ice floes drifting on very dark water',
	'floe-seam'      => 'Tightly packed white ice floes separated by thin dark cracks',
	'floe-blue'      => 'White ice floes on bright royal blue water',
	'floe-giant'     => 'A few very large ice floes divided by dark channels',
	'floe-pack-teal' => 'Dense white pack ice on teal water',
	'floe-dusk'      => 'Off-white ice floes on indigo water',
	'open-water'     => 'Open blue water with small white highlights',
);

$floe_folder = $args[0] ?? '';
if ( $floe_folder && is_dir( $floe_folder ) ) {
	foreach ( glob( trailingslashit( $floe_folder ) . '*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE ) ?: array() as $file ) {
		if ( '_' === basename( $file )[0] ) {
			continue;
		}
		$name = pathinfo( $file, PATHINFO_FILENAME );
		$floe_import( $file, $name, $floe_alt[ $name ] ?? '' );
	}
}
foreach ( array_keys( $floe_alt ) as $name ) {
	if ( 'open-water' !== $name && ! image_id( $name ) ) {
		$floe_log( "Image {$name} isn't in the media library yet; its slots stay empty. Upload it, or run this script with the folder of images." );
	}
}

$floe_logos = array(
	'northwind'    => 'Northwind',
	'halcyon'      => 'Halcyon',
	'meridian'     => 'Meridian',
	'arbor-and-co' => 'Arbor & Co',
	'kestrel'      => 'Kestrel',
	'lumen'        => 'Lumen',
);
foreach ( $floe_logos as $slug => $label ) {
	$file = get_template_directory() . "/Assets/PreviewImagery/logos/logo-{$slug}.png";
	if ( is_file( $file ) ) {
		$floe_import( $file, $label . ' logo', $label );
	}
}

// Placeholder documents, generated so the preview works anywhere.
$floe_pdf = static function ( string $title, string $body ): string {
	$text   = static fn( string $s ): string => str_replace( array( '\\', '(', ')' ), array( '\\\\', '\\(', '\\)' ), $s );
	$stream = 'BT /F1 24 Tf 72 740 Td (' . $text( $title ) . ") Tj ET\nBT /F1 12 Tf 72 710 Td (" . $text( $body ) . ') Tj ET';
	$objects = array(
		'<< /Type /Catalog /Pages 2 0 R >>',
		'<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
		'<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
		'<< /Length ' . strlen( $stream ) . " >>\nstream\n{$stream}\nendstream",
		'<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
	);
	$pdf     = "%PDF-1.4\n";
	$offsets = array();
	foreach ( $objects as $index => $object ) {
		$offsets[] = strlen( $pdf );
		$pdf      .= ( $index + 1 ) . " 0 obj\n{$object}\nendobj\n";
	}
	$xref = strlen( $pdf );
	$pdf .= 'xref' . "\n0 " . ( count( $objects ) + 1 ) . "\n0000000000 65535 f \n";
	foreach ( $offsets as $offset ) {
		$pdf .= sprintf( "%010d 00000 n \n", $offset );
	}
	return $pdf . 'trailer' . "\n<< /Size " . ( count( $objects ) + 1 ) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
};

$floe_documents = array(
	'floe-overview'             => array( 'Floe overview', 'pdf' ),
	'section-library-reference' => array( 'Section library reference', 'pdf' ),
	'editor-guide'              => array( 'Editor guide', 'pdf' ),
	'brand-token-template'      => array( 'Brand token template', 'xlsx' ),
);
foreach ( $floe_documents as $slug => [ $title, $type ] ) {
	if ( image_id( $slug ) ) {
		continue;
	}
	$path = get_temp_dir() . "{$slug}.{$type}";
	if ( 'pdf' === $type ) {
		file_put_contents( $path, $floe_pdf( $title, 'Placeholder document for the Floe preview site.' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	} elseif ( class_exists( 'ZipArchive' ) ) {
		$zip = new ZipArchive();
		$zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE );
		$zip->addFromString( '[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>' );
		$zip->addFromString( '_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>' );
		$zip->addFromString( 'xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Tokens" sheetId="1" r:id="rId1"/></sheets></workbook>' );
		$zip->addFromString( 'xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>' );
		$zip->addFromString( 'xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>Token</t></is></c><c r="B1" t="inlineStr"><is><t>Value</t></is></c></row><row r="2"><c r="A2" t="inlineStr"><is><t>blue</t></is></c><c r="B2" t="inlineStr"><is><t>#2B50E8</t></is></c></row></sheetData></worksheet>' );
		$zip->close();
	} else {
		continue;
	}
	$floe_import( $path, $title );
	wp_delete_file( $path );
}

// ---------------------------------------------------------------------------
// 2. Site settings
// ---------------------------------------------------------------------------
update_option( 'blogname', 'Floe' );
update_option( 'blogdescription', 'Floe is a modular WordPress platform for sites that keep growing.' );
update_option( 'date_format', 'd M Y' );
set_theme_mod( 'floe_footer_legal', 'Open source under GPL-2.0.' );
if ( ! get_option( 'permalink_structure' ) ) {
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
}
$floe_log( 'Site title, tagline, date format and footer legal line set.' );

// ---------------------------------------------------------------------------
// 3. Pages and posts
// ---------------------------------------------------------------------------
$floe_pattern = static function ( string $slug ): string {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	return $pattern ? (string) $pattern['content'] : '';
};

$floe_upsert = static function ( string $type, string $slug, array $data ) use ( $floe_log ): int {
	$existing = get_posts(
		array(
			'post_type'   => $type,
			'name'        => $slug,
			'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'numberposts' => 1,
		)
	);
	$post = array_merge(
		array(
			'post_type'   => $type,
			'post_name'   => $slug,
			'post_status' => 'publish',
		),
		$data
	);
	if ( $existing ) {
		$post['ID'] = $existing[0]->ID;
		$id         = wp_update_post( wp_slash( $post ), true );
	} else {
		$id = wp_insert_post( wp_slash( $post ), true );
	}
	if ( is_wp_error( $id ) ) {
		$floe_log( "Could not save {$type} {$slug}: " . $id->get_error_message() );
		return 0;
	}
	$floe_log( ( $existing ? 'Updated' : 'Created' ) . " {$type} /{$slug}/ (#{$id})" );
	return (int) $id;
};

// The old preview page is reused for the Block Preview.
$floe_old_preview = get_page_by_path( 'floe-block-qa' );
if ( $floe_old_preview ) {
	wp_update_post(
		array(
			'ID'        => $floe_old_preview->ID,
			'post_name' => 'block-preview',
		)
	);
}

$floe_pages = array(
	'home'          => array( 'Home', 'floe/page-home' ),
	'platform'      => array( 'Platform', 'floe/page-platform' ),
	'pricing'       => array( 'Pricing', 'floe/page-pricing' ),
	'about'         => array( 'About', 'floe/page-about' ),
	'contact'       => array( 'Contact', 'floe/page-contact' ),
	'block-preview' => array( 'Block Preview', 'floe/block-preview' ),
);
$floe_ids   = array();
foreach ( $floe_pages as $slug => [ $title, $pattern ] ) {
	$content = $floe_pattern( $pattern );
	if ( '' === $content ) {
		$floe_log( "Pattern {$pattern} is missing; skipped /{$slug}/." );
		continue;
	}
	$floe_ids[ $slug ] = $floe_upsert(
		'page',
		$slug,
		array(
			'post_title'   => $title,
			'post_content' => $content,
			'menu_order'   => count( $floe_ids ),
		)
	);
}

$floe_ids['journal'] = $floe_upsert(
	'page',
	'journal',
	array(
		'post_title'   => 'Journal',
		'post_content' => '',
	)
);

$floe_simple_page = static function ( string $title, string $lead, array $body ): string {
	return block( 'floe/page-banner', array( 'showMedia' => false ) )
		. block(
			'floe/article',
			array(),
			paragraph( $lead, 'lead' ) . implode( '', array_map( static fn( $text ) => paragraph( $text ), $body ) )
		);
};

$floe_ids['privacy'] = $floe_upsert(
	'page',
	'privacy',
	array(
		'post_title'   => 'Privacy',
		'post_content' => $floe_simple_page(
			'Privacy',
			'This is placeholder text for the Floe preview site. Replace it with your organisation’s privacy policy.',
			array( 'WordPress can help you write one: Settings → Privacy has a guide to what a policy for this site should cover.' )
		),
	)
);
$floe_ids['accessibility'] = $floe_upsert(
	'page',
	'accessibility',
	array(
		'post_title'   => 'Accessibility',
		'post_content' => $floe_simple_page(
			'Accessibility',
			'Floe sections are built to meet WCAG 2.2 AA.',
			array( 'Every section works with a keyboard, shows a visible focus state, respects reduced motion and meets colour contrast requirements. This is placeholder text for the preview site: replace it with your organisation’s accessibility statement.' )
		),
	)
);

update_option( 'show_on_front', 'page' );
if ( ! empty( $floe_ids['home'] ) ) {
	update_option( 'page_on_front', $floe_ids['home'] );
}
if ( ! empty( $floe_ids['journal'] ) ) {
	update_option( 'page_for_posts', $floe_ids['journal'] );
}
if ( ! empty( $floe_ids['privacy'] ) ) {
	update_option( 'wp_page_for_privacy_policy', $floe_ids['privacy'] );
}

// Sample journal posts.
$floe_category = static function ( string $name ): int {
	$term = get_term_by( 'name', $name, 'category' );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$term = wp_insert_term( $name, 'category' );
	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
};

$floe_posts = array(
	array( 'a-considered-approach-to-growing-a-website', 'A considered approach to growing a website', 'Insights', '2026-06-12 09:00:00', 'floe-drift', 'Why the first version of a site should be designed for the tenth.', 'The first version of a website is the one everyone plans. The tenth is the one that matters: the version with forty more pages, three new services and a team that has changed twice. Designing for that version means choosing sections, not layouts.' ),
	array( 'building-better-pages-with-fewer-choices', 'Building better pages with fewer choices', 'Journal', '2026-06-04 09:00:00', 'floe-seam', 'Constraints give editors confidence and keep a brand consistent.', 'An editor with every option in front of them hesitates. An editor with the right few options moves quickly and gets it right. Floe’s editor only offers choices that fit the design system, so every page stays on brand.' ),
	array( 'what-comes-next-for-floe', 'What comes next for Floe', 'Updates', '2026-05-20 09:00:00', 'floe-blue', 'New sections, better media handling and a faster editor.', 'The next release brings new sections for proof and conversion, a faster editor, and better handling of looping video and image sizes.' ),
);
foreach ( $floe_posts as [ $slug, $title, $category, $date, $image, $excerpt, $body ] ) {
	$id = $floe_upsert(
		'post',
		$slug,
		array(
			'post_title'    => $title,
			'post_excerpt'  => $excerpt,
			'post_date'     => $date,
			'post_category' => array_filter( array( $floe_category( $category ) ) ),
			'post_content'  => block( 'floe/article', array(), paragraph( $excerpt, 'lead' ) . paragraph( $body ) ),
		)
	);
	if ( $id && image_id( $image ) ) {
		set_post_thumbnail( $id, image_id( $image ) );
	}
}

// WordPress's starter content would otherwise appear in the latest posts.
foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as [ $slug, $type ] ) {
	$starter = get_page_by_path( $slug, OBJECT, $type );
	if ( $starter && 'trash' !== $starter->post_status ) {
		wp_trash_post( $starter->ID );
		$floe_log( "Moved the starter {$type} /{$slug}/ to the trash." );
	}
}

// ---------------------------------------------------------------------------
// 4. Menus
// ---------------------------------------------------------------------------
$floe_menu = static function ( string $name, string $location, array $items ) use ( $floe_log ): void {
	$menu = wp_get_nav_menu_object( $name );
	$id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $name );
	foreach ( (array) wp_get_nav_menu_items( $id ) as $old ) {
		wp_delete_post( $old->ID, true );
	}
	foreach ( $items as $position => $item ) {
		$data = array(
			'menu-item-title'    => $item[0],
			'menu-item-status'   => 'publish',
			'menu-item-position' => $position + 1,
		);
		if ( is_int( $item[1] ) ) {
			$data += array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $item[1],
				'menu-item-type'      => 'post_type',
			);
		} else {
			$data += array(
				'menu-item-url'  => $item[1],
				'menu-item-type' => 'custom',
			);
		}
		if ( ! empty( $item[2] ) ) {
			$data['menu-item-target'] = '_blank';
		}
		wp_update_nav_menu_item( $id, 0, $data );
	}
	$locations              = get_nav_menu_locations();
	$locations[ $location ] = $id;
	set_theme_mod( 'nav_menu_locations', $locations );
	$floe_log( "Menu {$name} → {$location}" );
};

$floe_page = static fn( string $slug ): int => (int) ( $floe_ids[ $slug ] ?? 0 );

$floe_menu(
	'Header',
	'primary',
	array(
		array( 'Platform', $floe_page( 'platform' ) ),
		array( 'Pricing', $floe_page( 'pricing' ) ),
		array( 'About', $floe_page( 'about' ) ),
		array( 'Journal', $floe_page( 'journal' ) ),
		array( 'Contact', $floe_page( 'contact' ) ),
	)
);
$floe_menu( 'Header and footer button', 'action', array( array( 'Get started', $floe_page( 'contact' ) ) ) );
$floe_menu(
	'Explore',
	'footer-1',
	array(
		array( 'Platform', $floe_page( 'platform' ) ),
		array( 'Pricing', $floe_page( 'pricing' ) ),
		array( 'About', $floe_page( 'about' ) ),
		array( 'Journal', $floe_page( 'journal' ) ),
	)
);
$floe_menu(
	'Company',
	'footer-2',
	array(
		array( 'Documentation', 'https://github.com/jordanneenan/Floe/tree/main/Documentation', true ),
		array( 'Contact', $floe_page( 'contact' ) ),
		array( 'Privacy', $floe_page( 'privacy' ) ),
		array( 'Accessibility', $floe_page( 'accessibility' ) ),
	)
);
$floe_menu(
	'Follow',
	'footer-3',
	array(
		array( 'LinkedIn', 'https://www.linkedin.com/', true ),
		array( 'Instagram', 'https://www.instagram.com/', true ),
		array( 'Newsletter', home_url( '/contact/' ) ),
	)
);

$floe_log( 'Done. Visit ' . home_url( '/' ) );
