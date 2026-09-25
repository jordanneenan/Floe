<?php
/**
 * Title: Pricing
 * Slug: floe/page-pricing
 * Categories: floe-pages
 * Description: The Floe pricing page: banner, package comparison, testimonials, FAQ and an Accent CTA.
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, details, table};

defined( 'ABSPATH' ) || exit;

echo block(
	'floe/page-banner',
	array(
		'heading'   => 'Simple packages, one platform',
		'intro'     => 'Every package is built on the same Floe sections, so you can start small and grow without a rebuild.',
		'link'      => link( 'Compare packages', '#compare' ),
		'showMedia' => false,
	)
);

echo block(
	'floe/table',
	array(
		'anchor'           => 'compare',
		'eyebrow'          => 'Compare',
		'heading'          => 'Choose the right starting point',
		'intro'            => 'Every package includes the full block library, hosting guidance and training.',
		'highlight'        => 2,
		'badge'            => 'Popular',
		'emphasiseLastRow' => true,
	),
	table(
		array(
			array( '', 'Launch', 'Studio', 'Partner' ),
			array( 'Pages at launch', 'Up to 8', 'Up to 20', 'Unlimited' ),
			array( 'Floe section library', '✓', '✓', '✓' ),
			array( 'Custom sections', '—', '2 included', 'As needed' ),
			array( 'Content entry', '—', '✓', '✓' ),
			array( 'Editor training', '1 session', '2 sessions', 'Ongoing' ),
			array( 'Support', 'Email', 'Priority email', 'Dedicated contact' ),
			array( 'From', '$9,000', '$18,000', 'Let’s talk' ),
		)
	)
);

echo block(
	'floe/testimonials',
	array(
		'eyebrow' => 'What clients say',
		'heading' => 'Teams who edit their own sites',
	),
	block( 'floe/quote', array( 'quote' => '“We publish weekly now. Before Floe, every new page was a small project.”', 'name' => 'Priya Shah', 'role' => 'Marketing lead, Halcyon' ) )
	. block( 'floe/quote', array( 'quote' => '“The editor only offers what fits the brand, so nothing looks off even when we move fast.”', 'name' => 'Tom Reeves', 'role' => 'Communications, Meridian' ) )
	. block( 'floe/quote', array( 'quote' => '“Launch took six weeks, and the site has grown by forty pages since without a redesign.”', 'name' => 'Hannah Cole', 'role' => 'Director, Arbor &amp; Co' ) )
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
	details( 'Are there ongoing licence fees?', 'No. Floe is open source under GPL-2.0. You pay for design, build and support, not for the platform.', true )
	. details( 'Can we move from Launch to Studio later?', 'Yes. Every package uses the same sections, so moving up adds pages and features without a rebuild.' )
	. details( 'Who hosts the site?', 'You own the site and choose the host. We can set it up with a host we recommend, or deploy to yours.' )
	. details( 'Can you add a custom section?', 'Yes. Custom sections are made the same way as the library: designed in Figma, built as a block and documented for editors.' )
	. details( 'What does training cover?', 'Adding pages, choosing and editing sections, managing media and keeping the site tidy. Sessions are recorded for new team members.' )
);

echo block(
	'floe/cta',
	array(
		'eyebrow' => 'Not sure which fits?',
		'heading' => 'Let’s find the right starting point',
		'body'    => 'Tell us about your organisation and we’ll recommend a package.',
		'action'  => link( 'Talk to us', '/contact/' ),
		'note'    => 'or email <a href="mailto:hello@floe.studio">hello@floe.studio</a>',
		'surface' => 'accent',
	)
);
