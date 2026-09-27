/*
 * Carousel: Floe's own, no library. Two modes, set by data-carousel:
 *
 *   slides  The track scrolls sideways with CSS scroll snapping, so swipe,
 *           trackpad and keyboard scrolling work natively. The buttons and
 *           the arrow keys step one slide; the buttons disable at either end
 *           and hide when every slide already fits. Each slide is labelled
 *           "2 of 6" for screen readers.
 *   ticker  The items are repeated to fill the width and slide past on a
 *           loop. Hovering or focusing pauses it; the pause button stops it
 *           until pressed again. Reduced motion leaves the items still.
 *
 * Carousels in the page start on load. Scripts that add one later call
 * window.floe.carousel.init( element ); goTo( element, index, smooth )
 * moves a slides carousel to a slide.
 */
const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );

// ---------------------------------------------------------------------------
// Slides
// ---------------------------------------------------------------------------
const slidesOf = ( track ) => [ ...track.children ];

function step( track ) {
	const slide = track.firstElementChild;
	const gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
	return slide
		? slide.getBoundingClientRect().width + gap
		: track.clientWidth;
}

function current( track ) {
	return Math.round( track.scrollLeft / ( step( track ) || 1 ) );
}

function goTo( carousel, index, smooth = true ) {
	const track = carousel.querySelector( '.carousel__track' );
	if ( ! track ) {
		return;
	}
	const last = slidesOf( track ).length - 1;
	const target = Math.max( 0, Math.min( last, index ) );
	track.scrollTo( {
		left: target * step( track ),
		behavior: smooth && ! reduce.matches ? 'smooth' : 'instant',
	} );
}

function initSlides( carousel ) {
	const track = carousel.querySelector( '.carousel__track' );
	if ( ! track ) {
		return;
	}
	const prev = carousel.querySelector( '.carousel__prev' );
	const next = carousel.querySelector( '.carousel__next' );

	const label = () => {
		const slides = slidesOf( track );
		slides.forEach( ( slide, index ) => {
			if ( slide.matches( 'li' ) ) {
				return;
			}
			slide.setAttribute( 'role', 'group' );
			slide.setAttribute( 'aria-roledescription', 'slide' );
			slide.setAttribute(
				'aria-label',
				`${ index + 1 } of ${ slides.length }`
			);
		} );
	};

	const update = () => {
		const max = track.scrollWidth - track.clientWidth;
		carousel.classList.toggle( 'carousel--static', max <= 2 );
		if ( prev && next ) {
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max - 2;
		}
		carousel.dispatchEvent(
			new CustomEvent( 'floe:carousel', {
				detail: {
					index: current( track ),
					count: slidesOf( track ).length,
				},
			} )
		);
	};

	prev?.addEventListener( 'click', () =>
		goTo( carousel, current( track ) - 1 )
	);
	next?.addEventListener( 'click', () =>
		goTo( carousel, current( track ) + 1 )
	);
	track.addEventListener( 'keydown', ( event ) => {
		if ( event.target !== track ) {
			return;
		}
		const move = { ArrowLeft: -1, ArrowRight: 1 }[ event.key ];
		if ( move ) {
			event.preventDefault();
			goTo( carousel, current( track ) + move );
		} else if ( event.key === 'Home' || event.key === 'End' ) {
			event.preventDefault();
			goTo(
				carousel,
				event.key === 'Home' ? 0 : slidesOf( track ).length - 1
			);
		}
	} );
	track.addEventListener(
		'scroll',
		() => window.requestAnimationFrame( update ),
		{ passive: true }
	);
	new window.ResizeObserver( update ).observe( track );
	new window.MutationObserver( () => {
		label();
		update();
	} ).observe( track, { childList: true } );

	label();
	update();
}

// ---------------------------------------------------------------------------
// Ticker
// ---------------------------------------------------------------------------
function initTicker( carousel ) {
	const viewport = carousel.querySelector( '.carousel__viewport' );
	const track = carousel.querySelector( '.carousel__track' );
	if ( ! viewport || ! track || ! track.children.length ) {
		return;
	}
	const pause = carousel.querySelector( '.carousel__pause' );
	const originals = [ ...track.children ];
	const speed =
		parseFloat(
			getComputedStyle( carousel ).getPropertyValue( '--carousel-speed' )
		) || 40;

	const clear = () =>
		track
			.querySelectorAll( '[data-carousel-clone]' )
			.forEach( ( clone ) => clone.remove() );

	const build = () => {
		clear();
		if ( reduce.matches ) {
			carousel.classList.remove( 'is-running' );
			pause.hidden = true;
			return;
		}
		carousel.classList.add( 'is-running' );
		// Repeat the items until one run of them is at least as wide as the
		// viewport, then add a second, identical run: the track slides by
		// exactly one run and starts again, so the loop never shows a join.
		const gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
		const set = track.getBoundingClientRect().width + gap;
		if ( set <= gap ) {
			return;
		}
		const copies = Math.min(
			12,
			Math.max( 1, Math.ceil( viewport.clientWidth / set ) )
		);
		for ( let run = 1; run < copies * 2; run++ ) {
			originals.forEach( ( item ) => {
				const clone = item.cloneNode( true );
				clone.setAttribute( 'data-carousel-clone', '' );
				clone.setAttribute( 'aria-hidden', 'true' );
				clone.inert = true;
				track.append( clone );
			} );
		}
		const distance = set * copies;
		carousel.style.setProperty( '--carousel-distance', `${ -distance }px` );
		carousel.style.setProperty(
			'--carousel-duration',
			`${ distance / speed }s`
		);
		pause.hidden = false;
	};

	pause.addEventListener( 'click', () => {
		const paused = pause.getAttribute( 'aria-pressed' ) !== 'true';
		pause.setAttribute( 'aria-pressed', String( paused ) );
		carousel.classList.toggle( 'is-paused', paused );
	} );

	let width = viewport.clientWidth;
	new window.ResizeObserver( () => {
		if ( Math.abs( viewport.clientWidth - width ) > 1 ) {
			width = viewport.clientWidth;
			build();
		}
	} ).observe( viewport );
	reduce.addEventListener?.( 'change', build );

	build();
}

// ---------------------------------------------------------------------------
function init( carousel ) {
	if ( ! carousel || carousel.dataset.carouselReady ) {
		return;
	}
	carousel.dataset.carouselReady = '';
	if ( carousel.dataset.carousel === 'ticker' ) {
		initTicker( carousel );
	} else {
		initSlides( carousel );
	}
}

window.floe = window.floe || {};
window.floe.carousel = { init, goTo };

document.querySelectorAll( '[data-carousel]' ).forEach( init );
