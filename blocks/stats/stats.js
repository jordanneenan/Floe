/*
 * Count up: in Stats sections with "Count up" on, each figure ticks up from
 * zero as it scrolls into view, keeping any prefix, suffix, thousands
 * separators and decimal places ("£1,200", "4.5", "24/7" counts the 24).
 *
 * Like the scroll reveals, only figures still below the fold are touched, so
 * nothing on the first screen flashes, and reduced motion leaves the numbers
 * as they are. The value keeps its final width while it counts so the unit
 * beside it doesn't shuffle along.
 */
const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );

const DURATION = 1600;
const NUMBER = /^(\D*?)(\d[\d,]*(?:\.\d+)?)(.*)$/s;

const easeOut = ( t ) => 1 - Math.pow( 1 - t, 4 );

function parse( text ) {
	const match = text.match( NUMBER );
	if ( ! match ) {
		return null;
	}
	const [ , prefix, number, suffix ] = match;
	const target = parseFloat( number.replace( /,/g, '' ) );
	if ( ! Number.isFinite( target ) || target === 0 ) {
		return null;
	}
	const decimals = ( number.split( '.' )[ 1 ] || '' ).length;
	const format = new Intl.NumberFormat( 'en-GB', {
		minimumFractionDigits: decimals,
		maximumFractionDigits: decimals,
		useGrouping: number.includes( ',' ),
	} );
	return { prefix, suffix, target, format, text };
}

function count( element, figure ) {
	const start = performance.now();
	const tick = ( now ) => {
		const progress = Math.min( 1, ( now - start ) / DURATION );
		if ( progress < 1 ) {
			element.textContent =
				figure.prefix +
				figure.format.format( figure.target * easeOut( progress ) ) +
				figure.suffix;
			window.requestAnimationFrame( tick );
		} else {
			element.textContent = figure.text;
			element.style.removeProperty( 'min-width' );
			element.removeAttribute( 'aria-hidden' );
			element.parentElement
				.querySelector( '.stat__count-label' )
				?.remove();
		}
	};
	window.requestAnimationFrame( tick );
}

if ( ! reduce.matches && 'IntersectionObserver' in window ) {
	const figures = new Map();
	const observer = new IntersectionObserver(
		( entries ) => {
			entries
				.filter( ( entry ) => entry.isIntersecting )
				.forEach( ( entry ) => {
					observer.unobserve( entry.target );
					count( entry.target, figures.get( entry.target ) );
				} );
		},
		{ rootMargin: '0px 0px -15% 0px' }
	);

	const fold = window.innerHeight;
	document
		.querySelectorAll( '.stats[data-count-up] .stat__value' )
		.forEach( ( element ) => {
			const figure = parse( element.textContent.trim() );
			if ( ! figure || element.getBoundingClientRect().top <= fold ) {
				return;
			}
			figures.set( element, figure );
			element.style.minWidth = `${ element.getBoundingClientRect().width }px`;
			// Screen readers get the real figure, not the ticking one.
			const label = document.createElement( 'span' );
			label.className = 'screen-reader-text stat__count-label';
			label.textContent = figure.text;
			element.after( label );
			element.setAttribute( 'aria-hidden', 'true' );
			element.textContent =
				figure.prefix + figure.format.format( 0 ) + figure.suffix;
			observer.observe( element );
		} );
}
