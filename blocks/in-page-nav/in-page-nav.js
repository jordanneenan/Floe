/*
 * Highlight the in-page link for the section currently in view, and keep the
 * active pill scrolled into view on narrow screens.
 */
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

	const update = () => {
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
