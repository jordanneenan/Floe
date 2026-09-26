/*
 * Form: sends the form in the background and shows the result in place.
 * Without JavaScript the form posts normally and the page reloads with the
 * result (see form.php).
 */
function setup( root ) {
	const form = root.querySelector( '.form__form' );
	const message = root.querySelector( '.form__message' );
	const success = root.querySelector( '.form__success' );
	const submit = form?.querySelector( '[type="submit"]' );
	if ( ! form || ! message || ! success || ! submit ) {
		return;
	}

	const showError = ( text, fields = [] ) => {
		message.textContent = text || root.dataset.error;
		message.hidden = false;
		fields.forEach( ( name ) =>
			form.elements[ name ]?.setAttribute( 'aria-invalid', 'true' )
		);
		form.querySelector( '[aria-invalid="true"]' )?.focus();
	};

	form.addEventListener( 'input', ( event ) =>
		event.target.removeAttribute( 'aria-invalid' )
	);

	form.addEventListener( 'submit', async ( event ) => {
		event.preventDefault();
		if ( submit.getAttribute( 'aria-busy' ) === 'true' ) {
			return;
		}
		message.hidden = true;
		submit.setAttribute( 'aria-busy', 'true' );
		try {
			// Not form.action: the hidden "action" field shadows it.
			const response = await window.fetch(
				form.getAttribute( 'action' ),
				{
					method: 'POST',
					body: new window.FormData( form ),
					headers: { Accept: 'application/json' },
					credentials: 'same-origin',
				}
			);
			const result = await response.json();
			if ( result.ok ) {
				form.hidden = true;
				success.hidden = false;
				success.focus();
				return;
			}
			showError( result.message, result.fields );
		} catch {
			showError();
		} finally {
			submit.removeAttribute( 'aria-busy' );
		}
	} );
}

document.querySelectorAll( '[data-floe-form]' ).forEach( setup );
