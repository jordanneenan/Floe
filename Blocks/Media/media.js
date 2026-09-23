import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
import { MediaPicker } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	return <div { ...useBlockProps( { className: 'floe-media-item' } ) }><MediaPicker attributes={ attributes } setAttributes={ setAttributes } label="Choose an image or silent looping MP4" /></div>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => null } );
