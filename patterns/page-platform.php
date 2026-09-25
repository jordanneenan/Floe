<?php
/**
 * Title: Platform
 * Slug: floe/page-platform
 * Categories: floe-pages
 * Description: The Floe platform page: banner, in-page navigation, overview article, section library, editing, process, video, resources and newsletter.
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, media, heading, paragraph, items, pullquote};

defined( 'ABSPATH' ) || exit;

echo block(
	'floe/page-banner',
	array(
		'heading' => 'The Floe platform',
		'intro'   => 'A library of designed sections on native WordPress, with brand tokens that make every site its own.',
		'link'    => link( 'View the sections', '#sections' ),
		'media'   => media( 'floe-seam' ),
	)
);

echo block(
	'floe/in-page-nav',
	array(
		'label'  => 'On this page',
		'items'  => array(
			array( 'label' => 'Overview', 'anchor' => 'overview' ),
			array( 'label' => 'Sections', 'anchor' => 'sections' ),
			array( 'label' => 'Editing', 'anchor' => 'editing' ),
			array( 'label' => 'Process', 'anchor' => 'process' ),
			array( 'label' => 'Resources', 'anchor' => 'resources' ),
		),
		'action' => link( 'Book a walkthrough', '/contact/' ),
	)
);

echo block(
	'floe/article',
	array(
		'anchor' => 'overview',
		'action' => link( 'See the sections', '#sections' ),
	),
	heading( 'Everything is a section' )
	. paragraph( 'Floe is built on one idea from years of agency work: pages are made of sections, and every section should arrive designed. Editors pick what the page needs and fill it in; the layout, spacing and responsive behaviour are already solved.', 'lead' )
	. paragraph( 'Anything that can be native WordPress is. What can’t be native lives in the theme, and anything that should outlive a theme change becomes a small, focused plugin.' )
	. heading( 'What you get', 3 )
	. items( array( 'Over twenty designed sections', 'Brand tokens for colour, type and spacing', 'Wireframes, designs and code that match', 'Editing guidance for every section' ) )
	. pullquote( '“Design the structure once. Let each brand do the rest.”', 'Floe principle' )
	. block( 'floe/media', array( 'media' => media( 'floe-blue' ), 'caption' => 'Every Floe section has a wireframe, a design and a WordPress block.' ) )
	. paragraph( 'The result is a site that looks designed on launch day and still looks designed two years and a hundred pages later.' )
);

echo block(
	'floe/cards',
	array(
		'anchor'  => 'sections',
		'eyebrow' => 'The library',
		'heading' => 'Sections for every job',
		'intro'   => 'Mix and match on any page. Each one is responsive, accessible and edited in place.',
		'style'   => 'media',
	),
	block( 'floe/card-item', array( 'title' => 'Openings and content', 'text' => 'Home and page banners, article, image and copy, cards and steps.', 'media' => media( 'floe-giant' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Proof and people', 'text' => 'Stats, logo strip, testimonials, team and posts.', 'media' => media( 'floe-pack-teal' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Media and conversion', 'text' => 'Images, video, downloads, contact, newsletter and calls to action.', 'media' => media( 'floe-dusk' ) ) )
);

echo block(
	'floe/image-copy',
	array(
		'anchor'        => 'editing',
		'eyebrow'       => 'Editing',
		'heading'       => 'Editing that stays on brand',
		'body'          => 'Clients edit words and images directly on the page. Controls only offer choices that fit the design system, so updates never drift.',
		'action'        => link( 'Watch the demo', '#video' ),
		'media'         => media( 'floe-hero' ),
		'mediaPosition' => 'right',
	)
);

echo block(
	'floe/steps',
	array(
		'anchor'  => 'process',
		'eyebrow' => 'Process',
		'heading' => 'From brief to launch in four steps',
		'action'  => link( 'See the full process', '/about/' ),
	),
	block( 'floe/step', array( 'title' => 'Discover', 'text' => 'We agree goals, audiences and the pages the site needs.' ) )
	. block( 'floe/step', array( 'title' => 'Design', 'text' => 'Brand tokens, type and imagery are applied to the Floe sections.' ) )
	. block( 'floe/step', array( 'title' => 'Build', 'text' => 'Content is entered with real blocks, reviewed in place.' ) )
	. block( 'floe/step', array( 'title' => 'Launch', 'text' => 'Your team is trained, and the site goes live.' ) )
);

echo block(
	'floe/video',
	array(
		'anchor'   => 'video',
		'eyebrow'  => 'Video',
		'heading'  => 'See how a Floe site comes together',
		'intro'    => 'The player only loads when a visitor presses play, so the page stays fast and no YouTube cookies are set in advance.',
		'cover'    => media( 'floe-dawn' ),
		'duration' => '2:14',
	)
);

$files = '';
foreach ( array( 'floe-overview', 'section-library-reference', 'editor-guide', 'brand-token-template' ) as $file ) {
	$files .= block( 'floe/download', array( 'file' => media( $file ) ) );
}
echo block(
	'floe/document-download',
	array(
		'anchor'  => 'resources',
		'eyebrow' => 'Resources',
		'heading' => 'Documents to take away',
		'intro'   => 'Reports, guides and forms, ready to download. File type and size are added automatically.',
	),
	$files
);

echo block(
	'floe/newsletter',
	array(
		'eyebrow' => 'Newsletter',
		'heading' => 'Notes from the studio',
		'intro'   => 'One short email a month on building better websites. No spam, unsubscribe any time.',
		'note'    => 'We’ll only use your email to send the newsletter.',
		'action'  => link( 'Subscribe', 'mailto:hello@floe.studio?subject=Newsletter' ),
	)
);
