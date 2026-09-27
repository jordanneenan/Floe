import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

const TEMPLATE = [
	[ 'floe/card-item' ],
	[ 'floe/card-item' ],
	[ 'floe/card-item' ],
];

function Edit( { attributes, setAttributes, name, context } ) {
	const { style, columns } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{ className: `cards--${ style } cards--cols-${ columns }` },
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
							'Feature: numbered cards with a link. Icon: an icon on each card instead of the number. Media: an image on each card.',
							'floe'
						) }
						value={ style }
						options={ [
							{
								label: __( 'Feature', 'floe' ),
								value: 'feature',
							},
							{ label: __( 'Icon', 'floe' ), value: 'icon' },
							{ label: __( 'Media', 'floe' ), value: 'media' },
						] }
						onChange={ ( next ) =>
							setAttributes( { style: next } )
						}
					/>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Columns', 'floe' ) }
						help={ __(
							'On large screens. Tablets show two, phones one.',
							'floe'
						) }
						min={ 2 }
						max={ 4 }
						value={ columns }
						onChange={ ( next ) =>
							setAttributes( { columns: next || 3 } )
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
