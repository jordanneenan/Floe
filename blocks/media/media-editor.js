import { registerBlockType } from '@wordpress/blocks';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { MediaSlot } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context, isSelected } ) {
	const inRow = context[ 'floe/imagesFit' ] !== undefined;
	const fill = inRow && context[ 'floe/imagesFit' ] !== 'natural';
	const blockProps = useBlockProps( {
		className: inRow ? 'floe-media-block-wrapper' : 'media-figure',
	} );
	const slot = (
		<MediaSlot
			value={ attributes.media }
			onChange={ ( media ) => setAttributes( { media } ) }
			cover={ fill }
			className="media-block"
		/>
	);

	if ( inRow ) {
		return <div { ...blockProps }>{ slot }</div>;
	}
	return (
		<figure { ...blockProps }>
			{ slot }
			{ ( attributes.caption || isSelected ) && (
				<RichText
					tagName="figcaption"
					className="media-figure__caption"
					value={ attributes.caption }
					onChange={ ( caption ) => setAttributes( { caption } ) }
					placeholder={ __( 'Optional caption', 'floe' ) }
				/>
			) }
		</figure>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
