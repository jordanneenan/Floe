import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useText, MediaSlot } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context } ) {
	const text = useText( attributes, setAttributes );
	const level = Math.min( 4, ( context[ 'floe/headingLevel' ] || 2 ) + 1 );
	const blockProps = useBlockProps( { className: 'person' } );
	return (
		<li { ...blockProps }>
			<MediaSlot value={ attributes.portrait } onChange={ ( portrait ) => setAttributes( { portrait } ) } ratio="4/5" allowVideo={ false } label={ __( 'Portrait', 'floe' ) } className="person__portrait" placeholder />
			{ text( 'name', __( 'Name', 'floe' ), { allowedFormats: [] } )( { tagName: `h${ level }`, className: 'person__name' } ) }
			{ text( 'role', __( 'Role', 'floe' ), { allowedFormats: [] } )( { tagName: 'p', className: 'person__role' } ) }
		</li>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
