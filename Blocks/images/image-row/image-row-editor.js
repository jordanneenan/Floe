import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId, name } ) {
	const count = useSelect( ( select ) => select( blockEditorStore ).getBlockCount( clientId ), [ clientId ] );
	const blockProps = useFloeBlockProps( name, { className: `image-row--count-${ Math.min( 3, Math.max( 1, count ) ) }${ attributes.gap ? '' : ' image-row--no-gap' }` }, { section: false } );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: metadata.allowedBlocks,
		template: [ [ 'floe/media' ] ],
		orientation: 'horizontal',
		renderAppender: count < 3 ? InnerBlocks.ButtonBlockAppender : false,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Row', 'floe' ) }>
					<ToggleControl __nextHasNoMarginBottom label={ __( 'Gap between items', 'floe' ) } checked={ attributes.gap } onChange={ ( gap ) => setAttributes( { gap } ) } />
					<p>{ __( 'A row holds up to three items. The number of columns follows the number of items.', 'floe' ) }</p>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } />
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
