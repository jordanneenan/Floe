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
import { PanelBody, SelectControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
} from '@floe/editor';
import metadata from './block.json';

// Blocks that can sit in the right-hand column say so with "supports":
// { "floeBlockIntro": true } in their block.json, so no block is named here.
const fits = ( type ) => !! type?.supports?.floeBlockIntro;

function Edit( { attributes, setAttributes, clientId, name } ) {
	const { layout, headingLevel } = attributes;
	const columns = layout === 'columns';
	const { types, held, next, rootClientId, index } = useSelect(
		( select ) => {
			const editor = select( blockEditorStore );
			const root = editor.getBlockRootClientId( clientId );
			const nextId = editor.getNextBlockClientId( clientId );
			return {
				types: select( blocksStore ).getBlockTypes(),
				held: editor.getBlocks( clientId )[ 0 ] || null,
				next: nextId ? editor.getBlock( nextId ) : null,
				rootClientId: root,
				index: editor.getBlockIndex( clientId ),
			};
		},
		[ clientId ]
	);
	const { moveBlocksToPosition } = useDispatch( blockEditorStore );
	const allowed = useMemo(
		() => types.filter( fits ).map( ( type ) => type.name ),
		[ types ]
	);
	const titles = useMemo(
		() =>
			types
				.filter( fits )
				.map( ( type ) => type.title )
				.join( ', ' ),
		[ types ]
	);

	// Switching layout carries the block with it: two columns takes the
	// block below into the right column (if it fits there); stacked puts the
	// held block back below as the next block.
	const changeLayout = ( value ) => {
		if (
			value === 'columns' &&
			! held &&
			next &&
			fits( getBlockType( next.name ) )
		) {
			moveBlocksToPosition(
				[ next.clientId ],
				rootClientId,
				clientId,
				0
			);
		}
		if ( value === 'stacked' && held ) {
			moveBlocksToPosition(
				[ held.clientId ],
				clientId,
				rootClientId,
				index + 1
			);
		}
		setAttributes( { layout: value } );
	};

	const blockProps = useFloeBlockProps( name, {
		className: `block-intro--${ columns ? 'columns' : 'stacked' }`,
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'block-intro__block' },
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
						help={
							columns
								? sprintf(
										/* translators: %s: list of block names. */
										__(
											'The block this introduces sits on the right: %s.',
											'floe'
										),
										titles
									)
								: __(
										'Add the block this introduces below it.',
										'floe'
									)
						}
						value={ layout }
						options={ [
							{
								label: __( 'Stacked', 'floe' ),
								value: 'stacked',
							},
							{
								label: __( 'Two columns', 'floe' ),
								value: 'columns',
							},
						] }
						onChange={ changeLayout }
					/>
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="block-intro__inner">
					<EditableSectionHeader
						attributes={ attributes }
						setAttributes={ setAttributes }
						layout={ columns ? 'stacked' : 'split' }
						action
						actionStyle={ columns ? 'link' : 'secondary' }
						placeholders={ {
							heading: __( 'Heading', 'floe' ),
							action: __( 'Optional button', 'floe' ),
						} }
					/>
					{ columns && <div { ...innerBlocksProps } /> }
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
	transforms: {
		// "Transform to Block intro" on a block that suits the right-hand
		// column: it moves into a new two-column Block intro.
		from: [
			{
				type: 'block',
				blocks: [ '*' ],
				isMatch: ( attributes, block ) =>
					!! block?.name && fits( getBlockType( block.name ) ),
				__experimentalConvert: ( block ) =>
					createBlock( metadata.name, { layout: 'columns' }, [
						createBlock(
							block.name,
							block.attributes,
							block.innerBlocks
						),
					] ),
			},
		],
	},
} );
