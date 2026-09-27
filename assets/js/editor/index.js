/*
 * @floe/editor: shared editing helpers for Floe block editor scripts.
 * Not a module: blocks import it through the build alias. It only holds
 * editing UI; all markup comes from the components (@floe/components/*).
 */
import { __ } from '@wordpress/i18n';
import { useState, useRef } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { store as blocksStore } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
	MediaUpload,
	MediaUploadCheck,
	MediaPlaceholder,
	LinkControl,
	useBlockEditContext,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	Button as WPButton,
	Popover,
	SelectControl,
	TextareaControl,
	ToolbarGroup,
	ToolbarButton,
	Dropdown,
} from '@wordpress/components';
import { link as linkIcon, image as imageIcon } from '@wordpress/icons';
import { Button } from '@floe/components/button';
import { Media } from '@floe/components/media';
import { SectionHeader } from '@floe/components/section-header';

/**
 * Block wrapper props with the block's own class (its folder name), and for
 * top-level sections "floe-section" and an optional surface class, matching
 * Floe\block_attributes().
 *
 * A section held inside another (its block context has "floe/nested", set
 * by a two-column Block intro) gets neither, like the server.
 *
 * @param {string}  name            Block name, e.g. "floe/cta".
 * @param {Object}  extra           Extra props for useBlockProps.
 * @param {Object}  options         Options.
 * @param {boolean} options.section Add "floe-section" (top-level sections).
 * @param {string}  options.surface Surface slug for a "surface-*" class.
 * @param {Object}  options.context The block's context, to detect nesting.
 * @return {Object} Block wrapper props.
 */
export function useFloeBlockProps(
	name,
	extra = {},
	{ section = true, surface = '', context = {} } = {}
) {
	if ( context?.[ 'floe/nested' ] ) {
		section = false;
		surface = '';
	}
	const slug = name.split( '/' )[ 1 ];
	const className = [
		slug,
		section ? 'floe-section' : '',
		surface ? `surface-${ surface }` : '',
		extra.className,
	]
		.filter( Boolean )
		.join( ' ' );
	return useBlockProps( { ...extra, className } );
}

/**
 * Whether the block being edited is selected or holds the selected block.
 * Empty optional parts (an eyebrow, intro, note or button) show only then,
 * so a block that isn't being edited looks as it will on the page, where
 * empty parts aren't rendered.
 *
 * @return {boolean} True while the block is being edited.
 */
export function useIsEditing() {
	const { clientId } = useBlockEditContext();
	return useSelect(
		( select ) => {
			const store = select( blockEditorStore );
			return (
				store.isBlockSelected( clientId ) ||
				store.hasSelectedInnerBlock( clientId, true )
			);
		},
		[ clientId ]
	);
}

/**
 * Returns a render function for a RichText bound to an attribute, for the
 * { tagName, className } parts accepted by component twins.
 *
 * @param {Object}   attributes    Block attributes.
 * @param {Function} setAttributes Block attribute setter.
 * @return {Function} ( key, placeholder, options ) => render function.
 */
export function useText( attributes, setAttributes ) {
	return ( key, placeholder, options = {} ) =>
		( { tagName, className } ) => (
			<RichText
				identifier={ key }
				tagName={ options.tagName || tagName }
				className={
					[ className, options.className ]
						.filter( Boolean )
						.join( ' ' ) || undefined
				}
				value={ attributes[ key ] || '' }
				onChange={ ( value ) => setAttributes( { [ key ]: value } ) }
				placeholder={ placeholder }
				allowedFormats={
					options.allowedFormats ?? [
						'core/bold',
						'core/italic',
						'core/link',
					]
				}
				disableLineBreaks={ options.disableLineBreaks ?? true }
			/>
		);
}

/*
 * An editable button: the label is edited in place, the link through
 * WordPress's link picker (with page search). Value: { label, url, newTab }.
 */
export function LinkButton( {
	value,
	onChange,
	style = 'primary',
	arrow = true,
	placeholder = __( 'Button label', 'floe' ),
	className = '',
} ) {
	const [ isOpen, setOpen ] = useState( false );
	const anchor = useRef();
	const editing = useIsEditing();
	const current = value || {};

	// Empty, it shows only while its block is edited. With a label but no
	// link it stays, outlined, as the page won't show it until it's linked.
	if ( ! editing && ! hasLink( current ) ) {
		return null;
	}

	return (
		<span
			ref={ anchor }
			className={ `floe-link-button ${ current.url ? '' : 'is-unlinked' } ${ className }`.trim() }
		>
			<Button
				style={ style }
				arrow={ arrow }
				label={
					<RichText
						tagName="span"
						value={ current.label || '' }
						onChange={ ( label ) =>
							onChange( { ...current, label } )
						}
						placeholder={ placeholder }
						allowedFormats={ [] }
						withoutInteractiveFormatting
						disableLineBreaks
					/>
				}
			/>
			<WPButton
				className="floe-link-button__edit"
				icon={ linkIcon }
				size="small"
				label={
					current.url
						? __( 'Edit link', 'floe' )
						: __( 'Add link', 'floe' )
				}
				onClick={ () => setOpen( ! isOpen ) }
			/>
			{ isOpen && (
				<Popover
					anchor={ anchor.current }
					placement="bottom-start"
					onClose={ () => setOpen( false ) }
					focusOnMount="firstElement"
				>
					<LinkControl
						value={ {
							url: current.url || '',
							opensInNewTab: !! current.newTab,
						} }
						onChange={ ( next ) =>
							onChange( {
								...current,
								url: next.url || '',
								newTab: !! next.opensInNewTab,
							} )
						}
						onRemove={ () => {
							onChange( { ...current, url: '' } );
							setOpen( false );
						} }
						settings={ [
							{
								id: 'opensInNewTab',
								title: __( 'Open in new tab', 'floe' ),
							},
						] }
					/>
				</Popover>
			) }
		</span>
	);
}

/**
 * Resolve a stored media value ({ id, posterId, alt }) to URLs and type.
 *
 * @param {Object} value Stored media value.
 * @return {Object} { media, url, type, poster, alt, libraryAlt, title }.
 */
export function useMedia( value ) {
	const id = value?.id;
	const posterId = value?.posterId;
	return useSelect(
		( select ) => {
			const media = id
				? select( coreStore ).getEntityRecord(
						'postType',
						'attachment',
						id,
						{ context: 'view' }
					)
				: null;
			const poster = posterId
				? select( coreStore ).getEntityRecord(
						'postType',
						'attachment',
						posterId,
						{ context: 'view' }
					)
				: null;
			if ( ! media ) {
				return { media: null, url: '', type: 'image', alt: '' };
			}
			const isVideo = media.mime_type === 'video/mp4';
			const sizes = media.media_details?.sizes || {};
			return {
				media,
				type: isVideo ? 'video' : 'image',
				url: isVideo
					? media.source_url
					: sizes.laptop?.source_url ||
						sizes.large?.source_url ||
						media.source_url,
				poster:
					poster?.media_details?.sizes?.laptop?.source_url ||
					poster?.source_url,
				alt: value?.alt || media.alt_text || '',
				libraryAlt: media.alt_text || '',
				title: media.title?.rendered || '',
			};
		},
		[ id, posterId, value?.alt ]
	);
}

/*
 * An editable media slot: pick an image or MP4 from the library; replace,
 * remove, set a poster (MP4) and override alt text from the overlay.
 * Value: { id, posterId, alt }.
 */
export function MediaSlot( {
	value,
	onChange,
	ratio = '',
	radius = 'lg',
	cover = false,
	allowVideo = true,
	label = __( 'Image or looping MP4', 'floe' ),
	placeholder = false,
	className = '',
} ) {
	const resolved = useMedia( value );
	const allowedTypes = allowVideo ? [ 'image', 'video' ] : [ 'image' ];
	const select = ( media ) => {
		if (
			media?.type === 'video' &&
			media.mime !== 'video/mp4' &&
			media.mime_type !== 'video/mp4'
		) {
			return;
		}
		onChange( {
			id: media?.id,
			posterId: value?.posterId,
			alt: value?.alt,
		} );
	};

	if ( ! value?.id ) {
		return (
			<div
				className={ `floe-media-slot is-empty ${ className }`.trim() }
				style={ ratio ? { aspectRatio: ratio } : undefined }
			>
				<MediaPlaceholder
					icon={ imageIcon }
					labels={ {
						title: label,
						instructions: allowVideo
							? __(
									'Choose an image, or an MP4 that will loop silently.',
									'floe'
								)
							: __( 'Choose an image.', 'floe' ),
					} }
					allowedTypes={ allowedTypes }
					onSelect={ select }
					multiple={ false }
				/>
			</div>
		);
	}

	return (
		<div className={ `floe-media-slot ${ className }`.trim() }>
			<Media
				url={ resolved.url }
				type={ resolved.type }
				poster={ resolved.poster }
				alt={ resolved.alt }
				ratio={ ratio }
				radius={ radius }
				cover={ cover }
				placeholder={ placeholder || ! resolved.url }
			/>
			<MediaUploadCheck>
				<div className="floe-media-slot__tools">
					<ToolbarGroup>
						<MediaUpload
							allowedTypes={ allowedTypes }
							value={ value.id }
							onSelect={ select }
							render={ ( { open } ) => (
								<ToolbarButton onClick={ open }>
									{ __( 'Replace', 'floe' ) }
								</ToolbarButton>
							) }
						/>
						{ resolved.type === 'video' && (
							<MediaUpload
								allowedTypes={ [ 'image' ] }
								value={ value.posterId }
								onSelect={ ( poster ) =>
									onChange( {
										...value,
										posterId: poster?.id,
									} )
								}
								render={ ( { open } ) => (
									<ToolbarButton onClick={ open }>
										{ value.posterId
											? __( 'Poster', 'floe' )
											: __( 'Add poster', 'floe' ) }
									</ToolbarButton>
								) }
							/>
						) }
						<Dropdown
							popoverProps={ { placement: 'bottom-end' } }
							renderToggle={ ( { isOpen, onToggle } ) => (
								<ToolbarButton
									onClick={ onToggle }
									aria-expanded={ isOpen }
								>
									{ __( 'Alt text', 'floe' ) }
								</ToolbarButton>
							) }
							renderContent={ () => (
								<div className="floe-media-slot__alt">
									<TextareaControl
										__nextHasNoMarginBottom
										label={ __(
											'Alternative text for this slot',
											'floe'
										) }
										help={ __(
											'Leave empty to use the alt text from the media library.',
											'floe'
										) }
										placeholder={ resolved.libraryAlt }
										value={ value.alt || '' }
										onChange={ ( alt ) =>
											onChange( { ...value, alt } )
										}
									/>
								</div>
							) }
						/>
						<ToolbarButton onClick={ () => onChange( {} ) }>
							{ __( 'Remove', 'floe' ) }
						</ToolbarButton>
					</ToolbarGroup>
				</div>
			</MediaUploadCheck>
		</div>
	);
}

/*
 * The section header with every part editable. Attributes used: eyebrow,
 * heading, headingLevel, intro and action (a { label, url, newTab } object).
 */
export function EditableSectionHeader( {
	attributes,
	setAttributes,
	layout = 'split',
	intro = true,
	action = false,
	actionStyle = 'secondary',
	actionArrow = true,
	aside,
	placeholders = {},
} ) {
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	return (
		<SectionHeader
			layout={ layout }
			headingLevel={ attributes.headingLevel || 2 }
			eyebrow={
				editing || hasText( attributes.eyebrow )
					? text(
							'eyebrow',
							placeholders.eyebrow || __( 'Eyebrow', 'floe' ),
							{ allowedFormats: [] }
						)
					: null
			}
			heading={ text(
				'heading',
				placeholders.heading || __( 'Section heading', 'floe' )
			) }
			intro={
				intro && ( editing || hasText( attributes.intro ) )
					? text(
							'intro',
							placeholders.intro ||
								__( 'Optional supporting line', 'floe' )
						)
					: null
			}
			action={
				action && ( editing || hasLink( attributes.action ) ) ? (
					<LinkButton
						value={ attributes.action }
						onChange={ ( value ) =>
							setAttributes( { action: value } )
						}
						style={ actionStyle }
						arrow={ actionArrow }
						placeholder={
							placeholders.action ||
							__( 'Optional action', 'floe' )
						}
					/>
				) : null
			}
			aside={ aside }
		/>
	);
}

export function HeadingLevelControl( {
	value = 2,
	onChange,
	levels = [ 2, 3, 4 ],
} ) {
	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ __( 'Heading level', 'floe' ) }
			help={ __(
				'Keep headings in order down the page. Only banners use H1.',
				'floe'
			) }
			value={ String( value ) }
			options={ levels.map( ( level ) => ( {
				label: `H${ level }`,
				value: String( level ),
			} ) ) }
			onChange={ ( next ) => onChange( parseInt( next, 10 ) ) }
		/>
	);
}

export function SurfaceControl( { value, onChange, options, context = {} } ) {
	if ( context?.[ 'floe/nested' ] ) {
		return (
			<p>
				{ __(
					'The background comes from the Block intro this block sits in.',
					'floe'
				) }
			</p>
		);
	}
	const labels = {
		base: __( 'Base (white)', 'floe' ),
		subtle: __( 'Subtle (grey)', 'floe' ),
		tint: __( 'Tint (ice blue)', 'floe' ),
		inverse: __( 'Inverse (ink)', 'floe' ),
		accent: __( 'Accent (blue)', 'floe' ),
	};
	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ __( 'Surface', 'floe' ) }
			value={ value }
			options={ options.map( ( option ) => ( {
				label: labels[ option ] || option,
				value: option,
			} ) ) }
			onChange={ onChange }
		/>
	);
}

/**
 * Blocks a form slot accepts: any non-Floe block (a form plugin's block, or
 * the Shortcode block), plus Floe blocks made for this parent (Form).
 *
 * @param {string} name The slot block's name, e.g. "floe/contact".
 * @return {string[]} Allowed block names.
 */
export function useFormSlotBlocks( name ) {
	return useSelect(
		( select ) =>
			select( blocksStore )
				.getBlockTypes()
				.filter(
					( type ) =>
						! type.name.startsWith( 'floe/' ) ||
						( type.parent || [] ).includes( name )
				)
				.map( ( type ) => type.name ),
		[ name ]
	);
}

/**
 * Strip tags for places that need plain text (e.g. accessible names).
 *
 * @param {string} html Text that may contain HTML.
 * @return {string} Plain text.
 */
export const plain = ( html = '' ) => html.replace( /<[^>]+>/g, '' ).trim();

/**
 * Whether a text attribute has visible text, as the PHP templates check
 * before rendering a part.
 *
 * @param {string} html Attribute value.
 * @return {boolean} True when there is text.
 */
export const hasText = ( html ) => plain( html || '' ) !== '';

/**
 * Whether a link attribute ({ label, url, newTab }) holds anything yet.
 *
 * @param {Object} link Attribute value.
 * @return {boolean} True when it has a label or a URL.
 */
export const hasLink = ( link ) => !! ( link?.label || link?.url );
