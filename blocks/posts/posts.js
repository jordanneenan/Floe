/*
 * Posts block: filter buttons and "Load more" (a button, or automatic when
 * the button scrolls into view). Cards come from the block's REST route and
 * are swapped in place without reloading the page; the chosen filter is kept
 * in the address (?filter=<slug>) and Back steps through filters. New cards
 * fade up into place.
 */
const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

// New cards fade up into place, one after another.
function appear( cards ) {
	if ( reduceMotion.matches || ! Element.prototype.animate ) {
		return;
	}
	cards.forEach( ( card, index ) =>
		card.animate(
			[
				{ opacity: 0, transform: 'translate3d(0, 20px, 0)' },
				{ opacity: 1, transform: 'none' },
			],
			{
				duration: 700,
				delay: Math.min( index, 6 ) * 80,
				easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
				fill: 'backwards',
			}
		)
	);
}

function setup( section ) {
	const config = JSON.parse( section.dataset.posts || '{}' );
	const grid = section.querySelector( '.posts__grid' );
	const moreWrap = section.querySelector( '.posts__more' );
	const moreButton = section.querySelector( '.posts__more-button' );
	const status = section.querySelector( '.posts__status' );
	const filters = [ ...section.querySelectorAll( '.posts__filter' ) ];
	let page = 1;
	let term = config.term || 0;
	let loading = false;
	let observer = null;

	const request = ( nextPage ) => {
		const params = new URLSearchParams( {
			type: config.type,
			per_page: config.per_page,
			all: config.all ? '1' : '0',
			page: String( nextPage ),
			taxonomy: config.taxonomy || '',
			term: String( term ),
			level: String( config.level ),
			overrides: config.overrides || '',
		} );
		( config.exclude || [] ).forEach( ( id ) =>
			params.append( 'exclude[]', id )
		);
		return fetch( `${ config.endpoint }?${ params }`, {
			headers: { Accept: 'application/json' },
		} ).then( ( response ) => {
			if ( ! response.ok ) {
				throw new Error( response.statusText );
			}
			return response.json();
		} );
	};

	const load = ( reset ) => {
		if ( loading ) {
			return Promise.resolve();
		}
		loading = true;
		grid.setAttribute( 'aria-busy', 'true' );
		section.classList.add( 'is-loading' );
		const nextPage = reset ? 1 : page + 1;

		return request( nextPage )
			.then( ( data ) => {
				page = nextPage;
				const before = grid.children.length;
				if ( reset ) {
					grid.innerHTML = data.html;
				} else {
					grid.insertAdjacentHTML( 'beforeend', data.html );
					// Move keyboard focus to the first new card's link.
					grid.children[ before ]
						?.querySelector( 'a' )
						?.focus( { preventScroll: true } );
				}
				appear( [ ...grid.children ].slice( reset ? 0 : before ) );
				if ( moreWrap ) {
					moreWrap.hidden = ! data.hasMore;
				}
				if ( status ) {
					status.textContent = reset
						? `${ grid.children.length } posts shown.`
						: `${ data.count } more posts loaded.`;
				}
			} )
			.catch( () => {
				if ( status ) {
					status.textContent = 'Posts could not be loaded.';
				}
			} )
			.finally( () => {
				loading = false;
				grid.setAttribute( 'aria-busy', 'false' );
				section.classList.remove( 'is-loading' );
				// Automatic mode: if the button is still in view, observing
				// it again fires straight away and loads the next set.
				if ( observer && moreButton ) {
					observer.unobserve( moreButton );
					observer.observe( moreButton );
				}
			} );
	};

	const select = ( button ) => {
		filters.forEach( ( other ) =>
			other.setAttribute( 'aria-pressed', String( other === button ) )
		);
		term = Number( button.dataset.term ) || 0;
		return load( true );
	};

	filters.forEach( ( button ) =>
		button.addEventListener( 'click', () => {
			if ( button.getAttribute( 'aria-pressed' ) === 'true' ) {
				return;
			}
			select( button );
			// Keep the filter in the address without reloading the page, so
			// the view can be shared, bookmarked or reached with Back.
			const url = new URL( window.location.href );
			if ( button.dataset.slug ) {
				url.searchParams.set( 'filter', button.dataset.slug );
			} else {
				url.searchParams.delete( 'filter' );
			}
			window.history.pushState(
				{ floeFilter: button.dataset.slug || '' },
				'',
				url
			);
		} )
	);

	if ( filters.length ) {
		window.addEventListener( 'popstate', () => {
			const slug =
				new URL( window.location.href ).searchParams.get( 'filter' ) ||
				'';
			const button =
				filters.find( ( item ) => item.dataset.slug === slug ) ||
				filters[ 0 ];
			if ( button.getAttribute( 'aria-pressed' ) !== 'true' ) {
				select( button );
			}
		} );
	}

	moreButton?.addEventListener( 'click', () => load( false ) );

	// Automatic mode: load when the button comes into view (it stays usable
	// as a fallback, e.g. for keyboard users).
	if (
		config.more === 'scroll' &&
		moreButton &&
		'IntersectionObserver' in window
	) {
		section.classList.add( 'posts--auto' );
		observer = new IntersectionObserver(
			( entries ) => {
				if (
					entries.some( ( entry ) => entry.isIntersecting ) &&
					! moreWrap.hidden
				) {
					load( false );
				}
			},
			{ rootMargin: '0px 0px 400px 0px' }
		);
		observer.observe( moreButton );
	}
}

document.querySelectorAll( '.posts[data-posts]' ).forEach( setup );
