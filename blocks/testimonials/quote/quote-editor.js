import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useText, MediaSlot } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const text = useText( attributes, setAttributes );
	const blockProps = useBlockProps( { className: 'quote' } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Portrait', 'floe' ) }>
					<MediaSlot
						value={ attributes.portrait }
						onChange={ ( portrait ) =>
							setAttributes( { portrait } )
						}
						ratio="1/1"
						allowVideo={ false }
						label={ __( 'Portrait (optional)', 'floe' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<figure { ...blockProps }>
				<blockquote className="quote__text">
					{ text( 'quote', __( '“A short quote.”', 'floe' ), {
						disableLineBreaks: false,
					} )( { tagName: 'p' } ) }
				</blockquote>
				<figcaption className="quote__attribution">
					<MediaSlot
						value={ attributes.portrait }
						onChange={ ( portrait ) =>
							setAttributes( { portrait } )
						}
						ratio="1/1"
						radius="pill"
						allowVideo={ false }
						className="quote__portrait"
						placeholder
						label={ __( 'Portrait', 'floe' ) }
					/>
					<span className="quote__who">
						{ text( 'name', __( 'Name', 'floe' ), {
							allowedFormats: [],
						} )( { tagName: 'span', className: 'quote__name' } ) }
						{ text( 'role', __( 'Role, organisation', 'floe' ), {
							allowedFormats: [],
						} )( { tagName: 'span', className: 'quote__role' } ) }
					</span>
				</figcaption>
			</figure>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
