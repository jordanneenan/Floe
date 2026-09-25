import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, HeadingLevelControl } from '@floe/editor';
import { Icon } from '@floe/components/icon';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId, name } ) {
	const count = useSelect( ( select ) => select( blockEditorStore ).getBlockCount( clientId ), [ clientId ] );
	const slider = count > 3;
	// The editor shows every card in a wrapping grid so each one can be edited.
	const blockProps = useFloeBlockProps( name, { className: 'testimonials--grid' }, { surface: 'base' } );
	const innerBlocksProps = useInnerBlocksProps( { className: 'testimonials__track floe-editor-wrap' }, {
		allowedBlocks: metadata.allowedBlocks,
		template: [ [ 'floe/quote' ], [ 'floe/quote' ], [ 'floe/quote' ] ],
		orientation: 'horizontal',
	} );
	const controls = slider ? (
		<div className="testimonials__controls" aria-hidden="true">
			<button type="button" className="testimonials__prev" disabled><Icon name="arrow-left" /></button>
			<button type="button" className="testimonials__next"><Icon name="arrow" /></button>
		</div>
	) : null;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<HeadingLevelControl value={ attributes.headingLevel } onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) } />
					<p>{ __( 'Up to three quotes show as a grid. With four or more, the page shows them as a slider with previous and next buttons.', 'floe' ) }</p>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="testimonials__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } intro={ false } aside={ controls } />
					<div { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
