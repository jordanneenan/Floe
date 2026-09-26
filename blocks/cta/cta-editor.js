import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	LinkButton,
	HeadingLevelControl,
	SurfaceControl,
} from '@floe/editor';
import { Eyebrow } from '@floe/components/eyebrow';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const { surface, headingLevel } = attributes;
	const text = useText( attributes, setAttributes );
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl
						value={ surface }
						onChange={ ( next ) =>
							setAttributes( { surface: next } )
						}
						options={ [ 'inverse', 'accent' ] }
					/>
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="cta__inner">
					<div className={ `cta__panel surface-${ surface }` }>
						<div className="cta__copy">
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
								className: 'cta__heading',
							} ) }
							{ text(
								'body',
								__( 'A short line', 'floe' )
							)( { tagName: 'p', className: 'cta__body' } ) }
						</div>
						<div className="cta__actions">
							<LinkButton
								value={ attributes.action }
								onChange={ ( action ) =>
									setAttributes( { action } )
								}
								placeholder={ __( 'Action', 'floe' ) }
							/>
							{ text(
								'note',
								__(
									'Optional note, e.g. or email hello@…',
									'floe'
								)
							)( { tagName: 'p', className: 'cta__note' } ) }
						</div>
					</div>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
