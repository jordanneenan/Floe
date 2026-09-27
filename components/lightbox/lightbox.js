/*
 * Lightbox: a link with data-lightbox="<group>" opens its image full screen
 * in a modal <dialog>. Every link in the same group becomes a slide of the
 * Carousel inside it, so visitors step through with the buttons, the arrow
 * keys or a swipe. Escape, the close button or a click beside the image
 * closes it, and focus goes back to the link that opened it.
 *
 * The viewer's markup is a <template id="floe-lightbox"> printed once by
 * lightbox.php; it's copied into the page the first time it's needed.
 */
let dialog;
let carousel;
let track;
let count;
let opener;

const api = () => window.floe?.carousel;

function show( index, smooth ) {
	if ( api() ) {
		api().goTo( carousel, index, smooth );
	} else {
		track.scrollTo( { left: index * track.clientWidth } );
	}
}

function position() {
	const step = track.firstElementChild?.getBoundingClientRect().width || 1;
	const gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
	return Math.round( track.scrollLeft / ( step + gap ) );
}

function updateCount() {
	const total = track.children.length;
	count.textContent = total > 1 ? `${ position() + 1 } / ${ total }` : '';
}

function slide( link ) {
	const figure = document.createElement( 'figure' );
	figure.className = 'lightbox__slide';
	const image = document.createElement( 'img' );
	image.src = link.dataset.lightboxSrc || link.href;
	if ( link.dataset.lightboxSrcset ) {
		image.srcset = link.dataset.lightboxSrcset;
		image.sizes = '100vw';
	}
	image.alt = link.dataset.lightboxAlt || '';
	image.width = Number( link.dataset.lightboxWidth ) || 0;
	image.height = Number( link.dataset.lightboxHeight ) || 0;
	image.decoding = 'async';
	image.loading = 'lazy';
	figure.append( image );
	if ( link.dataset.lightboxCaption ) {
		const caption = document.createElement( 'figcaption' );
		caption.textContent = link.dataset.lightboxCaption;
		figure.append( caption );
	}
	return figure;
}

function setup() {
	if ( dialog ) {
		return true;
	}
	const template = document.getElementById( 'floe-lightbox' );
	if ( ! template ) {
		return false;
	}
	document.body.append( template.content.cloneNode( true ) );
	dialog = document.body.lastElementChild;
	carousel = dialog.querySelector( '.carousel' );
	track = dialog.querySelector( '.carousel__track' );
	count = dialog.querySelector( '.lightbox__count' );

	dialog
		.querySelector( '.lightbox__close' )
		.addEventListener( 'click', () => dialog.close() );

	// A click beside the image (not on it or a control) closes the viewer.
	dialog.addEventListener( 'click', ( event ) => {
		if (
			event.target === dialog ||
			event.target.matches( '.lightbox__slide, .carousel__track' )
		) {
			dialog.close();
		}
	} );

	dialog.addEventListener( 'keydown', ( event ) => {
		const move = { ArrowLeft: -1, ArrowRight: 1 }[ event.key ];
		if ( move && event.target !== track ) {
			event.preventDefault();
			show( position() + move, true );
		}
	} );

	track.addEventListener(
		'scroll',
		() => window.requestAnimationFrame( updateCount ),
		{ passive: true }
	);

	dialog.addEventListener( 'close', () => {
		document.documentElement.classList.remove( 'lightbox-open' );
		track.replaceChildren();
		opener?.focus( { preventScroll: true } );
	} );
	return true;
}

function open( link ) {
	if ( ! setup() ) {
		return false;
	}
	const group = link.dataset.lightbox;
	const links = [ ...document.querySelectorAll( '[data-lightbox]' ) ].filter(
		( item ) => item.dataset.lightbox === group
	);
	const index = Math.max( 0, links.indexOf( link ) );
	const slides = links.map( slide );
	// The opened image and its neighbours load straight away.
	[ index - 1, index, index + 1 ].forEach( ( near ) => {
		const image = slides[ near ]?.querySelector( 'img' );
		if ( image ) {
			image.loading = 'eager';
		}
	} );

	opener = link;
	track.replaceChildren( ...slides );
	document.documentElement.classList.add( 'lightbox-open' );
	dialog.showModal();
	api()?.init( carousel );
	show( index, false );
	updateCount();
	track.focus( { preventScroll: true } );
	return true;
}

document.addEventListener( 'click', ( event ) => {
	const link = event.target.closest?.( 'a[data-lightbox]' );
	if (
		! link ||
		event.defaultPrevented ||
		event.button !== 0 ||
		event.metaKey ||
		event.ctrlKey ||
		event.shiftKey ||
		event.altKey
	) {
		return;
	}
	if ( open( link ) ) {
		event.preventDefault();
	}
} );
