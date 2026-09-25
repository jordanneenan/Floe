/**
 * Mobile menu disclosure: the toggle's aria-expanded shows or hides the
 * panel. Escape closes it and returns focus to the toggle; so does moving
 * focus out of the header or resizing to desktop.
 */
const header = document.querySelector( '.site-header' );
const toggle = header?.querySelector( '.site-header__toggle' );
const panel = header?.querySelector( '.site-header__panel' );

if ( header && toggle && panel ) {
	const desktop = window.matchMedia( '(min-width: 768px)' );
	header.classList.add( 'has-js' );

	const setOpen = ( open, returnFocus = false ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		header.classList.toggle( 'is-open', open );
		document.documentElement.classList.toggle( 'has-open-menu', open && ! desktop.matches );
		if ( ! open && returnFocus ) {
			toggle.focus();
		}
	};

	toggle.addEventListener( 'click', () => setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) );

	header.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' && header.classList.contains( 'is-open' ) ) {
			setOpen( false, true );
		}
	} );

	header.addEventListener( 'focusout', ( event ) => {
		if ( header.classList.contains( 'is-open' ) && event.relatedTarget && ! header.contains( event.relatedTarget ) ) {
			setOpen( false );
		}
	} );

	desktop.addEventListener( 'change', () => setOpen( false ) );
}
