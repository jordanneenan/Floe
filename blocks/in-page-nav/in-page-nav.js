/*
 * Highlight the in-page link for the section currently in view, and keep the
 * active pill scrolled into view on narrow screens.
 *
 * Once the bar sticks, it shrinks over the next SHRINK_OVER pixels of
 * scrolling: --in-page-nav-progress goes from 0 to 1 and in-page-nav.scss
 * turns it into padding.
 */
const SHRINK_OVER = 150;

document.querySelectorAll( '.in-page-nav' ).forEach( ( nav ) => {
	const links = [ ...nav.querySelectorAll( '.in-page-nav__link' ) ];
	const sections = links.map( ( link ) =>
		document.getElementById( decodeURIComponent( link.hash.slice( 1 ) ) )
	);
	const list = nav.querySelector( '.in-page-nav__list' );
	let current = null;

	const setActive = ( index ) => {
		if ( index === current ) {
			return;
		}
		current = index;
		links.forEach( ( link, i ) => {
			const active = i === index;
			link.classList.toggle( 'is-active', active );
			if ( active ) {
				link.setAttribute( 'aria-current', 'true' );
				if ( list && list.scrollWidth > list.clientWidth ) {
					list.scrollTo( {
						left: link.offsetLeft - 16,
						behavior: 'smooth',
					} );
				}
			} else {
				link.removeAttribute( 'aria-current' );
			}
		} );
	};

	// How far the page has scrolled since the bar stuck. Unstuck, the bar
	// would sit right after the block before it (or at the top of its
	// parent), which doesn't move while the bar shrinks.
	let progress = null;
	const shrink = () => {
		const before = nav.previousElementSibling;
		const home = before
			? before.getBoundingClientRect().bottom +
				parseFloat( window.getComputedStyle( before ).marginBottom )
			: nav.parentElement.getBoundingClientRect().top;
		const stuckFor =
			parseFloat( window.getComputedStyle( nav ).top ) - home;
		const next =
			Math.round(
				Math.min( Math.max( stuckFor / SHRINK_OVER, 0 ), 1 ) * 1000
			) / 1000;
		if ( next !== progress ) {
			progress = next;
			nav.style.setProperty( '--in-page-nav-progress', next );
		}
	};

	const update = () => {
		shrink();
		const offset = nav.getBoundingClientRect().bottom + 24;
		let index = -1;
		sections.forEach( ( section, i ) => {
			if ( section && section.getBoundingClientRect().top <= offset ) {
				index = i;
			}
		} );
		setActive( index );
	};

	let ticking = false;
	window.addEventListener(
		'scroll',
		() => {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( () => {
					update();
					ticking = false;
				} );
			}
		},
		{ passive: true }
	);
	window.addEventListener( 'resize', update );
	update();
} );
