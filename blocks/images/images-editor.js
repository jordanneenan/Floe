import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

const TEMPLATE = [
	[ 'floe/image-row', {}, [ [ 'floe/media' ] ] ],
	[ 'floe/image-row', {}, [ [ 'floe/media' ], [ 'floe/media' ] ] ],
];

function Edit( { attributes, setAttributes, name, context } ) {
	const { fit } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{ className: `images--${ fit }` },
		{ surface: 'base', context }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'images__rows' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Fit', 'floe' ) }
						help={ __(
							'Fill crops every image to the row’s shape. Natural keeps each file’s own proportions.',
							'floe'
						) }
						value={ fit }
						options={ [
							{ label: __( 'Fill', 'floe' ), value: 'fill' },
							{
								label: __( 'Natural', 'floe' ),
								value: 'natural',
							},
						] }
						onChange={ ( next ) => setAttributes( { fit: next } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="images__inner">
					<div { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
