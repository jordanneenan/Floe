import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useText } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const text = useText( attributes, setAttributes );
	const blockProps = useBlockProps( { className: 'stat' } );
	return (
		<div { ...blockProps }>
			<p className="stat__figure">
				{ text( 'value', '40', { allowedFormats: [] } )( {
					tagName: 'span',
					className: 'stat__value',
				} ) }
				{ text( 'unit', __( 'unit', 'floe' ), { allowedFormats: [] } )(
					{ tagName: 'span', className: 'stat__unit' }
				) }
			</p>
			{ text(
				'label',
				__( 'What the number means', 'floe' )
			)( { tagName: 'p', className: 'stat__label' } ) }
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
