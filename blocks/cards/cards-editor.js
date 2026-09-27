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
	[ 'floe/card-item' ],
	[ 'floe/card-item' ],
	[ 'floe/card-item' ],
];

function Edit( { attributes, setAttributes, name, context } ) {
	const { style } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{ className: `cards--${ style }` },
		{ context }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'cards__grid' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: TEMPLATE,
			orientation: 'horizontal',
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Card style', 'floe' ) }
						help={ __(
							'Feature: numbered cards with a link. Media: an image on each card.',
							'floe'
						) }
						value={ style }
						options={ [
							{
								label: __( 'Feature', 'floe' ),
								value: 'feature',
							},
							{ label: __( 'Media', 'floe' ), value: 'media' },
						] }
						onChange={ ( next ) =>
							setAttributes( { style: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="cards__inner">
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
