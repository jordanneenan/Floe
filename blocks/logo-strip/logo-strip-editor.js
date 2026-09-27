import { registerBlockType } from '@wordpress/blocks';
import {
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	BlockControls,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	useIsEditing,
	hasText,
} from '@floe/editor';
import { Carousel } from '@floe/components/carousel';
import metadata from './block.json';

const MASKED = [ 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' ];

function Logo( { id } ) {
	const media = useSelect(
		( select ) =>
			select( coreStore ).getEntityRecord( 'postType', 'attachment', id, {
				context: 'view',
			} ),
		[ id ]
	);
	if ( ! media ) {
		return null;
	}
	const size =
		media.media_details?.sizes?.mobile || media.media_details || {};
	const url =
		media.media_details?.sizes?.mobile?.source_url || media.source_url;
	const ratio =
		size.width && size.height
			? ( size.width / size.height ).toFixed( 4 )
			: 3;
	const alt = media.alt_text || media.title?.rendered || '';
	return (
		<li className="logo-strip__item">
			{ MASKED.includes( media.mime_type ) ? (
				<span
					className="logo-strip__logo logo-strip__logo--mask"
					role="img"
					aria-label={ alt }
					style={ { '--logo': `url(${ url })`, '--ratio': ratio } }
				/>
			) : (
				<img
					className="logo-strip__logo logo-strip__logo--image"
					src={ url }
					alt={ alt }
					style={ { '--ratio': ratio } }
				/>
			) }
		</li>
	);
}

function Edit( { attributes, setAttributes, name } ) {
	const { logos, ticker } = attributes;
	const limit = ticker ? 24 : 8;
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	const blockProps = useFloeBlockProps( name );
	const onSelect = ( items ) =>
		setAttributes( {
			logos: items.slice( 0, 24 ).map( ( item ) => ( { id: item.id } ) ),
		} );
	const ids = logos.slice( 0, limit ).map( ( logo ) => logo.id );
	const list = ids.map( ( id ) => <Logo key={ id } id={ id } /> );

	return (
		<>
			{ !! logos.length && (
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
										{ __( 'Edit logos', 'floe' ) }
									</ToolbarButton>
								) }
							/>
						</ToolbarGroup>
					</MediaUploadCheck>
				</BlockControls>
			) }
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Scroll as a ticker', 'floe' ) }
						help={
							ticker
								? __(
										'The logos scroll past continuously (up to 24). Visitors can pause it, and it stays still for anyone who prefers reduced motion.',
										'floe'
									)
								: __(
										'Four to eight logos in one row. Turn this on to show more, scrolling past continuously.',
										'floe'
									)
						}
						checked={ ticker }
						onChange={ ( next ) =>
							setAttributes( { ticker: next } )
						}
					/>
					{ logos.length > limit && (
						<p>
							{ __(
								'Only the first eight logos show in a row. Turn on the ticker to show them all.',
								'floe'
							) }
						</p>
					) }
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="logo-strip__inner">
					{ ( editing || hasText( attributes.label ) ) &&
						text(
							'label',
							__(
								'Short label, e.g. Trusted by teams at',
								'floe'
							),
							{ allowedFormats: [] }
						)( { tagName: 'p', className: 'logo-strip__label' } ) }
					{ logos.length && ticker ? (
						<Carousel
							mode="ticker"
							list
							className="logo-strip__ticker"
						>
							{ list }
						</Carousel>
					) : null }
					{ logos.length && ! ticker ? (
						<ul className="logo-strip__logos">{ list }</ul>
					) : null }
					{ ! logos.length && (
						<MediaPlaceholder
							labels={ {
								title: __( 'Logos', 'floe' ),
								instructions: __(
									'Choose four to eight logos, or up to 24 for a ticker. Transparent PNG or WebP files work best: they’re shown in one muted colour.',
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
