import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, useText, MediaSlot, SurfaceControl } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name, isSelected } ) {
	const { surface, portrait } = attributes;
	const text = useText( attributes, setAttributes );
	const blockProps = useFloeBlockProps( name, {}, { surface } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl value={ surface } onChange={ ( next ) => setAttributes( { surface: next } ) } options={ [ 'subtle', 'base', 'tint' ] } />
					<p>{ __( 'Portrait (optional)', 'floe' ) }</p>
					<MediaSlot value={ portrait } onChange={ ( next ) => setAttributes( { portrait: next } ) } ratio="1/1" allowVideo={ false } label={ __( 'Portrait', 'floe' ) } />
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<figure className="testimonial__inner">
					<span className="testimonial__mark" aria-hidden="true">“</span>
					<blockquote className="testimonial__quote">
						{ text( 'quote', __( 'Quote', 'floe' ), { disableLineBreaks: false } )( { tagName: 'p' } ) }
					</blockquote>
					<figcaption className="testimonial__attribution">
						{ ( portrait?.id || isSelected ) && (
							<MediaSlot value={ portrait } onChange={ ( next ) => setAttributes( { portrait: next } ) } ratio="1/1" radius="pill" allowVideo={ false } className="testimonial__portrait" label={ __( 'Portrait', 'floe' ) } placeholder />
						) }
						<span className="testimonial__who">
							{ text( 'name', __( 'Name', 'floe' ), { allowedFormats: [] } )( { tagName: 'span', className: 'testimonial__name' } ) }
							{ text( 'role', __( 'Role, organisation', 'floe' ), { allowedFormats: [] } )( { tagName: 'span', className: 'testimonial__role' } ) }
						</span>
					</figcaption>
				</figure>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
