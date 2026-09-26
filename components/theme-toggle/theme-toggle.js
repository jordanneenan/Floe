/*
 * Light/dark switch. The saved choice wins; without one the site follows the
 * device, live. Switching wipes the new theme across the page in a circle
 * from the button (View Transitions), or cross-fades colours over 0.4s where
 * that isn't supported. Reduced motion switches instantly.
 */
const root = document.documentElement;
const buttons = document.querySelectorAll( '.theme-toggle' );
const KEY = 'floe-theme';
const device = window.matchMedia( '(prefers-color-scheme: dark)' );
const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' );

const saved = () => {
	try {
		return window.localStorage.getItem( KEY );
	} catch {
		return null;
	}
};

const sync = () => {
	const dark = root.dataset.theme === 'dark';
	buttons.forEach( ( button ) =>
		button.setAttribute( 'aria-pressed', String( dark ) )
	);
};

const set = ( theme, origin ) => {
	if ( root.dataset.theme === theme ) {
		return;
	}
	const swap = () => {
		root.dataset.theme = theme;
		sync();
	};

	if ( reduce.matches ) {
		swap();
		return;
	}

	if ( origin && document.startViewTransition ) {
		const box = origin.getBoundingClientRect();
		const x = box.left + box.width / 2;
		const y = box.top + box.height / 2;
		const radius = Math.hypot(
			Math.max( x, window.innerWidth - x ),
			Math.max( y, window.innerHeight - y )
		);
		root.classList.add( 'floe-theme-wipe' );
		const transition = document.startViewTransition( swap );
		transition.ready
			.then( () =>
				root.animate(
					{
						clipPath: [
							`circle(0px at ${ x }px ${ y }px)`,
							`circle(${ radius }px at ${ x }px ${ y }px)`,
						],
					},
					{
						duration: 700,
						easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
						pseudoElement: '::view-transition-new(root)',
					}
				)
			)
			.catch( () => {} );
		transition.finished.finally( () =>
			root.classList.remove( 'floe-theme-wipe' )
		);
		return;
	}

	root.classList.add( 'floe-theme-changing' );
	swap();
	window.setTimeout(
		() => root.classList.remove( 'floe-theme-changing' ),
		450
	);
};

buttons.forEach( ( button ) =>
	button.addEventListener( 'click', () => {
		const theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
		try {
			window.localStorage.setItem( KEY, theme );
		} catch {}
		set( theme, button );
	} )
);

device.addEventListener( 'change', () => {
	if ( ! saved() ) {
		set( device.matches ? 'dark' : 'light' );
	}
} );

sync();
