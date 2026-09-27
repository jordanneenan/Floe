/*
 * Mobile menu disclosure: the toggle's aria-expanded shows or hides the
 * panel. Escape closes it and returns focus to the toggle; so does moving
 * focus out of the header or resizing to desktop.
 *
 * A sticky header shrinks as the page scrolls: --floe-header-progress goes
 * from 0 to 1 over the first SHRINK_OVER pixels and header.scss turns it
 * into padding. It also keeps --floe-sticky-top at the bar's real height,
 * as it shrinks and when a long menu wraps onto a second row.
 */
const SHRINK_OVER = 300;

const header = document.querySelector( '.site-header' );
const toggle = header?.querySelector( '.site-header__toggle' );
const panel = header?.querySelector( '.site-header__panel' );
const inner = header?.querySelector( '.site-header__inner' );

header?.classList.add( 'has-js' );

if ( inner && header.classList.contains( 'site-header--sticky' ) ) {
	let progress = null;
	const shrink = () => {
		const next =
			Math.round(
				Math.min( Math.max( window.scrollY / SHRINK_OVER, 0 ), 1 ) *
					1000
			) / 1000;
		if ( next !== progress ) {
			progress = next;
			header.style.setProperty( '--floe-header-progress', next );
		}
	};
	window.addEventListener( 'scroll', shrink, { passive: true } );
	shrink();

	new ResizeObserver( () =>
		document.documentElement.style.setProperty(
			'--floe-sticky-top',
			`${ inner.getBoundingClientRect().height }px`
		)
	).observe( inner );
}

if ( header && toggle && panel ) {
	// The menu sits in the bar from 1280px (header.scss).
	const desktop = window.matchMedia( '(min-width: 1280px)' );

	const setOpen = ( open, returnFocus = false ) => {
		toggle.setAttribute( 'aria-expanded', String( open ) );
		header.classList.toggle( 'is-open', open );
		document.documentElement.classList.toggle(
			'has-open-menu',
			open && ! desktop.matches
		);
		if ( ! open && returnFocus ) {
			toggle.focus();
		}
	};

	toggle.addEventListener( 'click', () =>
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' )
	);

	header.addEventListener( 'keydown', ( event ) => {
		if (
			event.key === 'Escape' &&
			header.classList.contains( 'is-open' )
		) {
			setOpen( false, true );
		}
	} );

	header.addEventListener( 'focusout', ( event ) => {
		if (
			header.classList.contains( 'is-open' ) &&
			event.relatedTarget &&
			! header.contains( event.relatedTarget )
		) {
			setOpen( false );
		}
	} );

	desktop.addEventListener( 'change', () => setOpen( false ) );
}
