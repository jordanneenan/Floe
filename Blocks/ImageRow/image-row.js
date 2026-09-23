import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const columns = Math.max( 1, Math.min( 3, attributes.columns || 2 ) );
	const classes = 'floe-image-row floe-image-row--' + columns + ( attributes.gap ? '' : ' is-gapless' ) + ( attributes.fit === 'natural' ? ' is-natural' : '' );
	return <div { ...useBlockProps( { className: classes } ) }><InspectorControls><PanelBody title="Row layout"><SelectControl label="Columns" value={ String( columns ) } options={ [ { label: 'One', value: '1' }, { label: 'Two', value: '2' }, { label: 'Three', value: '3' } ] } onChange={ ( value ) => setAttributes( { columns: Number( value ) } ) } /><ToggleControl label="Gap between media" checked={ !! attributes.gap } onChange={ ( gap ) => setAttributes( { gap } ) } /><SelectControl label="Image fit" value={ attributes.fit || 'cover' } options={ [ { label: 'Fill container', value: 'cover' }, { label: 'Natural aspect ratio', value: 'natural' } ] } onChange={ ( fit ) => setAttributes( { fit } ) } /></PanelBody></InspectorControls><InnerBlocks allowedBlocks={ [ 'floe/media' ] } template={ Array.from( { length: columns }, () => [ 'floe/media' ] ) } renderAppender={ InnerBlocks.ButtonBlockAppender } /></div>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
