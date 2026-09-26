/*
 * Posts block: filter buttons and "Load more" (a button, or automatic when
 * the button scrolls into view). Cards come from the block's REST route and
 * are swapped in place: no page load and no URL change.
 */
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

	filters.forEach( ( button ) =>
		button.addEventListener( 'click', () => {
			if ( button.getAttribute( 'aria-pressed' ) === 'true' ) {
				return;
			}
			filters.forEach( ( other ) =>
				other.setAttribute( 'aria-pressed', String( other === button ) )
			);
			term = Number( button.dataset.term ) || 0;
			load( true );
		} )
	);

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
