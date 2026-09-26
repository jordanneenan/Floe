/*
 * Testimonials slider: previous/next buttons scroll the track by one card and
 * disable at either end. The track also scrolls by swipe, trackpad and keys.
 */
document.querySelectorAll( '.testimonials--slider' ).forEach( ( section ) => {
	const track = section.querySelector( '.testimonials__track' );
	const prev = section.querySelector( '.testimonials__prev' );
	const next = section.querySelector( '.testimonials__next' );
	if ( ! track || ! prev || ! next ) {
		return;
	}

	const step = () => {
		const card = track.querySelector( '.quote' );
		const gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
		return card
			? card.getBoundingClientRect().width + gap
			: track.clientWidth;
	};
	const update = () => {
		const max = track.scrollWidth - track.clientWidth - 2;
		prev.disabled = track.scrollLeft <= 2;
		next.disabled = track.scrollLeft >= max;
	};

	prev.addEventListener( 'click', () =>
		track.scrollBy( { left: -step(), behavior: 'smooth' } )
	);
	next.addEventListener( 'click', () =>
		track.scrollBy( { left: step(), behavior: 'smooth' } )
	);
	track.addEventListener(
		'scroll',
		() => window.requestAnimationFrame( update ),
		{ passive: true }
	);
	window.addEventListener( 'resize', update );
	update();
} );
