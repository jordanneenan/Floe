/**
 * Breadcrumb: editor preview. The real trail is built on the server from the
 * page hierarchy, so the editor shows Home / the current page title.
 * import { Breadcrumb } from '@floe/components/breadcrumb';
 */
import { __ } from '@wordpress/i18n';

export function Breadcrumb( { current } ) {
	return (
		<div className="breadcrumb">
			<nav className="wp-block-breadcrumbs" aria-label={ __( 'Breadcrumbs', 'floe' ) } style={ { '--separator': '"/"' } }>
				<ol>
					<li><a href="#home" onClick={ ( event ) => event.preventDefault() }>{ __( 'Home', 'floe' ) }</a></li>
					<li><span aria-current="page">{ current || __( 'This page', 'floe' ) }</span></li>
				</ol>
			</nav>
		</div>
	);
}
