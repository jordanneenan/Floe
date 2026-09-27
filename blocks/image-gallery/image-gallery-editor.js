import { registerBlockType } from '@wordpress/blocks';
import {
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, useMedia } from '@floe/editor';
import { Media } from '@floe/components/media';
import metadata from './block.json';

function Item( { id, layout } ) {
	const resolved = useMedia( { id } );
	return (
		<li className="image-gallery__item">
			<Media
				url={ resolved.url }
				alt={ resolved.alt }
				ratio={ layout === 'grid' ? '4/3' : '' }
				cover={ layout === 'mosaic' }
				radius="md"
				placeholder={ ! resolved.url }
			/>
		</li>
	);
}

function Edit( { attributes, setAttributes, name, context } ) {
	const { images, layout, columns, lightbox } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{
			className: `image-gallery--${ layout }`,
			style: { '--image-gallery-columns': columns },
		},
		{ context }
	);
	const ids = images.map( ( image ) => image.id );
	const onSelect = ( items ) =>
		setAttributes( {
			images: items.map( ( item ) => ( { id: item.id } ) ),
		} );

	return (
		<>
			{ !! images.length && (
				<BlockControls>
					<MediaUploadCheck>
						<ToolbarGroup>
							<MediaUpload
								multiple
								gallery
								addToGallery
								allowedTypes={ [ 'image' ] }
								value={ ids }
								onSelect={ onSelect }
								render={ ( { open } ) => (
									<ToolbarButton onClick={ open }>
										{ __( 'Edit images', 'floe' ) }
									</ToolbarButton>
								) }
							/>
						</ToolbarGroup>
					</MediaUploadCheck>
				</BlockControls>
			) }
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Layout', 'floe' ) }
						help={
							{
								grid: __(
									'Even tiles, each cropped to 4:3.',
									'floe'
								),
								mosaic: __(
									'A large image beside two small ones, swapping sides each time.',
									'floe'
								),
								masonry: __(
									'Each image keeps its own shape, in columns.',
									'floe'
								),
							}[ layout ]
						}
						value={ layout }
						options={ [
							{ label: __( 'Grid', 'floe' ), value: 'grid' },
							{ label: __( 'Mosaic', 'floe' ), value: 'mosaic' },
							{
								label: __( 'Masonry', 'floe' ),
								value: 'masonry',
							},
						] }
						onChange={ ( next ) =>
							setAttributes( { layout: next } )
						}
					/>
					{ layout !== 'mosaic' && (
						<RangeControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Columns', 'floe' ) }
							help={ __(
								'From 768px wide. Phones show two.',
								'floe'
							) }
							min={ 2 }
							max={ 4 }
							value={ columns }
							onChange={ ( next ) =>
								setAttributes( { columns: next || 3 } )
							}
						/>
					) }
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Open images in a lightbox', 'floe' ) }
						help={ __(
							'Clicking an image shows it full screen, where visitors can step through the whole gallery.',
							'floe'
						) }
						checked={ lightbox }
						onChange={ ( next ) =>
							setAttributes( { lightbox: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="image-gallery__inner">
					{ images.length ? (
						<ul className="image-gallery__grid">
							{ ids.map( ( id, index ) => (
								<Item
									key={ `${ id }-${ index }` }
									id={ id }
									layout={ layout }
								/>
							) ) }
						</ul>
					) : (
						<MediaPlaceholder
							labels={ {
								title: __( 'Gallery', 'floe' ),
								instructions: __(
									'Choose the images. You can reorder them in the media library window.',
									'floe'
								),
							} }
							allowedTypes={ [ 'image' ] }
							multiple="add"
							onSelect={ onSelect }
						/>
					) }
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
