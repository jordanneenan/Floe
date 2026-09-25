import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, EditableSectionHeader, HeadingLevelControl } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId, name } ) {
	const count = useSelect( ( select ) => select( blockEditorStore ).getBlockCount( clientId ), [ clientId ] );
	const blockProps = useFloeBlockProps( name, { className: `stats--count-${ Math.min( 4, count ) }` }, { surface: 'base' } );
	const innerBlocksProps = useInnerBlocksProps( { className: 'stats__list' }, {
		allowedBlocks: metadata.allowedBlocks,
		template: [ [ 'floe/stat' ], [ 'floe/stat' ], [ 'floe/stat' ] ],
		orientation: 'horizontal',
		renderAppender: count < 4 ? InnerBlocks.ButtonBlockAppender : false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<HeadingLevelControl value={ attributes.headingLevel } onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) } />
					<p>{ __( 'Two to four stats.', 'floe' ) }</p>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="stats__inner">
					<EditableSectionHeader attributes={ attributes } setAttributes={ setAttributes } />
					<div { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
