import { registerBlockType } from '@wordpress/blocks';
import { MediaPlaceholder, MediaUpload, MediaUploadCheck, BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, useText } from '@floe/editor';
import metadata from './block.json';

const MASKED = [ 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' ];

function Logo( { id } ) {
	const media = useSelect( ( select ) => select( coreStore ).getMedia( id, { context: 'view' } ), [ id ] );
	if ( ! media ) {
		return null;
	}
	const size = media.media_details?.sizes?.mobile || media.media_details || {};
	const url = media.media_details?.sizes?.mobile?.source_url || media.source_url;
	const ratio = size.width && size.height ? ( size.width / size.height ).toFixed( 4 ) : 3;
	const alt = media.alt_text || media.title?.rendered || '';
	return (
		<li className="logo-strip__item">
			{ MASKED.includes( media.mime_type ) ? (
				<span className="logo-strip__logo logo-strip__logo--mask" role="img" aria-label={ alt } style={ { '--logo': `url(${ url })`, '--ratio': ratio } } />
			) : (
				<img className="logo-strip__logo logo-strip__logo--image" src={ url } alt={ alt } style={ { '--ratio': ratio } } />
			) }
		</li>
	);
}

function Edit( { attributes, setAttributes, name } ) {
	const { logos } = attributes;
	const text = useText( attributes, setAttributes );
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const onSelect = ( items ) => setAttributes( { logos: items.slice( 0, 8 ).map( ( item ) => ( { id: item.id } ) ) } );
	const ids = logos.map( ( logo ) => logo.id );

	return (
		<>
			{ !! logos.length && (
				<BlockControls>
					<MediaUploadCheck>
						<ToolbarGroup>
							<MediaUpload multiple gallery addToGallery allowedTypes={ [ 'image' ] } value={ ids } onSelect={ onSelect } render={ ( { open } ) => <ToolbarButton onClick={ open }>{ __( 'Edit logos', 'floe' ) }</ToolbarButton> } />
						</ToolbarGroup>
					</MediaUploadCheck>
				</BlockControls>
			) }
			<section { ...blockProps }>
				<div className="logo-strip__inner">
					{ text( 'label', __( 'Short label, e.g. Trusted by teams at', 'floe' ), { allowedFormats: [] } )( { tagName: 'p', className: 'logo-strip__label' } ) }
					{ logos.length ? (
						<ul className="logo-strip__logos">{ ids.map( ( id ) => <Logo key={ id } id={ id } /> ) }</ul>
					) : (
						<MediaPlaceholder
							labels={ { title: __( 'Logos', 'floe' ), instructions: __( 'Choose four to eight logos. Transparent PNG or WebP files work best: they’re shown in one muted colour.', 'floe' ) } }
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
