import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, MediaSlot, HeadingLevelControl, SurfaceControl } from '@floe/editor';
import { PlayControl } from '@floe/components/play-control';
import metadata from './block.json';

const hasId = ( url ) => /(?:youtube(?:-nocookie)?\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)[A-Za-z0-9_-]{11}/.test( url );

function Edit( { attributes, setAttributes, name } ) {
	const { url, duration, surface, headingLevel } = attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface } );
	const valid = hasId( url );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Video', 'floe' ) }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'YouTube link', 'floe' ) }
						help={ url && ! valid ? __( 'That doesn’t look like a YouTube video link.', 'floe' ) : __( 'A watch, youtu.be, Shorts or embed link.', 'floe' ) }
						value={ url }
						onChange={ ( next ) => setAttributes( { url: next.trim() } ) }
					/>
					<TextControl __next40pxDefaultSize __nextHasNoMarginBottom label={ __( 'Duration (optional)', 'floe' ) } placeholder="2:14" value={ duration } onChange={ ( next ) => setAttributes( { duration: next } ) } />
					<SurfaceControl value={ surface } onChange={ ( next ) => setAttributes( { surface: next } ) } options={ [ 'inverse', 'base', 'subtle' ] } />
					<HeadingLevelControl value={ headingLevel } onChange={ ( next ) => setAttributes( { headingLevel: next } ) } />
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="video__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } />
					<div className="video__player">
						<MediaSlot value={ attributes.cover } onChange={ ( cover ) => setAttributes( { cover } ) } ratio="16/9" radius="xl" allowVideo={ false } label={ __( 'Cover image', 'floe' ) } className="video__cover" />
						{ valid && (
							<div className="video__control">
								<PlayControl duration={ duration } />
							</div>
						) }
					</div>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
