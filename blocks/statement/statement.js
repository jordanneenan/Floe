/*
 * Light up as you scroll: in Statements with the setting on, each word starts
 * dim and lights up in reading order as the statement moves up the screen,
 * from when its top passes 85% of the window to when its bottom passes 60%.
 * It's tied to the scroll position, so scrolling back up dims the words
 * again.
 *
 * The words are wrapped in spans here, keeping any bold, italic or link
 * around them, so the page's markup stays plain text for search engines and
 * without JavaScript. One custom property per frame drives every word (see
 * statement.scss). A statement near the end of a page that can't scroll far
 * enough still finishes lit, and reduced motion leaves it as it is.
 */
const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );

const START = 0.85;
const END = 0.6;

// Wrap each word of every text node in a span, leaving the spaces between
// them (and any inline formatting) where they are. Returns the count.
function split( text ) {
	const walker = document.createTreeWalker( text, NodeFilter.SHOW_TEXT );
	const nodes = [];
	while ( walker.nextNode() ) {
		nodes.push( walker.currentNode );
	}
	let index = 0;
	nodes.forEach( ( node ) => {
		const fragment = document.createDocumentFragment();
		node.textContent.split( /(\s+)/ ).forEach( ( part ) => {
			if ( ! part ) {
				return;
			}
			if ( /^\s+$/.test( part ) ) {
				fragment.append( part );
				return;
			}
			const word = document.createElement( 'span' );
			word.className = 'statement__word';
			word.style.setProperty( '--i', index++ );
			word.textContent = part;
			fragment.append( word );
		} );
		node.replaceWith( fragment );
	} );
	return index;
}

function fill( text, count ) {
	const rect = text.getBoundingClientRect();
	const height = window.innerHeight;
	const top = rect.top + window.scrollY;
	const last = document.documentElement.scrollHeight - height;
	const end = Math.min( top + rect.height - END * height, last );
	const start = Math.min( top - START * height, end - 0.25 * height );
	const progress =
		end <= 0
			? 1
			: Math.min(
					1,
					Math.max( 0, ( window.scrollY - start ) / ( end - start ) )
				);
	text.style.setProperty( '--statement-fill', progress * count );
}

if ( ! reduce.matches && 'IntersectionObserver' in window ) {
	const texts = new Map();
	const active = new Set();
	let frame = 0;

	const update = () => {
		frame = 0;
		active.forEach( ( text ) => fill( text, texts.get( text ) ) );
	};
	const request = () => {
		frame = frame || window.requestAnimationFrame( update );
	};

	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					active.add( entry.target );
				} else {
					active.delete( entry.target );
					// Settle it where it left: fully lit above, dim below.
					fill( entry.target, texts.get( entry.target ) );
				}
			} );
			request();
		},
		{ rootMargin: '25% 0px' }
	);

	document
		.querySelectorAll( '.statement[data-light-up] .statement__text' )
		.forEach( ( text ) => {
			texts.set( text, split( text ) );
			fill( text, texts.get( text ) );
			observer.observe( text );
		} );

	if ( texts.size ) {
		window.addEventListener( 'scroll', request, { passive: true } );
		window.addEventListener( 'resize', request );
	}
}
