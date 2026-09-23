import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
import { MediaPicker } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	return <article { ...useBlockProps( { className: 'floe-card' } ) }>
		<InspectorControls><PanelBody title="Card link" initialOpen={ false }><TextControl label="Link label" value={ attributes.linkLabel || '' } onChange={ ( linkLabel ) => setAttributes( { linkLabel } ) } /><TextControl label="Link URL" type="url" value={ attributes.linkUrl || '' } onChange={ ( linkUrl ) => setAttributes( { linkUrl } ) } /></PanelBody></InspectorControls>
		<MediaPicker attributes={ attributes } setAttributes={ setAttributes } />
		<div className="floe-card__content"><RichText tagName="p" className="floe-card__meta" value={ attributes.meta } onChange={ ( meta ) => setAttributes( { meta } ) } placeholder="Category or label" allowedFormats={ [] } /><RichText tagName="h3" className="floe-card__title" value={ attributes.title } onChange={ ( title ) => setAttributes( { title } ) } placeholder="Card title" allowedFormats={ [] } /><RichText tagName="p" className="floe-card__description" value={ attributes.description } onChange={ ( description ) => setAttributes( { description } ) } placeholder="Card description" />{ attributes.linkLabel && attributes.linkUrl ? <span className="floe-card__link">{ attributes.linkLabel }</span> : null }</div>
	</article>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => null } );
