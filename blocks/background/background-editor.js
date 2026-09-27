import { registerBlockType, store as blocksStore } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, SurfaceControl } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const { surface, lightText, autoSpacing } = attributes;
	// Any Floe section can go in a band, except those that opt out with
	// "supports": { "floeBackground": false } (banners, In-page navigation,
	// another Background).
	const types = useSelect(
		( select ) => select( blocksStore ).getBlockTypes(),
		[]
	);
	const allowed = useMemo(
		() =>
			types
				.filter(
					( type ) =>
						type.category === 'floe-sections' &&
						type.supports?.floeBackground !== false
				)
				.map( ( type ) => type.name ),
		[ types ]
	);
	const blockProps = useFloeBlockProps(
		name,
		{
			className: [
				autoSpacing ? 'background--spaced' : '',
				lightText ? 'force-light-text' : '',
			]
				.filter( Boolean )
				.join( ' ' ),
		},
		{ surface }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'background__inner' },
		{
			allowedBlocks: allowed,
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl
						value={ surface }
						onChange={ ( value ) =>
							setAttributes( { surface: value } )
						}
						options={ [ 'subtle', 'tint', 'inverse', 'accent' ] }
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Force light text', 'floe' ) }
						help={ __(
							'Text and buttons already turn light on Ink and Accent. Use this to make them light on any colour.',
							'floe'
						) }
						checked={ lightText }
						onChange={ ( value ) =>
							setAttributes( { lightText: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Auto spacing', 'floe' ) }
						help={ __(
							'Space above the first block and below the last, the same as between blocks. Turn it off to set the space with Spacing blocks inside.',
							'floe'
						) }
						checked={ autoSpacing }
						onChange={ ( value ) =>
							setAttributes( { autoSpacing: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div { ...innerBlocksProps } />
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
