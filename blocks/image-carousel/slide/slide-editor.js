import { registerBlockType } from '@wordpress/blocks';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { MediaSlot } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context, isSelected } ) {
	const blockProps = useBlockProps( { className: 'slide' } );
	return (
		<div { ...blockProps }>
			<figure className="media-figure">
				<MediaSlot
					value={ attributes.media }
					onChange={ ( media ) => setAttributes( { media } ) }
					ratio={ context[ 'floe/carouselRatio' ] || '3/2' }
				/>
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
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
