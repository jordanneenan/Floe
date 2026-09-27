<?php
/**
 * SVG uploads (documentation/decisions.md D69).
 *
 * WordPress refuses SVG files because an SVG can carry script. Floe accepts
 * them from people WordPress already trusts with unfiltered HTML
 * (administrators and editors on a single site; the `floe_svg_uploads`
 * filter changes who), and rebuilds every SVG from an allowlist on the way in:
 *
 * - Only drawing elements and their attributes survive. Scripts, event
 *   handlers, animation, foreignObject, metadata, comments and anything
 *   unknown are removed. Links are removed and what they wrapped is kept.
 * - References stay inside the file: href and CSS url() may only point at an
 *   #id, except that <image> may embed PNG, JPEG, GIF or WebP data. CSS that
 *   could load a file or run script is removed.
 * - A file that isn't well-formed SVG, or whose DOCTYPE declares entities, is
 *   refused. The parser never reads from the network.
 * - An .svg file only passes WordPress's file type check once it's clean, and
 *   wp_upload_bits() (XML-RPC, importers), which skips the upload filters,
 *   can't write SVGs at all.
 *
 * The SVG's width and height are saved as attachment metadata, so it works in
 * media slots, the logo and core Image blocks like any other image. SVGs never
 * go to the image editor or the Customizer's cropper, which work on pixels.
 *
 * Delete this file to turn SVG uploads off. SVGs already in the media library
 * stay there.
 */

namespace Floe\Includes\Media\Svg;

defined( 'ABSPATH' ) || exit;

const MIME     = 'image/svg+xml';
const SVG_NS   = 'http://www.w3.org/2000/svg';
const XLINK_NS = 'http://www.w3.org/1999/xlink';
const XML_NS   = 'http://www.w3.org/XML/1998/namespace';

/** Elements kept. Anything else is removed with its contents, except <a>. */
const ELEMENTS = array(
	'svg', 'g', 'defs', 'symbol', 'use', 'switch', 'title', 'desc', 'style',
	'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
	'text', 'tspan', 'textPath',
	'linearGradient', 'radialGradient', 'stop', 'pattern', 'clipPath', 'mask', 'marker', 'image',
	'filter', 'feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix',
	'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feDropShadow', 'feFlood',
	'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge',
	'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting',
	'feSpotLight', 'feTile', 'feTurbulence',
);

/** Attributes kept (no namespace), besides href, xml:space and xml:lang. */
const ATTRIBUTES = array(
	// Core and accessibility.
	'id', 'class', 'style', 'lang', 'role', 'focusable', 'aria-label', 'aria-labelledby',
	'aria-describedby', 'aria-hidden', 'systemLanguage', 'type', 'media', 'version',
	// Geometry and layout.
	'viewBox', 'preserveAspectRatio', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r',
	'rx', 'ry', 'fx', 'fy', 'fr', 'width', 'height', 'd', 'points', 'pathLength', 'transform',
	// Text.
	'dx', 'dy', 'rotate', 'textLength', 'lengthAdjust', 'startOffset', 'method', 'spacing', 'side',
	// Gradients, patterns, clips, masks and markers.
	'offset', 'gradientUnits', 'gradientTransform', 'spreadMethod', 'patternUnits',
	'patternContentUnits', 'patternTransform', 'clipPathUnits', 'maskUnits', 'maskContentUnits',
	'markerWidth', 'markerHeight', 'markerUnits', 'refX', 'refY', 'orient',
	// Filters.
	'filterUnits', 'primitiveUnits', 'in', 'in2', 'result', 'stdDeviation', 'mode', 'operator',
	'k1', 'k2', 'k3', 'k4', 'values', 'tableValues', 'slope', 'intercept', 'amplitude', 'exponent',
	'kernelMatrix', 'order', 'divisor', 'bias', 'targetX', 'targetY', 'edgeMode', 'preserveAlpha',
	'surfaceScale', 'diffuseConstant', 'specularConstant', 'specularExponent', 'kernelUnitLength',
	'azimuth', 'elevation', 'z', 'pointsAtX', 'pointsAtY', 'pointsAtZ', 'limitingConeAngle',
	'scale', 'xChannelSelector', 'yChannelSelector', 'radius', 'baseFrequency', 'numOctaves',
	'seed', 'stitchTiles',
	// Presentation.
	'alignment-baseline', 'baseline-shift', 'clip', 'clip-path', 'clip-rule', 'color',
	'color-interpolation', 'color-interpolation-filters', 'direction', 'display',
	'dominant-baseline', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'flood-color',
	'flood-opacity', 'font', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch',
	'font-style', 'font-variant', 'font-weight', 'image-rendering', 'isolation', 'letter-spacing',
	'lighting-color', 'marker', 'marker-end', 'marker-mid', 'marker-start', 'mask', 'mask-type',
	'mix-blend-mode', 'opacity', 'overflow', 'paint-order', 'pointer-events', 'shape-rendering',
	'stop-color', 'stop-opacity', 'stroke', 'stroke-dasharray', 'stroke-dashoffset',
	'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke-width',
	'text-anchor', 'text-decoration', 'text-rendering', 'transform-origin', 'unicode-bidi',
	'vector-effect', 'visibility', 'word-spacing', 'writing-mode',
);

/** Whether a user may upload SVGs. Every SVG is cleaned, whoever uploads it. */
function can_upload( $user = null ): bool {
	if ( ! class_exists( '\\DOMDocument' ) ) {
		return false;
	}
	if ( ! $user instanceof \WP_User ) {
		$user = $user ? get_userdata( (int) $user ) : wp_get_current_user();
	}
	$allowed = $user && $user->exists() && user_can( $user, 'upload_files' ) && user_can( $user, 'unfiltered_html' );

	/**
	 * Filters whether a user may upload SVG files.
	 *
	 * @param bool           $allowed Default: the user can upload files and use unfiltered HTML.
	 * @param \WP_User|false $user    The user.
	 */
	return (bool) apply_filters( 'floe_svg_uploads', $allowed, $user );
}

function upload_mimes( array $mimes, $user = null ): array {
	if ( can_upload( $user ) ) {
		$mimes['svg'] = MIME;
	}
	return $mimes;
}
add_filter( 'upload_mimes', __NAMESPACE__ . '\\upload_mimes', 10, 2 );

/** Clean an uploaded SVG in place before WordPress checks and moves it, or refuse it. */
function clean_upload( array $file ): array {
	if ( ! empty( $file['error'] ) || ! is_svg_name( (string) ( $file['name'] ?? '' ) ) ) {
		return $file;
	}
	$path  = (string) ( $file['tmp_name'] ?? '' );
	$svg   = is_readable( $path ) ? file_get_contents( $path ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- the uploaded temp file.
	$clean = false === $svg ? null : sanitize( $svg );
	if ( null === $clean || false === file_put_contents( $path, $clean ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$file['error'] = __( 'This SVG couldn’t be read safely. Export it again as a plain SVG from your design app, then upload that.', 'floe' );
		return $file;
	}
	$file['size'] = strlen( $clean );
	$file['type'] = MIME;
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\clean_upload' );
add_filter( 'wp_handle_sideload_prefilter', __NAMESPACE__ . '\\clean_upload' );

/**
 * An .svg file passes WordPress's file type check exactly when it's already
 * clean (clean_upload() has just cleaned it); any other .svg file fails.
 * Content sniffing alone doesn't always recognise SVG.
 */
function check_filetype( array $data, string $file, string $filename, $mimes ): array {
	if ( ! is_svg_name( $filename ) || ! in_array( MIME, (array) ( $mimes ? $mimes : get_allowed_mime_types() ), true ) ) {
		return $data;
	}
	$svg          = is_readable( $file ) ? file_get_contents( $file ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$clean        = false !== $svg && sanitize( $svg ) === $svg;
	$data['ext']  = $clean ? 'svg' : false;
	$data['type'] = $clean ? MIME : false;
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', __NAMESPACE__ . '\\check_filetype', 10, 4 );

/** wp_upload_bits() writes files without the upload filters, so it can't take SVGs. */
function refuse_bits( $upload ) {
	if ( is_array( $upload ) && is_svg_name( (string) ( $upload['name'] ?? '' ) ) ) {
		return __( 'Upload SVG files through the media library.', 'floe' );
	}
	return $upload;
}
add_filter( 'wp_upload_bits', __NAMESPACE__ . '\\refuse_bits' );

/** Save an SVG's width and height, so WordPress sizes it like any other image. */
function metadata( $metadata, int $attachment_id ) {
	if ( MIME !== get_post_mime_type( $attachment_id ) ) {
		return $metadata;
	}
	$file = get_attached_file( $attachment_id );
	$dom  = $file && is_readable( $file ) ? load( (string) file_get_contents( $file ) ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! $dom ) {
		return $metadata;
	}
	$metadata         = is_array( $metadata ) ? $metadata : array();
	$metadata['file'] = _wp_relative_upload_path( $file );
	list( $width, $height ) = dimensions( $dom->documentElement );
	if ( $width && $height ) {
		$metadata['width']  = $width;
		$metadata['height'] = $height;
	}
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\metadata', 10, 2 );

/** The image editor and the Customizer's cropper never open an SVG. */
function no_editing( $path, $attachment_id ) {
	return MIME === get_post_mime_type( (int) $attachment_id ) ? false : $path;
}
add_filter( 'load_image_to_edit_path', __NAMESPACE__ . '\\no_editing', 10, 2 );

/**
 * The attachment edit screen hides "Edit Image" when no image editor supports
 * the file; the media modal doesn't check, so hide it there for SVGs.
 */
function hide_edit_button(): void {
	wp_add_inline_style(
		'media-views',
		'.thumbnail:has(> .details-image[src$=".svg" i]) .edit-attachment,'
		. '.attachment-info:has(> .thumbnail img[src$=".svg" i]) .edit-attachment{display:none}'
	);
}
add_action( 'wp_enqueue_media', __NAMESPACE__ . '\\hide_edit_button' );

function is_svg_name( string $name ): bool {
	return 'svg' === strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
}

/**
 * Parse an SVG without reading from the network or expanding entities. Null
 * when it isn't a well-formed SVG document.
 */
function load( string $svg ): ?\DOMDocument {
	// A plain DOCTYPE (older Illustrator exports) is dropped. Entity
	// declarations, and UTF-16 files that could hide them, are refused.
	$svg = preg_replace( '/<!DOCTYPE[^>\[]*>/i', '', $svg );
	if ( ! is_string( $svg ) || '' === trim( $svg ) || str_contains( $svg, "\0" ) || false !== stripos( $svg, '<!DOCTYPE' ) || false !== stripos( $svg, '<!ENTITY' ) || ! class_exists( '\\DOMDocument' ) ) {
		return null;
	}
	$errors = libxml_use_internal_errors( true );
	$dom    = new \DOMDocument();
	$loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOCDATA );
	libxml_clear_errors();
	libxml_use_internal_errors( $errors );

	$root = $dom->documentElement;
	if ( ! $loaded || $dom->doctype || ! $root || 'svg' !== $root->localName || SVG_NS !== $root->namespaceURI ) {
		return null;
	}
	return $dom;
}

/** Rebuild an SVG from the allowlist: the cleaned file, or null if it can't be used. */
function sanitize( string $svg ): ?string {
	$dom = load( $svg );
	if ( ! $dom ) {
		return null;
	}
	// Comments and processing instructions (such as xml-stylesheet) around the root.
	foreach ( iterator_to_array( $dom->childNodes, false ) as $node ) {
		if ( $node !== $dom->documentElement ) {
			$dom->removeChild( $node );
		}
	}
	clean_attributes( $dom->documentElement );
	clean_children( $dom->documentElement );

	// Namespace declarations left behind by editors (Inkscape, Sodipodi…).
	$xpath = new \DOMXPath( $dom );
	foreach ( $xpath->query( '//*' ) as $element ) {
		foreach ( iterator_to_array( $xpath->query( 'namespace::*', $element ), false ) as $namespace ) {
			if ( ! in_array( $namespace->namespaceURI, array( SVG_NS, XLINK_NS, XML_NS ), true ) ) {
				$element->removeAttributeNS( $namespace->namespaceURI, $namespace->prefix );
			}
		}
	}

	$dom->encoding = 'UTF-8';
	$clean         = $dom->saveXML();
	return false === $clean ? null : $clean;
}

function clean_children( \DOMElement $parent ): void {
	foreach ( iterator_to_array( $parent->childNodes, false ) as $node ) {
		if ( $node instanceof \DOMText ) {
			continue;
		}
		if ( ! $node instanceof \DOMElement || SVG_NS !== $node->namespaceURI ) {
			$parent->removeChild( $node );
			continue;
		}
		$name = $node->localName;

		if ( 'a' === $name ) {
			clean_children( $node );
			while ( $node->firstChild ) {
				$parent->insertBefore( $node->firstChild, $node );
			}
			$parent->removeChild( $node );
			continue;
		}
		if ( ! in_array( $name, ELEMENTS, true ) ) {
			$parent->removeChild( $node );
			continue;
		}

		clean_attributes( $node );
		if ( 'style' === $name ) {
			$css = $node->textContent;
			while ( $node->firstChild ) {
				$node->removeChild( $node->firstChild );
			}
			if ( safe_css( $css ) ) {
				$node->appendChild( $node->ownerDocument->createTextNode( $css ) );
			} else {
				$parent->removeChild( $node );
			}
			continue;
		}
		// Nothing left to draw without a safe reference.
		if ( in_array( $name, array( 'use', 'image', 'feImage' ), true ) && ! $node->hasAttribute( 'href' ) && ! $node->hasAttributeNS( XLINK_NS, 'href' ) ) {
			$parent->removeChild( $node );
			continue;
		}
		clean_children( $node );
	}
}

function clean_attributes( \DOMElement $element ): void {
	foreach ( iterator_to_array( $element->attributes, false ) as $attribute ) {
		$name      = $attribute->localName;
		$namespace = $attribute->namespaceURI;
		if ( 'href' === $name && ( null === $namespace || XLINK_NS === $namespace ) ) {
			$keep = safe_href( $element->localName, $attribute->value );
		} elseif ( null === $namespace ) {
			$keep = in_array( $name, ATTRIBUTES, true ) && safe_css( $attribute->value );
		} else {
			$keep = XML_NS === $namespace && in_array( $name, array( 'space', 'lang' ), true ) && safe_css( $attribute->value );
		}
		if ( ! $keep ) {
			$element->removeAttributeNode( $attribute );
		}
	}
}

/** An #id in this file, or embedded raster data for <image> and <feImage>. */
function safe_href( string $element, string $href ): bool {
	$href = trim( $href );
	if ( in_array( $element, array( 'use', 'textPath', 'linearGradient', 'radialGradient', 'pattern', 'filter', 'feImage' ), true ) && preg_match( '/^#[^\s"\'()<>\\\\]+$/', $href ) ) {
		return true;
	}
	return in_array( $element, array( 'image', 'feImage' ), true ) && preg_match( '#^data:image/(?:png|jpe?g|gif|webp);base64,[a-z0-9+/=\s]+$#i', $href );
}

/**
 * CSS (a style attribute, a <style> element or a presentation attribute) that
 * can't load a file or run script: url() only to an #id in this file, and none
 * of the rules or functions that fetch files. Escapes could disguise any of
 * those, so CSS with a backslash is refused too.
 */
function safe_css( string $css ): bool {
	$css = strtolower( $css );
	if ( preg_match( '/[\\\\<]|@import|expression|javascript:|vbscript:|behavior|-moz-binding|image-set|(?:image|cross-fade|element|src)\s*\(|data:/', $css ) ) {
		return false;
	}
	return ! preg_match( '/url\s*\((?!\s*[\'"]?\s*#)/', $css );
}

/** Width and height in pixels from the root's width, height and viewBox; zeros when unknown. */
function dimensions( \DOMElement $svg ): array {
	$box    = preg_split( '/[\s,]+/', trim( $svg->getAttribute( 'viewBox' ) ) );
	$box_w  = 4 === count( (array) $box ) ? (float) $box[2] : 0.0;
	$box_h  = 4 === count( (array) $box ) ? (float) $box[3] : 0.0;
	$width  = length( $svg->getAttribute( 'width' ) );
	$height = length( $svg->getAttribute( 'height' ) );

	if ( $box_w > 0 && $box_h > 0 ) {
		if ( ! $width && ! $height ) {
			$width  = $box_w;
			$height = $box_h;
		} elseif ( ! $width ) {
			$width = $height * $box_w / $box_h;
		} elseif ( ! $height ) {
			$height = $width * $box_h / $box_w;
		}
	}
	return $width > 0 && $height > 0 ? array( (int) max( 1, round( $width ) ), (int) max( 1, round( $height ) ) ) : array( 0, 0 );
}

/** An absolute SVG length in pixels; 0 for percentages, font-relative units and anything else. */
function length( string $value ): float {
	$units = array(
		''   => 1,
		'px' => 1,
		'pt' => 4 / 3,
		'pc' => 16,
		'mm' => 96 / 25.4,
		'cm' => 96 / 2.54,
		'in' => 96,
	);
	return preg_match( '/^\s*(\d*\.?\d+(?:e[+-]?\d+)?)\s*(px|pt|pc|mm|cm|in)?\s*$/i', $value, $match ) ? (float) $match[1] * $units[ strtolower( $match[2] ?? '' ) ] : 0.0;
}
