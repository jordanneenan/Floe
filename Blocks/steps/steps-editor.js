import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, HeadingLevelControl, SurfaceControl } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId, name } ) {
	const count = useSelect( ( select ) => select( blockEditorStore ).getBlockCount( clientId ), [ clientId ] );
	const blockProps = useFloeBlockProps( name, {}, { surface: attributes.surface } );
	const innerBlocksProps = useInnerBlocksProps( { className: 'steps__list' }, {
		allowedBlocks: metadata.allowedBlocks,
		template: [ [ 'floe/step' ], [ 'floe/step' ], [ 'floe/step' ], [ 'floe/step' ] ],
		orientation: 'horizontal',
		renderAppender: count < 5 ? InnerBlocks.ButtonBlockAppender : false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl value={ attributes.surface } onChange={ ( surface ) => setAttributes( { surface } ) } options={ [ 'subtle', 'base', 'tint' ] } />
					<HeadingLevelControl value={ attributes.headingLevel } onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) } />
					<p>{ __( 'Three to five steps. They are numbered automatically.', 'floe' ) }</p>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="steps__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } intro={ false } action actionStyle="secondary" />
					<ol { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
