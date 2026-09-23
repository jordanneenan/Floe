import { registerBlockType } from '@wordpress/blocks';
import { MediaUpload, MediaUploadCheck, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	return <div { ...useBlockProps( { className: 'floe-file-action' } ) }><span className="floe-file-action__type">FILE</span><div className="floe-file-action__info"><RichText tagName="strong" value={ attributes.title } onChange={ ( title ) => setAttributes( { title } ) } placeholder="Document title" allowedFormats={ [] } />{ attributes.fileId ? <small>Selected document</small> : <small>Choose a document</small> }</div><MediaUploadCheck><MediaUpload allowedTypes={ [ 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ] } value={ attributes.fileId } onSelect={ ( item ) => setAttributes( { fileId: item.id, fileUrl: item.url, title: attributes.title || item.title || item.filename || '' } ) } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ attributes.fileId ? 'Replace file' : 'Choose file' }</Button> } /></MediaUploadCheck></div>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => null } );
