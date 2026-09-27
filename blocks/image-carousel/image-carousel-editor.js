import { registerBlockType, createBlock } from '@wordpress/blocks';
import {
	BlockControls,
	InnerBlocks,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import { Carousel } from '@floe/components/carousel';
import metadata from './block.json';

const RATIOS = [
	{ label: __( 'Wide (16:9)', 'floe' ), value: '16/9' },
	{ label: __( 'Landscape (3:2)', 'floe' ), value: '3/2' },
	{ label: __( 'Classic (4:3)', 'floe' ), value: '4/3' },
	{ label: __( 'Square (1:1)', 'floe' ), value: '1/1' },
];

function Edit( { attributes, setAttributes, clientId, name, context } ) {
	const blockProps = useFloeBlockProps( name, {}, { context } );
	const count = useSelect(
		( select ) => select( blockEditorStore ).getBlockCount( clientId ),
		[ clientId ]
	);
	const { insertBlocks } = useDispatch( blockEditorStore );
	const trackProps = useInnerBlocksProps(
		{},
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [ [ 'floe/slide' ], [ 'floe/slide' ] ],
			orientation: 'horizontal',
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);
	// Several images at once: each becomes a Slide at the end.
	const addImages = ( items ) =>
		insertBlocks(
			items.map( ( item ) =>
				createBlock( 'floe/slide', { media: { id: item.id } } )
			),
			count,
			clientId
		);

	return (
		<>
			<BlockControls>
				<MediaUploadCheck>
					<ToolbarGroup>
						<MediaUpload
							multiple
							allowedTypes={ [ 'image', 'video' ] }
							onSelect={ addImages }
							render={ ( { open } ) => (
								<ToolbarButton onClick={ open }>
									{ __( 'Add images', 'floe' ) }
								</ToolbarButton>
							) }
						/>
					</ToolbarGroup>
				</MediaUploadCheck>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Shape', 'floe' ) }
						help={ __(
							'Every slide is cropped to this shape, so the carousel keeps the same height as it moves.',
							'floe'
						) }
						value={ attributes.ratio }
						options={ RATIOS }
						onChange={ ( ratio ) => setAttributes( { ratio } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="image-carousel__inner">
					<Carousel
						perView={ [ 1.08, 1.08, 1 ] }
						trackProps={ trackProps }
					/>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
