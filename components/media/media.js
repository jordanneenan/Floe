/*
 * Looping MP4s in Media slots: play only while on screen, never autoplay for
 * visitors who prefer reduced motion, and a visible pause/play control
 * (WCAG 2.2.2).
 */
const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

function setState( media, playing ) {
	const button = media.querySelector( '.media__toggle' );
	media.classList.toggle( 'is-paused', ! playing );
	if ( button ) {
		const label = playing
			? button.dataset.labelPause
			: button.dataset.labelPlay;
		button.setAttribute( 'aria-pressed', playing ? 'false' : 'true' );
		button.querySelector( '.screen-reader-text' ).textContent = label;
	}
}

function init( media ) {
	const video = media.querySelector( 'video' );
	if ( ! video || media.dataset.ready ) {
		return;
	}
	const button = media.querySelector( '.media__toggle' );
	media.dataset.ready = '1';

	let pausedByUser = reduceMotion.matches;
	setState( media, false );

	const play = () =>
		video
			.play()
			.then( () => setState( media, true ) )
			.catch( () => setState( media, false ) );
	const pause = () => {
		video.pause();
		setState( media, false );
	};

	button?.addEventListener( 'click', () => {
		if ( video.paused ) {
			pausedByUser = false;
			play();
		} else {
			pausedByUser = true;
			pause();
		}
	} );

	new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting && ! pausedByUser ) {
					play();
				} else if ( ! entry.isIntersecting && ! video.paused ) {
					video.pause();
					setState( media, false );
				}
			} );
		},
		{ threshold: 0.2 }
	).observe( media );
}

document.querySelectorAll( '.media--video' ).forEach( init );
