import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, HeadingLevelControl } from '@floe/editor';
import metadata from './block.json';

const item = ( summary ) => [ 'core/details', { summary }, [ [ 'core/paragraph', { placeholder: __( 'Answer', 'floe' ) } ] ] ];
const TEMPLATE = [ item( '' ), item( '' ), item( '' ) ];

function Edit( { attributes, setAttributes, name } ) {
	const { oneOpen, schema, headingLevel } = attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const innerBlocksProps = useInnerBlocksProps( { className: 'faq__items' }, { allowedBlocks: metadata.allowedBlocks, template: TEMPLATE } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<ToggleControl __nextHasNoMarginBottom label={ __( 'Only one answer open at a time', 'floe' ) } checked={ oneOpen } onChange={ ( next ) => setAttributes( { oneOpen: next } ) } />
					<ToggleControl __nextHasNoMarginBottom label={ __( 'Add FAQ structured data', 'floe' ) } help={ __( 'Helps search engines show these questions. Use once per page, for genuine questions and answers.', 'floe' ) } checked={ schema } onChange={ ( next ) => setAttributes( { schema: next } ) } />
					<HeadingLevelControl value={ headingLevel } onChange={ ( next ) => setAttributes( { headingLevel: next } ) } />
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="faq__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } layout="stacked" action actionStyle="link" placeholders={ { action: __( 'Optional link, e.g. Contact us', 'floe' ) } } />
					<div { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
