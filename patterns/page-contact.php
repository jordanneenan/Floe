<?php
/**
 * Title: Contact
 * Slug: floe/page-contact
 * Categories: floe-pages
 * Description: The Floe contact page: banner, contact details with a form slot, FAQ and newsletter.
 * Post Types: page
 * Viewport Width: 1440
 */

use function Floe\Config\Patterns\{block, link, details};

defined( 'ABSPATH' ) || exit;

echo block(
	'floe/page-banner',
	array(
		'heading'   => 'Talk to us about your next site',
		'intro'     => 'Whether you’re planning a new site or moving an old one to Floe, we’re happy to help.',
		'link'      => link( 'Book a walkthrough', '#contact' ),
		'showMedia' => false,
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
	'floe/faq',
	array(
		'eyebrow' => 'FAQ',
		'heading' => 'Questions, answered',
		'intro'   => 'Can’t find what you need? Our team is happy to help.',
		'action'  => link( 'Contact us', '#contact' ),
		'oneOpen' => true,
	),
	details( 'How soon can we start?', 'Most projects start within three to four weeks of a signed brief. We’ll confirm dates on our first call.', true )
	. details( 'Do you work with in-house teams?', 'Often. We can design and build the system and hand it over to your team, or work alongside your own developers.' )
	. details( 'Can you migrate our existing content?', 'Yes. We plan the move page by page, map old content to Floe sections and set up redirects so nothing is lost.' )
	. details( 'Do you offer ongoing support?', 'Yes. Support plans cover updates, monitoring and help for editors, with priority options for larger sites.' )
	. details( 'Where are you based?', 'Our studio is at 12 Example Street in Brisbane, and we work with organisations across Australia.' )
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
