import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, HeadingLevelControl } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const innerBlocksProps = useInnerBlocksProps( { className: 'document-download__files' }, { allowedBlocks: metadata.allowedBlocks, template: [ [ 'floe/download' ] ] } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<HeadingLevelControl value={ attributes.headingLevel } onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) } />
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="document-download__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } layout="stacked" />
					<ul { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
