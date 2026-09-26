/*
 * Scroll reveal. In every section of the page (banners and the sticky in-page
 * navigation excepted), the direct parts of the section's inner container
 * fade up as they come into view; lists and grids reveal their items
 * instead, so cards, steps, stats, logos and FAQs arrive one after another.
 *
 * Only elements still below the fold are hidden, so nothing that has already
 * painted blinks. Once an element is in place its data-reveal attribute is
 * removed and its own transitions (hovers etc.) apply again.
 */
const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );

const SECTIONS =
	'.site-main > .floe-section:not(.home-banner, .page-banner, .in-page-nav, .spacing)';
const GROUPS =
	'[class*="__grid"], [class*="__list"], [class*="__items"], [class*="__logos"], [class*="__files"], [class*="__rows"], .cta__inner';
const STAGGER = 90;
const MAX_STAGGER = 6;
const REVEAL_MS = 900; // --floe-reveal

const targets = () => {
	const found = [];
	document.querySelectorAll( SECTIONS ).forEach( ( section ) => {
		const inner =
			section.querySelector( ':scope > [class*="__inner"]' ) || section;
		const parts = inner.matches( GROUPS )
			? [ inner ]
			: [ ...inner.children ];
		parts.forEach( ( part ) => {
			if ( part.matches( GROUPS ) && part.children.length > 1 ) {
				found.push( ...part.children );
			} else if ( ! part.matches( '.screen-reader-text' ) ) {
				found.push( part );
			}
		} );
	} );
	return found;
};

const settle = ( element ) => {
	delete element.dataset.reveal;
	element.style.removeProperty( '--reveal-delay' );
};

if ( ! reduce.matches && 'IntersectionObserver' in window ) {
	const observer = new IntersectionObserver(
		( entries ) => {
			const entering = entries
				.filter( ( entry ) => entry.isIntersecting )
				.map( ( entry ) => entry.target )
				.sort(
					( a, b ) =>
						a.getBoundingClientRect().top -
							b.getBoundingClientRect().top ||
						a.getBoundingClientRect().left -
							b.getBoundingClientRect().left
				);
			entering.forEach( ( element, index ) => {
				observer.unobserve( element );
				element.style.setProperty(
					'--reveal-delay',
					`${ Math.min( index, MAX_STAGGER ) * STAGGER }ms`
				);
				element.dataset.reveal = 'in';
				// Hand the element back to its own transitions once it's in.
				window.setTimeout(
					() => settle( element ),
					Math.min( index, MAX_STAGGER ) * STAGGER + REVEAL_MS + 100
				);
			} );
		},
		{ rootMargin: '0px 0px -8% 0px' }
	);

	const fold = window.innerHeight;
	targets().forEach( ( element ) => {
		if ( element.getBoundingClientRect().top > fold ) {
			element.dataset.reveal = '';
			observer.observe( element );
		}
	} );
}
