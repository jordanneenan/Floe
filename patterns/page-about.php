<?php
/**
 * Title: About
 * Slug: floe/page-about
 * Categories: floe-pages
 * Description: The Floe about page: banner, story, stats, team, studio images, testimonial and CTA.
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, media, heading, paragraph, items, pullquote};

defined( 'ABSPATH' ) || exit;

echo block(
	'floe/page-banner',
	array(
		'heading' => 'Built from years of agency websites',
		'intro'   => 'Floe distils what worked across a decade of client builds into a platform any team can use.',
		'link'    => link( 'Meet the team', '#team' ),
		'media'   => media( 'floe-dawn' ),
	)
);

echo block(
	'floe/article',
	array( 'action' => link( 'See the platform', '/platform/' ) ),
	heading( 'Why we built Floe' )
	. paragraph( 'Every agency site we built started the same way: a blank theme, a new set of layouts, and a client who would soon need a page nobody had designed. Floe replaces that with a library of sections that already work.', 'lead' )
	. paragraph( 'It began as an internal framework called Made. Floe is its successor: rebuilt around native WordPress, open source, and designed so each client’s brand does the talking.' )
	. heading( 'What we believe', 3 )
	. items( array( 'Native WordPress first', 'Structure is designed once', 'Brands change tokens, not layouts', 'Editors should never break a page' ) )
	. pullquote( '“The best website is the one your team can keep improving.”', 'Jordan Neenan, Founder' )
	. block( 'floe/media', array( 'media' => media( 'floe-drift' ), 'caption' => 'The Floe studio in Brisbane.' ) )
	. paragraph( 'Today Floe powers brochure sites, service sites and publications, each one looking entirely its own.' )
);

echo block(
	'floe/stats',
	array(
		'eyebrow' => 'By the numbers',
		'heading' => 'Built to be used, not just launched',
		'intro'   => 'Figures from sites built on Floe over the last two years.',
	),
	block( 'floe/stat', array( 'value' => '40', 'unit' => '+', 'label' => 'Sites launched on the platform' ) )
	. block( 'floe/stat', array( 'value' => '6', 'unit' => 'wks', 'label' => 'Typical time from brief to launch' ) )
	. block( 'floe/stat', array( 'value' => '100', 'unit' => '%', 'label' => 'Pages edited by client teams' ) )
	. block( 'floe/stat', array( 'value' => '98', 'label' => 'Average Lighthouse performance score' ) )
);

echo block(
	'floe/team',
	array(
		'anchor'  => 'team',
		'eyebrow' => 'Our team',
		'heading' => 'The people behind the platform',
		'intro'   => 'A small studio of designers and developers, based in Brisbane.',
	),
	block( 'floe/person', array( 'name' => 'Jordan Neenan', 'role' => 'Founder, Digital director' ) )
	. block( 'floe/person', array( 'name' => 'Sam Carter', 'role' => 'Lead designer' ) )
	. block( 'floe/person', array( 'name' => 'Riley Nguyen', 'role' => 'WordPress developer' ) )
	. block( 'floe/person', array( 'name' => 'Morgan Lee', 'role' => 'Client success' ) )
);

$row = static function ( array $names ): string {
	$inner = '';
	foreach ( $names as $name ) {
		$inner .= block( 'floe/media', array( 'media' => media( $name ) ) );
	}
	return block( 'floe/image-row', array(), $inner );
};
echo block(
	'floe/images',
	array(
		'eyebrow' => 'Studio',
		'heading' => 'Where the work happens',
	),
	$row( array( 'floe-seam' ) ) . $row( array( 'floe-blue', 'floe-giant' ) ) . $row( array( 'floe-pack-teal', 'floe-dusk', 'floe-hero' ) )
);

echo block(
	'floe/testimonial',
	array(
		'quote' => 'Working with the Floe team felt like having our own digital department. The platform meant we spent our budget on content, not rebuilding layouts.',
		'name'  => 'Priya Shah',
		'role'  => 'Marketing lead, Halcyon',
	)
);

echo block(
	'floe/cta',
	array(
		'eyebrow' => 'Work with us',
		'heading' => 'Let’s build your next site together',
		'body'    => 'We take on a small number of new projects each quarter.',
		'action'  => link( 'Start a project', '/contact/' ),
		'note'    => 'or email <a href="mailto:hello@floe.studio">hello@floe.studio</a>',
	)
);
