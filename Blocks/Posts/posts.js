import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';
import { Eyebrow, Heading, MediaPicker } from '../_shared/editor';

function Edit( { attributes, setAttributes } ) {
	const { mode, categoryId, selectedIds, count } = attributes;
	const { posts, categories } = useSelect( ( select ) => {
		const core = select( 'core' );
		const query = { per_page: Math.max( 1, Math.min( 12, count || 3 ) ), orderby: 'date', order: 'desc' };
		if ( mode === 'category' && categoryId ) query.categories = categoryId;
		if ( mode === 'selected' ) { query.include = ( selectedIds || '' ).split( ',' ).map( ( id ) => Number( id.trim() ) ).filter( Boolean ); query.orderby = 'include'; }
		return { posts: core.getEntityRecords( 'postType', 'post', query ), categories: core.getEntityRecords( 'taxonomy', 'category', { per_page: 100 } ) };
	}, [ mode, categoryId, selectedIds, count ] );
	return <section { ...useBlockProps( { className: 'floe-section floe-posts' } ) }>
		<InspectorControls><PanelBody title="Post source"><SelectControl label="Mode" value={ mode || 'latest' } options={ [ { label: 'Latest posts', value: 'latest' }, { label: 'Category', value: 'category' }, { label: 'Selected post IDs', value: 'selected' } ] } onChange={ ( next ) => setAttributes( { mode: next } ) } />{ mode === 'category' ? <SelectControl label="Category" value={ String( categoryId || 0 ) } options={ [ { label: 'Choose category', value: '0' }, ...( categories || [] ).map( ( term ) => ( { label: term.name, value: String( term.id ) } ) ) ] } onChange={ ( next ) => setAttributes( { categoryId: Number( next ) } ) } /> : null }{ mode === 'selected' ? <TextControl label="Post IDs, comma separated" value={ selectedIds || '' } onChange={ ( next ) => setAttributes( { selectedIds: next } ) } /> : null }<RangeControl label="Number of posts" min={ 1 } max={ 12 } value={ count || 3 } onChange={ ( next ) => setAttributes( { count: next } ) } /></PanelBody></InspectorControls>
		<div className="floe-section__inner"><Eyebrow attributes={ attributes } setAttributes={ setAttributes } /><Heading attributes={ attributes } setAttributes={ setAttributes } /><div className="floe-posts__grid">{ posts && posts.length ? posts.map( ( post ) => {
			const overrides = attributes.mediaOverrides || {};
			const media = overrides[ post.id ] || {};
			const setMedia = ( next ) => setAttributes( { mediaOverrides: { ...overrides, [ post.id ]: { ...media, ...next } } } );
			return <article className="floe-card" key={ post.id }><MediaPicker attributes={ media } setAttributes={ setMedia } label="Featured image or choose an image / MP4" /><div className="floe-card__content"><p className="floe-card__meta">{ new Date( post.date ).toLocaleDateString() }</p><h3 className="floe-card__title">{ post.title.rendered.replace( /<[^>]*>/g, '' ) }</h3><div className="floe-card__description">{ post.excerpt.rendered.replace( /<[^>]*>/g, '' ) }</div></div></article>;
		} ) : <p>Publish a post to preview this section.</p> }</div></div>
	</section>;
}
registerBlockType( metadata.name, { edit: Edit, save: () => null } );
