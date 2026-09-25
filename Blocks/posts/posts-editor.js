import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	ComboboxControl,
	Button as WPButton,
	Flex,
	FlexItem,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { store as coreStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import { dateI18n, getSettings } from '@wordpress/date';
import { __ } from '@wordpress/i18n';
import { chevronUp, chevronDown, close } from '@wordpress/icons';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
	MediaSlot,
	useMedia,
} from '@floe/editor';
import { Card } from '@floe/components/card';
import { Media } from '@floe/components/media';
import metadata from './block.json';

const plainText = ( html = '' ) =>
	decodeEntities( html.replace( /<[^>]+>/g, '' ) ).trim();
const trimWords = ( text, words = 22 ) => {
	const parts = text.split( /\s+/ );
	return parts.length > words
		? `${ parts.slice( 0, words ).join( ' ' ) }…`
		: text;
};

function PostCard( { post, override, headingLevel, categories } ) {
	const featured = useMedia(
		override?.id ? override : { id: post.featured_media || undefined }
	);
	const category = categories?.find(
		( term ) => term.id === post.categories?.[ 0 ]
	);
	return (
		<Card
			variant="post"
			linked
			headingLevel={ headingLevel }
			media={
				featured.url ? (
					<Media
						url={ featured.url }
						type={ featured.type }
						alt={ featured.alt }
						ratio="4/3"
						className="card__media"
					/>
				) : null
			}
			category={ category ? decodeEntities( category.name ) : '' }
			date={ dateI18n( getSettings().formats.date, post.date ) }
			title={
				<a
					className="card__link"
					href={ post.link }
					onClick={ ( event ) => event.preventDefault() }
				>
					{ decodeEntities( post.title.rendered ) }
				</a>
			}
			text={ trimWords( plainText( post.excerpt.rendered ) ) }
		/>
	);
}

function PostPicker( { selected, onChange } ) {
	const [ search, setSearch ] = useState( '' );
	const { results, chosen } = useSelect(
		( select ) => ( {
			results:
				select( coreStore ).getEntityRecords( 'postType', 'post', {
					search,
					per_page: 20,
					status: 'publish',
					_fields: 'id,title',
				} ) || [],
			chosen: selected.length
				? select( coreStore ).getEntityRecords( 'postType', 'post', {
						include: selected,
						per_page: 12,
						_fields: 'id,title',
					} ) || []
				: [],
		} ),
		[ search, selected ]
	);
	const titleOf = ( id ) =>
		decodeEntities(
			chosen.find( ( post ) => post.id === id )?.title?.rendered ||
				`#${ id }`
		);
	const move = ( index, by ) => {
		const next = [ ...selected ];
		const [ item ] = next.splice( index, 1 );
		next.splice( index + by, 0, item );
		onChange( next );
	};

	return (
		<>
			<ComboboxControl
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				label={ __( 'Add a post', 'floe' ) }
				help={ __(
					'Search by title. Up to 12 posts, in the order listed below.',
					'floe'
				) }
				value={ null }
				options={ results
					.filter( ( post ) => ! selected.includes( post.id ) )
					.map( ( post ) => ( {
						value: post.id,
						label:
							decodeEntities( post.title.rendered ) ||
							`#${ post.id }`,
					} ) ) }
				onFilterValueChange={ setSearch }
				onChange={ ( id ) =>
					id &&
					selected.length < 12 &&
					onChange( [ ...selected, Number( id ) ] )
				}
			/>
			<ul className="floe-post-picker">
				{ selected.map( ( id, index ) => (
					<li key={ id }>
						<Flex>
							<FlexItem isBlock>{ titleOf( id ) }</FlexItem>
							<WPButton
								size="small"
								icon={ chevronUp }
								label={ __( 'Move up', 'floe' ) }
								disabled={ index === 0 }
								onClick={ () => move( index, -1 ) }
							/>
							<WPButton
								size="small"
								icon={ chevronDown }
								label={ __( 'Move down', 'floe' ) }
								disabled={ index === selected.length - 1 }
								onClick={ () => move( index, 1 ) }
							/>
							<WPButton
								size="small"
								icon={ close }
								label={ __( 'Remove', 'floe' ) }
								onClick={ () =>
									onChange(
										selected.filter(
											( item ) => item !== id
										)
									)
								}
							/>
						</Flex>
					</li>
				) ) }
			</ul>
		</>
	);
}

function Edit( { attributes, setAttributes, name } ) {
	const { source, category, posts, count, headingLevel, mediaOverrides } =
		attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const currentId = useSelect(
		( select ) => select( 'core/editor' )?.getCurrentPostId(),
		[]
	);

	const { records, categories } = useSelect(
		( select ) => {
			const query = {
				per_page: count,
				status: 'publish',
				_fields: 'id,title,excerpt,date,link,featured_media,categories',
				exclude: currentId ? [ currentId ] : undefined,
			};
			if ( source === 'manual' ) {
				Object.assign( query, {
					include: posts.length ? posts : [ 0 ],
					orderby: 'include',
					per_page: Math.max( 1, posts.length ),
					exclude: undefined,
				} );
			} else if ( source === 'category' && category ) {
				query.categories = [ category ];
			}
			return {
				records: select( coreStore ).getEntityRecords(
					'postType',
					'post',
					query
				),
				categories: select( coreStore ).getEntityRecords(
					'taxonomy',
					'category',
					{ per_page: 100, _fields: 'id,name' }
				),
			};
		},
		[ source, category, posts, count, currentId ]
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Posts', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Show', 'floe' ) }
						value={ source }
						options={ [
							{
								label: __( 'Latest posts', 'floe' ),
								value: 'latest',
							},
							{
								label: __( 'Posts from a category', 'floe' ),
								value: 'category',
							},
							{
								label: __( 'Hand-picked posts', 'floe' ),
								value: 'manual',
							},
						] }
						onChange={ ( next ) =>
							setAttributes( { source: next } )
						}
					/>
					{ source === 'category' && (
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Category', 'floe' ) }
							value={ String( category ) }
							options={ [
								{
									label: __( 'Choose a category', 'floe' ),
									value: '0',
								},
								...( categories || [] ).map( ( term ) => ( {
									label: decodeEntities( term.name ),
									value: String( term.id ),
								} ) ),
							] }
							onChange={ ( next ) =>
								setAttributes( {
									category: parseInt( next, 10 ),
								} )
							}
						/>
					) }
					{ source === 'manual' ? (
						<PostPicker
							selected={ posts }
							onChange={ ( next ) =>
								setAttributes( { posts: next } )
							}
						/>
					) : (
						<RangeControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Number of posts', 'floe' ) }
							min={ 1 }
							max={ 12 }
							value={ count }
							onChange={ ( next ) =>
								setAttributes( { count: next } )
							}
						/>
					) }
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
				</PanelBody>
				{ !! records?.length && (
					<PanelBody
						title={ __( 'Card images', 'floe' ) }
						initialOpen={ false }
					>
						<p>
							{ __(
								'Each card uses the post’s featured image. Choose a different image for this section only:',
								'floe'
							) }
						</p>
						{ records.map( ( post ) => (
							<div key={ post.id } style={ { marginBottom: 16 } }>
								<strong>
									{ decodeEntities( post.title.rendered ) }
								</strong>
								<MediaSlot
									value={ mediaOverrides[ post.id ] }
									onChange={ ( media ) => {
										const next = { ...mediaOverrides };
										if ( media?.id ) {
											next[ post.id ] = media;
										} else {
											delete next[ post.id ];
										}
										setAttributes( {
											mediaOverrides: next,
										} );
									} }
									ratio="4/3"
									allowVideo
									label={ __(
										'Card image override',
										'floe'
									) }
								/>
							</div>
						) ) }
					</PanelBody>
				) }
			</InspectorControls>
			<section { ...blockProps }>
				<div className="posts__inner">
					<EditableSectionHeader
						attributes={ attributes }
						setAttributes={ setAttributes }
						intro={ false }
						action
						actionStyle="secondary"
						placeholders={ {
							action: __( 'Optional “View all” link', 'floe' ),
						} }
					/>
					{ records === null && (
						<p>{ __( 'Loading posts…', 'floe' ) }</p>
					) }
					{ records?.length === 0 && (
						<p className="floe-slot-hint">
							{ source === 'manual'
								? __( 'Choose posts in the sidebar.', 'floe' )
								: __(
										'No published posts match. This section is hidden on the site until there are some.',
										'floe'
									) }
						</p>
					) }
					{ !! records?.length && (
						<div className="posts__grid">
							{ records.map( ( post ) => (
								<PostCard
									key={ post.id }
									post={ post }
									override={ mediaOverrides[ post.id ] }
									headingLevel={ Math.min(
										4,
										headingLevel + 1
									) }
									categories={ categories }
								/>
							) ) }
						</div>
					) }
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
