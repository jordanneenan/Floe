import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
} from '@floe/editor';
import metadata from './block.json';

const TEMPLATE = [
	[ 'floe/image-row', {}, [ [ 'floe/media' ] ] ],
	[ 'floe/image-row', {}, [ [ 'floe/media' ], [ 'floe/media' ] ] ],
];

function Edit( { attributes, setAttributes, name } ) {
	const { fit, headingLevel } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{ className: `images--${ fit }` },
		{ surface: 'base' }
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
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="images__inner">
					<EditableSectionHeader
						attributes={ attributes }
						setAttributes={ setAttributes }
						intro={ false }
						placeholders={ {
							eyebrow: __( 'Optional eyebrow', 'floe' ),
							heading: __( 'Optional heading', 'floe' ),
						} }
					/>
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
