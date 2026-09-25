/**
 * Replace the cover with the privacy-enhanced YouTube player only when the
 * visitor presses play. No YouTube request is made before that.
 */
document.querySelectorAll( '.video__player[data-video-id]' ).forEach( ( player ) => {
	const button = player.querySelector( '.play-control' );
	button?.addEventListener( 'click', () => {
		const iframe = document.createElement( 'iframe' );
		iframe.className = 'video__iframe';
		iframe.src = `https://www.youtube-nocookie.com/embed/${ encodeURIComponent( player.dataset.videoId ) }?autoplay=1&rel=0&playsinline=1`;
		iframe.title = player.dataset.videoTitle || 'Video';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen';
		iframe.allowFullscreen = true;
		iframe.referrerPolicy = 'strict-origin-when-cross-origin';
		player.classList.add( 'is-playing' );
		player.appendChild( iframe );
		player.querySelector( '.video__control' )?.remove();
		iframe.focus();
	} );
} );
