import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
import { Eyebrow, Heading } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	return <section { ...useBlockProps( { className: 'floe-section floe-document-download' } ) }><div className="floe-section__inner"><Eyebrow attributes={ attributes } setAttributes={ setAttributes } /><Heading attributes={ attributes } setAttributes={ setAttributes } /><div className="floe-document-download__items"><InnerBlocks allowedBlocks={ [ 'floe/download-item' ] } template={ [ [ 'floe/download-item' ], [ 'floe/download-item' ] ] } renderAppender={ InnerBlocks.ButtonBlockAppender } /></div></div></section>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
