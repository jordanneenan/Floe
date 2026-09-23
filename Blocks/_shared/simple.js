import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ActionPreview, ActionSettings, Body, Eyebrow, Heading, MediaPicker, SurfaceSettings } from './editor';

export function createSimpleEdit( kind ) {
	return function Edit( { attributes, setAttributes } ) {
		const surface = attributes.surface || ( 'cta' === kind ? 'accent' : 'base' );
		const classes = [ 'floe-section', `floe-${ kind }`, `floe-surface--${ surface }` ];
		if ( attributes.showMedia || attributes.mediaId ) classes.push( 'has-media' );
		const blockProps = useBlockProps( { className: classes.join( ' ' ) } );
		const action = <><ActionSettings attributes={ attributes } setAttributes={ setAttributes } /><ActionPreview attributes={ attributes } /></>;
		const heading = ( tagName = 'h2' ) => <Heading attributes={ attributes } setAttributes={ setAttributes } tagName={ tagName } />;
		const body = <Body attributes={ attributes } setAttributes={ setAttributes } />;
		const eyebrow = <Eyebrow attributes={ attributes } setAttributes={ setAttributes } />;
		const media = <MediaPicker attributes={ attributes } setAttributes={ setAttributes } />;
		const surfaceControl = <SurfaceSettings attributes={ attributes } setAttributes={ setAttributes } />;

		if ( 'home-banner' === kind ) {
			return <div { ...blockProps }><div className="floe-section__inner floe-home-banner__grid"><div className="floe-home-banner__copy">{ eyebrow }{ heading( 'h1' ) }{ body }{ action }</div>{ media }</div></div>;
		}
		if ( 'page-banner' === kind ) {
			return <div { ...blockProps }>
				<InspectorControls><PanelBody title={ __( 'Page banner options', 'floe' ) }><ToggleControl label={ __( 'Show media', 'floe' ) } checked={ !! attributes.showMedia } onChange={ ( showMedia ) => setAttributes( { showMedia } ) } /></PanelBody></InspectorControls>
				<div className="floe-section__inner floe-page-banner__grid"><div className="floe-page-banner__copy"><RichText tagName="p" className="floe-section__eyebrow" value={ attributes.breadcrumb } onChange={ ( breadcrumb ) => setAttributes( { breadcrumb } ) } placeholder={ __( 'Optional breadcrumb', 'floe' ) } allowedFormats={ [] } />{ heading( 'h1' ) }{ body }{ action }</div>{ attributes.showMedia ? media : null }</div>
			</div>;
		}
		if ( 'image-copy' === kind ) {
			return <div { ...blockProps }>
				<InspectorControls><PanelBody title={ __( 'Layout', 'floe' ) }><ToggleControl label={ __( 'Media on right', 'floe' ) } checked={ !! attributes.mediaRight } onChange={ ( mediaRight ) => setAttributes( { mediaRight } ) } /></PanelBody></InspectorControls>
				<div className={ `floe-section__inner floe-image-copy__grid${ attributes.mediaRight ? ' is-reversed' : '' }` }>{ media }<div className="floe-image-copy__copy">{ eyebrow }{ heading() }{ body }{ action }</div></div>
			</div>;
		}
		if ( 'cta' === kind ) {
			return <div { ...blockProps }>{ surfaceControl }<div className="floe-section__inner floe-cta__content">{ heading() }{ body }{ action }</div></div>;
		}
		if ( 'testimonial' === kind ) {
			return <div { ...blockProps }>
				<InspectorControls><PanelBody title={ __( 'Testimonial options', 'floe' ) }><ToggleControl label={ __( 'Show portrait', 'floe' ) } checked={ !! attributes.showMedia } onChange={ ( showMedia ) => setAttributes( { showMedia } ) } /></PanelBody></InspectorControls>
				<div className="floe-section__inner floe-testimonial__grid"><div className="floe-testimonial__copy"><span className="floe-testimonial__mark" aria-hidden="true">“</span><RichText tagName="blockquote" className="floe-testimonial__quote" value={ attributes.quote } onChange={ ( quote ) => setAttributes( { quote } ) } placeholder={ __( 'Add the testimonial quote', 'floe' ) } /><RichText tagName="p" className="floe-testimonial__attribution" value={ attributes.attribution } onChange={ ( attribution ) => setAttributes( { attribution } ) } placeholder={ __( 'Name and organisation', 'floe' ) } allowedFormats={ [] } /></div>{ attributes.showMedia ? media : null }</div>
			</div>;
		}
		if ( 'video' === kind ) {
			return <div { ...blockProps }>
				<InspectorControls><PanelBody title={ __( 'YouTube video', 'floe' ) }><TextControl label={ __( 'YouTube URL', 'floe' ) } type="url" value={ attributes.youtubeUrl || '' } onChange={ ( youtubeUrl ) => setAttributes( { youtubeUrl } ) } help={ __( 'The player loads only after a visitor presses play.', 'floe' ) } /></PanelBody></InspectorControls>
				<div className="floe-section__inner">{ eyebrow }{ heading() }<div className="floe-video__cover"><MediaPicker attributes={ attributes } setAttributes={ setAttributes } prefix="cover" label={ __( 'Choose a YouTube cover image', 'floe' ) } allowVideo={ false } /><span className="floe-video__play">▶ { __( 'Play video', 'floe' ) }</span></div>{ body }</div>
			</div>;
		}
		return null;
	};
}
