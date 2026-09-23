import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
import { Eyebrow, Heading } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	return <section { ...useBlockProps( { className: 'floe-section floe-images' } ) }><div className="floe-section__inner"><Eyebrow attributes={ attributes } setAttributes={ setAttributes } /><Heading attributes={ attributes } setAttributes={ setAttributes } /><div className="floe-images__rows"><InnerBlocks allowedBlocks={ [ 'floe/image-row' ] } template={ [ [ 'floe/image-row', { columns: 1 }, [ [ 'floe/media' ] ] ], [ 'floe/image-row', { columns: 2 }, [ [ 'floe/media' ], [ 'floe/media' ] ] ] ] } renderAppender={ InnerBlocks.ButtonBlockAppender } /></div></div></section>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
