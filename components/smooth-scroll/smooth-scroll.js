/*
 * Landing on an anchor: smooth-scroll.php's head script took the anchor off
 * the URL so the page opened at the top. Once everything has loaded (so the
 * target won't move), put it back with location.replace: a same-page anchor
 * navigation, so it scrolls with the page's smooth scroll-behavior, sets
 * :target and the focus starting point, and adds no history entry. If the
 * visitor has already scrolled, leave them where they are.
 */
const root = document.documentElement;
const hash = root.dataset.scrollTo;

if ( hash ) {
	delete root.dataset.scrollTo;

	const land = () => {
		if ( window.scrollY > 0 ) {
			window.history.replaceState( window.history.state, '', hash );
		} else {
			window.location.replace( hash );
		}
	};

	if ( document.readyState === 'complete' ) {
		land();
	} else {
		window.addEventListener( 'load', land, { once: true } );
	}
}
