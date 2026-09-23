import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export function Eyebrow( { attributes, setAttributes, placeholder = 'SECTION LABEL' } ) {
	return <RichText tagName="p" className="floe-section__eyebrow" value={ attributes.eyebrow } onChange={ ( eyebrow ) => setAttributes( { eyebrow } ) } placeholder={ placeholder } allowedFormats={ [] } />;
}

export function Heading( { attributes, setAttributes, tagName = 'h2', placeholder = 'Section heading' } ) {
	return <RichText tagName={ tagName } className="floe-section__heading" value={ attributes.heading } onChange={ ( heading ) => setAttributes( { heading } ) } placeholder={ placeholder } allowedFormats={ [] } />;
}

export function Body( { attributes, setAttributes, placeholder = 'Add supporting copy…' } ) {
	return <RichText tagName="p" className="floe-section__body" value={ attributes.body } onChange={ ( body ) => setAttributes( { body } ) } placeholder={ placeholder } />;
}

export function ActionSettings( { attributes, setAttributes } ) {
	return <InspectorControls>
		<PanelBody title={ __( 'Button', 'floe' ) } initialOpen={ false }>
			<TextControl label={ __( 'Label', 'floe' ) } value={ attributes.buttonLabel || '' } onChange={ ( buttonLabel ) => setAttributes( { buttonLabel } ) } />
			<TextControl label={ __( 'Link URL', 'floe' ) } type="url" value={ attributes.buttonUrl || '' } onChange={ ( buttonUrl ) => setAttributes( { buttonUrl } ) } help={ __( 'Both fields are needed to show the button.', 'floe' ) } />
		</PanelBody>
	</InspectorControls>;
}

export function ActionPreview( { attributes } ) {
	return attributes.buttonLabel && attributes.buttonUrl ? <span className="floe-button">{ attributes.buttonLabel }</span> : null;
}

export function SurfaceSettings( { attributes, setAttributes } ) {
	return <InspectorControls>
		<PanelBody title={ __( 'Section background', 'floe' ) } initialOpen={ false }>
			<SelectControl label={ __( 'Surface', 'floe' ) } value={ attributes.surface || 'base' } options={ [
				{ label: __( 'Base', 'floe' ), value: 'base' },
				{ label: __( 'Subtle', 'floe' ), value: 'subtle' },
				{ label: __( 'Accent', 'floe' ), value: 'accent' },
			] } onChange={ ( surface ) => setAttributes( { surface } ) } />
		</PanelBody>
	</InspectorControls>;
}

export function MediaPicker( { attributes, setAttributes, prefix = 'media', label = __( 'Media', 'floe' ), allowVideo = true } ) {
	const id = attributes[ `${ prefix }Id` ];
	const type = attributes[ `${ prefix }Type` ] || 'image';
	const url = attributes[ `${ prefix }Url` ];
	const poster = attributes[ `${ prefix }PosterUrl` ];
	const setMedia = ( item, nextType ) => setAttributes( {
		[ `${ prefix }Id` ]: item.id,
		[ `${ prefix }Type` ]: nextType,
		[ `${ prefix }Url` ]: item.url,
		[ `${ prefix }Alt` ]: nextType === 'image' ? ( item.alt || '' ) : '',
		[ `${ prefix }PosterId` ]: nextType === 'image' ? 0 : attributes[ `${ prefix }PosterId` ],
		[ `${ prefix }PosterUrl` ]: nextType === 'image' ? '' : attributes[ `${ prefix }PosterUrl` ],
	} );
	const clear = () => setAttributes( {
		[ `${ prefix }Id` ]: 0,
		[ `${ prefix }Url` ]: '',
		[ `${ prefix }PosterId` ]: 0,
		[ `${ prefix }PosterUrl` ]: '',
	} );

	return <div className="floe-media-editor">
		<div className="floe-media-editor__visual">
			{ id && 'image' === type && url ? <img src={ url } alt={ attributes[ `${ prefix }Alt` ] || '' } /> : null }
			{ id && 'video' === type && poster ? <img src={ poster } alt="" /> : null }
			{ ! id || ( 'video' === type && ! poster ) ? <span>{ 'video' === type ? __( 'Choose a poster image for this looping MP4', 'floe' ) : label }</span> : null }
			{ id && 'video' === type ? <small>{ __( 'Silent looping MP4', 'floe' ) }</small> : null }
		</div>
		<div className="floe-media-editor__controls">
			<MediaUploadCheck><MediaUpload onSelect={ ( item ) => setMedia( item, 'image' ) } allowedTypes={ [ 'image' ] } value={ 'image' === type ? id : 0 } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ __( 'Choose image', 'floe' ) }</Button> } /></MediaUploadCheck>
			{ allowVideo ? <MediaUploadCheck><MediaUpload onSelect={ ( item ) => setMedia( item, 'video' ) } allowedTypes={ [ 'video/mp4' ] } value={ 'video' === type ? id : 0 } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ __( 'Choose MP4', 'floe' ) }</Button> } /></MediaUploadCheck> : null }
			{ id && 'video' === type ? <MediaUploadCheck><MediaUpload onSelect={ ( item ) => setAttributes( { [ `${ prefix }PosterId` ]: item.id, [ `${ prefix }PosterUrl` ]: item.url } ) } allowedTypes={ [ 'image' ] } value={ attributes[ `${ prefix }PosterId` ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ __( 'Choose poster', 'floe' ) }</Button> } /></MediaUploadCheck> : null }
			{ id ? <Button variant="tertiary" isDestructive onClick={ clear }>{ __( 'Remove', 'floe' ) }</Button> : null }
		</div>
		{ id && 'image' === type ? <TextControl label={ __( 'Alternative text', 'floe' ) } value={ attributes[ `${ prefix }Alt` ] || '' } onChange={ ( alt ) => setAttributes( { [ `${ prefix }Alt` ]: alt } ) } /> : null }
	</div>;
}

export const mediaAttributes = {
	mediaId: { type: 'number', default: 0 },
	mediaType: { type: 'string', default: 'image' },
	mediaUrl: { type: 'string', default: '' },
	mediaAlt: { type: 'string', default: '' },
	mediaPosterId: { type: 'number', default: 0 },
	mediaPosterUrl: { type: 'string', default: '' },
};
