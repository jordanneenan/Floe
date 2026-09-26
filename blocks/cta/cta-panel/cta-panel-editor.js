import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	LinkButton,
	MediaSlot,
	HeadingLevelControl,
	SurfaceControl,
} from '@floe/editor';
import { Eyebrow } from '@floe/components/eyebrow';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const { surface, headingLevel, media } = attributes;
	const text = useText( attributes, setAttributes );
	const blockProps = useFloeBlockProps(
		name,
		{ className: media?.id ? 'has-media' : '' },
		{ section: false, surface }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Panel', 'floe' ) }>
					<SurfaceControl
						value={ surface }
						onChange={ ( next ) =>
							setAttributes( { surface: next } )
						}
						options={ [ 'inverse', 'accent', 'tint', 'subtle' ] }
					/>
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
					<p>{ __( 'Image (optional)', 'floe' ) }</p>
					<MediaSlot
						value={ media }
						onChange={ ( next ) =>
							setAttributes( { media: next } )
						}
						ratio="16/9"
						label={ __( 'Panel image', 'floe' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ media?.id && (
					<MediaSlot
						value={ media }
						onChange={ ( next ) =>
							setAttributes( { media: next } )
						}
						ratio="16/9"
						className="cta-panel__media"
					/>
				) }
				<div className="cta-panel__copy">
					<Eyebrow>
						{ text( 'eyebrow', __( 'Eyebrow', 'floe' ), {
							allowedFormats: [],
						} )( { tagName: 'span' } ) }
					</Eyebrow>
					{ text(
						'heading',
						__( 'Heading', 'floe' )
					)( {
						tagName: `h${ headingLevel }`,
						className: 'cta-panel__heading',
					} ) }
					{ text(
						'body',
						__( 'A short line', 'floe' )
					)( {
						tagName: 'p',
						className: 'cta-panel__body',
					} ) }
				</div>
				<div className="cta-panel__actions">
					<LinkButton
						value={ attributes.action }
						onChange={ ( action ) => setAttributes( { action } ) }
						placeholder={ __( 'Action', 'floe' ) }
					/>
					{ text(
						'note',
						__( 'Optional note, e.g. or email hello@…', 'floe' )
					)( { tagName: 'p', className: 'cta-panel__note' } ) }
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
