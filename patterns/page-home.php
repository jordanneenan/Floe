<?php
/**
 * Title: Home
 * Slug: floe/page-home
 * Categories: floe-pages
 * Description: The Floe brochure home page: banner, logos, story, stats, how it works, testimonial, journal, FAQ and CTA.
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, media, details};

defined( 'ABSPATH' ) || exit;

echo block(
	'floe/home-banner',
	array(
		'eyebrow'         => 'Modular WordPress platform',
		'heading'         => 'WordPress, built from sections that last.',
		'body'            => 'Floe gives every page a designed starting point and every editor a simple way to keep it current. Native WordPress, no page builder.',
		'primaryAction'   => link( 'Explore the platform', '/platform/' ),
		'secondaryAction' => link( 'See pricing', '/pricing/' ),
		'media'           => media( 'floe-hero' ),
	)
);

$logos = array();
foreach ( array( 'northwind', 'halcyon', 'meridian', 'arbor-and-co', 'kestrel', 'lumen' ) as $logo ) {
	if ( media( 'logo-' . $logo ) ) {
		$logos[] = media( 'logo-' . $logo );
	}
}
echo block(
	'floe/logo-strip',
	array(
		'label' => 'Powering sites for',
		'logos' => $logos,
	)
);

echo block(
	'floe/image-copy',
	array(
		'eyebrow' => 'Why Floe',
		'heading' => 'Designed sections, not a blank canvas',
		'body'    => 'Editors choose a section, add their words and images, and get a layout that already fits the brand. Designers set the system once, through tokens.',
		'action'  => link( 'How it works', '/platform/' ),
		'media'   => media( 'floe-dawn' ),
	)
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
	'floe/cards',
	array(
		'eyebrow' => 'How it works',
		'heading' => 'Three layers, one platform',
		'intro'   => 'Each layer can change without breaking the others, so sites stay easy to update for years.',
		'style'   => 'feature',
	),
	block( 'floe/card-item', array( 'title' => 'Native WordPress', 'text' => 'Built on the block editor, theme.json and core features. No page builder and no lock-in.', 'link' => link( 'Learn more', '/platform/' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Designed sections', 'text' => 'Over twenty sections cover banners, content, proof, media and conversion.', 'link' => link( 'Learn more', '/platform/#sections' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Brand tokens', 'text' => 'Colour, type, radius and spacing live in tokens, so every client site feels its own.', 'link' => link( 'Learn more', '/platform/' ) ) )
);

echo block(
	'floe/testimonial',
	array(
		'quote' => 'Floe gave us a site our whole team can update without breaking the design. We launched in six weeks and have added forty pages since.',
		'name'  => 'Hannah Cole',
		'role'  => 'Director, Arbor &amp; Co',
	)
);

echo block(
	'floe/posts',
	array(
		'eyebrow' => 'From the journal',
		'heading' => 'Notes on building better websites',
		'action'  => link( 'Read the journal', '/journal/' ),
		'count'   => 3,
	)
);

echo block(
	'floe/faq',
	array(
		'eyebrow' => 'FAQ',
		'heading' => 'Questions, answered',
		'intro'   => 'Can’t find what you need? Our team is happy to help.',
		'action'  => link( 'Contact us', '/contact/' ),
		'oneOpen' => true,
	),
	details( 'How long does a Floe site take to build?', 'Most brochure sites launch in four to eight weeks. Because the sections already exist, time goes into content, imagery and the brand rather than rebuilding layouts.', true )
	. details( 'Can our team edit the site without a developer?', 'Yes. Editors work in the WordPress block editor, choosing sections and filling them in. The design system keeps every page on brand, so there’s nothing to break.' )
	. details( 'Is Floe tied to a particular host?', 'No. Floe is a standard WordPress theme, so it runs on any good WordPress host. We’re happy to recommend one.' )
	. details( 'What happens if we change theme in future?', 'Your content stays in WordPress. Anything that should outlive a theme change lives in native WordPress features or a small plugin.' )
	. details( 'Do you support accessibility standards?', 'Yes. Sections are built to WCAG 2.2 AA, with strong contrast, keyboard access, visible focus and support for reduced motion.' )
);

echo block(
	'floe/cta',
	array(
		'eyebrow' => 'Get started',
		'heading' => 'Start your next site on Floe',
		'body'    => 'Book a 30-minute walkthrough of the platform and block library.',
		'action'  => link( 'Book a walkthrough', '/contact/' ),
		'note'    => 'or email <a href="mailto:hello@floe.studio">hello@floe.studio</a>',
		'surface' => 'inverse',
	)
);
