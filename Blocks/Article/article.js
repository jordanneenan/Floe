import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
import { ActionPreview, ActionSettings, SurfaceSettings } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( { className: 'floe-section floe-article floe-surface--' + ( attributes.surface || 'base' ) } );
	return <div { ...blockProps }><ActionSettings attributes={ attributes } setAttributes={ setAttributes } /><SurfaceSettings attributes={ attributes } setAttributes={ setAttributes } /><div className="floe-section__inner"><div className="floe-article__content"><InnerBlocks template={ [ [ 'core/heading', { level: 2, placeholder: 'Your story, in your words' } ], [ 'core/paragraph', { placeholder: 'Write freely with native WordPress blocks…' } ] ] } /></div><ActionPreview attributes={ attributes } /></div></div>;
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
