import {
	createBlock,
	getBlockType,
	registerBlockType,
	store as blocksStore,
} from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { Notice, PanelBody, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
	SurfaceControl,
} from '@floe/editor';
import metadata from './block.json';

// Blocks opt in to sitting in an Intro through "supports": { "floeIntro":
// [ "above", "beside" ] } in their block.json, so no block is named here.
// The first layout listed is the one a block gets by default.
const fitsIn = ( type, layout ) =>
	( type?.supports?.floeIntro || [] ).includes( layout );

function Edit( { attributes, setAttributes, clientId, name } ) {
	const { layout, surface, headingLevel } = attributes;
	const beside = layout === 'beside';
	const { types, held } = useSelect(
		( select ) => {
			const first = select( blockEditorStore ).getBlocks( clientId )[ 0 ];
			return {
				types: select( blocksStore ).getBlockTypes(),
				held: first
					? select( blocksStore ).getBlockType( first.name )
					: null,
			};
		},
		[ clientId ]
	);
	const allowed = useMemo(
		() =>
			types
				.filter( ( type ) => fitsIn( type, layout ) )
				.map( ( type ) => type.name ),
		[ types, layout ]
	);
	const besideTitles = useMemo(
		() =>
			types
				.filter( ( type ) => fitsIn( type, 'beside' ) )
				.map( ( type ) => type.title )
				.join( ', ' ),
		[ types ]
	);

	const blockProps = useFloeBlockProps(
		name,
		{ className: `intro--${ beside ? 'beside' : 'above' }` },
		{ surface }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'intro__block' },
		{
			allowedBlocks: allowed,
			renderAppender: held ? false : InnerBlocks.ButtonBlockAppender,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Layout', 'floe' ) }
						help={ sprintf(
							/* translators: %s: list of block names. */
							__( 'Two columns suits %s.', 'floe' ),
							besideTitles
						) }
						value={ layout }
						options={ [
							{
								label: __( 'Heading above the block', 'floe' ),
								value: 'above',
							},
							{
								label: __(
									'Two columns: heading beside the block',
									'floe'
								),
								value: 'beside',
								disabled: !! held && ! fitsIn( held, 'beside' ),
							},
						] }
						onChange={ ( next ) =>
							setAttributes( { layout: next } )
						}
					/>
					{ held && ! fitsIn( held, layout ) && (
						<Notice status="warning" isDismissible={ false }>
							{ sprintf(
								/* translators: %s: block name. */
								__(
									'%s needs the full width. Put the heading above it, or choose a different block.',
									'floe'
								),
								held.title
							) }
						</Notice>
					) }
					<SurfaceControl
						value={ surface }
						onChange={ ( next ) =>
							setAttributes( { surface: next } )
						}
						options={ [ 'base', 'subtle', 'tint', 'inverse' ] }
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
				<div className="intro__inner">
					<EditableSectionHeader
						attributes={ attributes }
						setAttributes={ setAttributes }
						layout={ beside ? 'stacked' : 'split' }
						action
						actionStyle={ beside ? 'link' : 'secondary' }
						placeholders={ {
							heading: __( 'Heading', 'floe' ),
							action: __( 'Optional button', 'floe' ),
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
	transforms: {
		// "Transform to Intro" on any block that can sit in one: the block
		// moves into a new Intro's slot, in the first layout it lists.
		from: [
			{
				type: 'block',
				blocks: [ '*' ],
				isMatch: ( attributes, block ) =>
					!! block?.name &&
					fitsIn( getBlockType( block.name ), 'above' ),
				__experimentalConvert: ( block ) =>
					createBlock(
						metadata.name,
						{
							layout: getBlockType( block.name ).supports
								.floeIntro[ 0 ],
						},
						[
							createBlock(
								block.name,
								block.attributes,
								block.innerBlocks
							),
						]
					),
			},
		],
	},
} );
