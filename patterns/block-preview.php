<?php
/**
 * Title: Block Preview
 * Slug: floe/block-preview
 * Categories: floe-pages
 * Description: Every Floe section with sample content, for checking the library. Not a real page: it has two H1s (both banners).
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, media, heading, paragraph, items, pullquote, details, table};

defined( 'ABSPATH' ) || exit;

$logos = array();
foreach ( array( 'northwind', 'halcyon', 'meridian', 'arbor-and-co', 'kestrel', 'lumen' ) as $logo ) {
	if ( media( 'logo-' . $logo ) ) {
		$logos[] = media( 'logo-' . $logo );
	}
}

$files = '';
foreach ( array( 'floe-overview', 'section-library-reference', 'editor-guide', 'brand-token-template' ) as $file ) {
	$files .= block( 'floe/download', array( 'file' => media( $file ) ) );
}

$row = static function ( array $names ): string {
	$inner = '';
	foreach ( $names as $name ) {
		$inner .= block( 'floe/media', array( 'media' => media( $name ) ) );
	}
	return block( 'floe/image-row', array(), $inner );
};

echo block(
	'floe/home-banner',
	array(
		'eyebrow'         => 'Modular WordPress, built to last',
		'heading'         => 'Websites that stay clear as they grow.',
		'body'            => 'Purpose-built sections give every page a confident starting point, and every editor a simple way to keep it current.',
		'primaryAction'   => link( 'Explore the blocks', '#sections' ),
		'secondaryAction' => link( 'See our work', '/about/' ),
		'media'           => media( 'floe-hero' ),
	)
);

echo block( 'floe/in-page-nav', array( 'label' => 'On this page', 'action' => link( 'Get started', '/contact/' ) ) );

echo block( 'floe/logo-strip', array( 'label' => 'Trusted by teams at', 'logos' => $logos ) );

echo block(
	'floe/page-banner',
	array(
		'heading' => 'Strategy and planning',
		'intro'   => 'A clear plan for what each page needs to do, who it serves and how it will be kept up to date.',
		'link'    => link( 'Talk to us', '/contact/' ),
		'media'   => media( 'floe-dawn' ),
	)
);

echo block(
	'floe/article',
	array(
		'anchor' => 'article',
		'action' => link( 'Optional button', '/contact/' ),
	),
	heading( 'Your story, in your words' )
	. paragraph( 'This area is a full WordPress editor. Headings, paragraphs, lists, quotes, images and links all use native blocks, styled to sit comfortably inside the Floe system.', 'lead' )
	. paragraph( 'Paragraphs keep a readable measure of around 70 characters. Links are underlined in the accent colour so they stand out without shouting, like <a href="#article">this example link</a>, and remain clear on every surface.' )
	. heading( 'What the section supports', 3 )
	. items( array( 'Headings at levels two to four', 'Ordered and unordered lists', 'Pull quotes and inline images', 'An optional button underneath' ) )
	. pullquote( '“An editorial moment can sit naturally within the article.”', 'Alex Morgan, Director' )
	. block( 'floe/media', array( 'media' => media( 'floe-drift' ), 'caption' => 'Captions sit quietly under the image in the small style.' ) )
	. paragraph( 'The layout keeps a readable measure while leaving the content flexible. Everything inside this column is edited with core blocks, so clients never meet an unfamiliar interface.' )
);

echo block(
	'floe/image-copy',
	array(
		'anchor'  => 'image-copy',
		'eyebrow' => 'Image + Copy',
		'heading' => 'A section with room to breathe',
		'body'    => 'Put the visual and the message together. Reverse the layout when the page needs a different rhythm, or swap the image for a silent looping video.',
		'action'  => link( 'Learn more', '/platform/' ),
		'media'   => media( 'floe-seam' ),
	)
);

echo block(
	'floe/cta',
	array(
		'eyebrow' => 'Ready when you are',
		'heading' => 'Make the next step clear',
		'body'    => 'A short, focused invitation keeps the choice simple.',
		'action'  => link( 'Start a project', '/contact/' ),
		'note'    => 'or email <a href="mailto:hello@studio.com">hello@studio.com</a>',
	)
);

echo block( 'floe/spacing', array( 'size' => 'small' ) );

echo block(
	'floe/cta',
	array(
		'eyebrow' => 'Accent surface',
		'heading' => 'Make the next step clear',
		'body'    => 'The same panel on the blue Accent surface.',
		'action'  => link( 'Start a project', '/contact/' ),
		'surface' => 'accent',
	)
);

echo block(
	'floe/testimonial',
	array(
		'quote' => 'A flexible system gives us consistency without making every page feel the same. Our team updates the site weekly and it still looks designed.',
		'name'  => 'Alex Morgan',
		'role'  => 'Director, Client Organisation',
	)
);

echo block(
	'floe/posts',
	array(
		'anchor'  => 'posts',
		'eyebrow' => 'Latest posts',
		'heading' => 'Ideas and updates',
		'action'  => link( 'View all posts', '/journal/' ),
	)
);

echo block(
	'floe/cards',
	array(
		'anchor'  => 'sections',
		'eyebrow' => 'How we work',
		'heading' => 'A simple process, done properly',
		'intro'   => 'Manually entered cards for services, benefits or steps. Add as many as the page needs.',
	),
	block( 'floe/card-item', array( 'title' => 'Plan the structure', 'text' => 'Sections are chosen for the job each page needs to do, not assembled from scratch.', 'link' => link( 'Learn more', '/platform/' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Design the system', 'text' => 'Typography, colour and imagery carry the brand across every section.', 'link' => link( 'Learn more', '/platform/' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Launch and grow', 'text' => 'Editors add pages confidently, and the site stays consistent as it grows.', 'link' => link( 'Learn more', '/platform/' ) ) )
);

echo block(
	'floe/cards',
	array(
		'eyebrow' => 'Services',
		'heading' => 'What we can help with',
		'intro'   => 'Manually entered cards for services, benefits or steps. Add as many as the page needs.',
		'style'   => 'media',
	),
	block( 'floe/card-item', array( 'title' => 'Strategy', 'text' => 'Clarify the purpose, audience and structure of every page.', 'media' => media( 'floe-hero' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Design', 'text' => 'A brand-led system applied through tokens, type and imagery.', 'media' => media( 'floe-dawn' ) ) )
	. block( 'floe/card-item', array( 'title' => 'Build', 'text' => 'A fast, accessible WordPress site your team can maintain.', 'media' => media( 'floe-drift' ) ) )
);

echo block(
	'floe/document-download',
	array(
		'anchor'  => 'downloads',
		'eyebrow' => 'Resources',
		'heading' => 'Documents to take away',
		'intro'   => 'Reports, guides and forms, ready to download. File type and size are added automatically.',
	),
	$files
);

echo block(
	'floe/images',
	array(
		'anchor'  => 'gallery',
		'eyebrow' => 'Gallery',
		'heading' => 'Places we have shaped',
	),
	$row( array( 'floe-giant' ) ) . $row( array( 'floe-blue', 'floe-pack-teal' ) ) . $row( array( 'floe-hero', 'floe-dusk', 'floe-seam' ) )
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

echo block(
	'floe/faq',
	array(
		'anchor'  => 'faq',
		'eyebrow' => 'FAQ',
		'heading' => 'Questions, answered',
		'intro'   => 'Can’t find what you need? Our team is happy to help.',
		'action'  => link( 'Contact us', '/contact/' ),
		'oneOpen' => true,
		'schema'  => true,
	),
	details( 'How long does a Floe site take to build?', 'Most brochure sites launch in four to eight weeks. Because the sections already exist, time goes into content, imagery and the brand rather than rebuilding layouts.', true )
	. details( 'Can our team edit the site without a developer?', 'Yes. Editors choose sections and fill them in; the design system keeps every page on brand.' )
	. details( 'Is Floe tied to a particular host?', 'No. Floe is a standard WordPress theme and runs on any good WordPress host.' )
);

echo block(
	'floe/stats',
	array(
		'anchor'  => 'stats',
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
	'floe/steps',
	array(
		'anchor'  => 'steps',
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
	'floe/testimonials',
	array(
		'anchor'  => 'testimonials',
		'eyebrow' => 'What clients say',
		'heading' => 'Teams who edit their own sites',
	),
	block( 'floe/quote', array( 'quote' => '“We publish weekly now. Before Floe, every new page was a small project.”', 'name' => 'Priya Shah', 'role' => 'Marketing lead, Halcyon' ) )
	. block( 'floe/quote', array( 'quote' => '“The editor only offers what fits the brand, so nothing looks off even when we move fast.”', 'name' => 'Tom Reeves', 'role' => 'Communications, Meridian' ) )
	. block( 'floe/quote', array( 'quote' => '“Launch took six weeks, and the site has grown by forty pages since without a redesign.”', 'name' => 'Hannah Cole', 'role' => 'Director, Arbor &amp; Co' ) )
	. block( 'floe/quote', array( 'quote' => '“A fourth quote turns the grid into a slider with previous and next buttons.”', 'name' => 'Alex Morgan', 'role' => 'Director, Client Organisation' ) )
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

echo block(
	'floe/table',
	array(
		'anchor'           => 'table',
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
			array( 'From', '$9,000', '$18,000', 'Let’s talk' ),
		)
	)
);

echo block(
	'floe/contact',
	array(
		'anchor'       => 'contact',
		'eyebrow'      => 'Contact',
		'heading'      => 'Let’s talk about your site',
		'intro'        => 'Tell us what you’re planning and we’ll reply within one working day.',
		'email'        => 'hello@floe.studio',
		'phone'        => '+61 7 3000 0000',
		'addressLabel' => 'Studio',
		'address'      => '12 Example Street, Brisbane QLD 4000',
		'hours'        => 'Monday to Friday, 9am to 5pm',
	)
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
