import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	ToggleControl,
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

function PostCard( { post, override, headingLevel, termName } ) {
	const featured = useMedia(
		override?.id ? override : { id: post.featured_media || undefined }
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
			category={ termName }
			date={ dateI18n( getSettings().formats.date, post.date ) }
			title={
				<a
					className="card__link"
					href={ post.link }
					onClick={ ( event ) => event.preventDefault() }
				>
					{ decodeEntities( post.title?.rendered || '' ) }
				</a>
			}
			text={ trimWords( plainText( post.excerpt?.rendered || '' ) ) }
		/>
	);
}

function PostPicker( { postType, selected, onChange } ) {
	const [ search, setSearch ] = useState( '' );
	const { results, chosen } = useSelect(
		( select ) => ( {
			results:
				select( coreStore ).getEntityRecords( 'postType', postType, {
					search,
					per_page: 20,
					status: 'publish',
				} ) || [],
			chosen: selected.length
				? select( coreStore ).getEntityRecords( 'postType', postType, {
						include: selected,
						per_page: 24,
					} ) || []
				: [],
		} ),
		[ postType, search, selected ]
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
					'Search by title. Up to 24, in the order listed below.',
					'floe'
				) }
				value={ null }
				options={ results
					.filter( ( post ) => ! selected.includes( post.id ) )
					.map( ( post ) => ( {
						value: post.id,
						label:
							decodeEntities( post.title?.rendered || '' ) ||
							`#${ post.id }`,
					} ) ) }
				onFilterValueChange={ setSearch }
				onChange={ ( id ) =>
					id &&
					selected.length < 24 &&
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
	const {
		source,
		postType,
		count,
		showAll,
		more,
		taxonomy,
		term,
		showFilters,
		posts,
		headingLevel,
		mediaOverrides,
	} = attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const currentId = useSelect(
		( select ) => select( 'core/editor' )?.getCurrentPostId(),
		[]
	);

	const { postTypes, taxonomies, taxonomyObject, terms } = useSelect(
		( select ) => {
			const core = select( coreStore );
			const allTaxonomies = core.getTaxonomies( { per_page: -1 } ) || [];
			return {
				postTypes: (
					core.getPostTypes( { per_page: -1 } ) || []
				).filter(
					( type ) => type.viewable && type.slug !== 'attachment'
				),
				taxonomies: allTaxonomies.filter(
					( tax ) =>
						tax.visibility?.public !== false &&
						( tax.types || [] ).includes( postType )
				),
				taxonomyObject: allTaxonomies.find(
					( tax ) => tax.slug === taxonomy
				),
				terms: taxonomy
					? core.getEntityRecords( 'taxonomy', taxonomy, {
							per_page: 100,
							hide_empty: true,
						} ) || []
					: [],
			};
		},
		[ postType, taxonomy ]
	);

	const records = useSelect(
		( select ) => {
			if ( source === 'manual' ) {
				return [];
			}
			const query = {
				per_page: showAll ? 12 : count,
				status: 'publish',
				exclude: currentId ? [ currentId ] : undefined,
			};
			if ( source === 'picker' ) {
				Object.assign( query, {
					include: posts.length ? posts : [ 0 ],
					orderby: 'include',
					per_page: Math.max( 1, posts.length ),
					exclude: undefined,
				} );
			} else if ( taxonomyObject && term && ! showFilters ) {
				query[ taxonomyObject.rest_base ] = [ term ];
			}
			return select( coreStore ).getEntityRecords(
				'postType',
				postType,
				query
			);
		},
		[
			source,
			postType,
			count,
			showAll,
			posts,
			term,
			showFilters,
			taxonomyObject,
			currentId,
		]
	);

	const termFor = ( post ) => {
		const base =
			taxonomyObject?.rest_base ||
			( postType === 'post' ? 'categories' : '' );
		const id = base ? post[ base ]?.[ 0 ] : 0;
		const found = terms.find( ( item ) => item.id === id );
		return found ? decodeEntities( found.name ) : '';
	};

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'posts__grid' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [
				[ 'floe/post-item' ],
				[ 'floe/post-item' ],
				[ 'floe/post-item' ],
			],
			orientation: 'horizontal',
		}
	);
	const topTerms = terms.filter( ( item ) => ! item.parent );

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
							{ label: __( 'Latest', 'floe' ), value: 'latest' },
							{
								label: __( 'Hand-picked', 'floe' ),
								value: 'picker',
							},
							{
								label: __( 'Manual entries', 'floe' ),
								value: 'manual',
							},
						] }
						onChange={ ( next ) =>
							setAttributes( { source: next } )
						}
					/>
					{ source !== 'manual' && (
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Post type', 'floe' ) }
							value={ postType }
							options={ postTypes.map( ( type ) => ( {
								label: type.name,
								value: type.slug,
							} ) ) }
							onChange={ ( next ) =>
								setAttributes( {
									postType: next,
									taxonomy: '',
									term: 0,
									posts: [],
								} )
							}
						/>
					) }
					{ source === 'latest' && (
						<>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __( 'Show all', 'floe' ) }
								help={ __(
									'Every published post (up to 100).',
									'floe'
								) }
								checked={ showAll }
								onChange={ ( next ) =>
									setAttributes( { showAll: next } )
								}
							/>
							{ ! showAll && (
								<>
									<RangeControl
										__next40pxDefaultSize
										__nextHasNoMarginBottom
										label={ __(
											'Number of posts',
											'floe'
										) }
										help={ __(
											'Shown at first, and added each time more load.',
											'floe'
										) }
										min={ 1 }
										max={ 24 }
										value={ count }
										onChange={ ( next ) =>
											setAttributes( { count: next } )
										}
									/>
									<SelectControl
										__next40pxDefaultSize
										__nextHasNoMarginBottom
										label={ __( 'More posts', 'floe' ) }
										value={ more }
										options={ [
											{
												label: __( 'None', 'floe' ),
												value: 'none',
											},
											{
												label: __(
													'Load more button',
													'floe'
												),
												value: 'button',
											},
											{
												label: __(
													'Load automatically on scroll',
													'floe'
												),
												value: 'scroll',
											},
										] }
										onChange={ ( next ) =>
											setAttributes( { more: next } )
										}
									/>
								</>
							) }
							<SelectControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Taxonomy', 'floe' ) }
								help={ __(
									'For filters, the label on each card, and limiting the posts.',
									'floe'
								) }
								value={ taxonomy }
								options={ [
									{ label: __( 'None', 'floe' ), value: '' },
									...taxonomies.map( ( tax ) => ( {
										label: tax.name,
										value: tax.slug,
									} ) ),
								] }
								onChange={ ( next ) =>
									setAttributes( { taxonomy: next, term: 0 } )
								}
							/>
							{ taxonomy && (
								<ToggleControl
									__nextHasNoMarginBottom
									label={ __( 'Show filters', 'floe' ) }
									help={ __(
										'Buttons for each top-level term. Clicking one updates the posts without reloading the page.',
										'floe'
									) }
									checked={ showFilters }
									onChange={ ( next ) =>
										setAttributes( { showFilters: next } )
									}
								/>
							) }
							{ taxonomy && ! showFilters && (
								<SelectControl
									__next40pxDefaultSize
									__nextHasNoMarginBottom
									label={ __( 'Only show', 'floe' ) }
									value={ String( term ) }
									options={ [
										{
											label: __( 'Everything', 'floe' ),
											value: '0',
										},
										...terms.map( ( item ) => ( {
											label: decodeEntities( item.name ),
											value: String( item.id ),
										} ) ),
									] }
									onChange={ ( next ) =>
										setAttributes( {
											term: parseInt( next, 10 ),
										} )
									}
								/>
							) }
						</>
					) }
					{ source === 'picker' && (
						<PostPicker
							postType={ postType }
							selected={ posts }
							onChange={ ( next ) =>
								setAttributes( { posts: next } )
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
				{ source !== 'manual' && !! records?.length && (
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
									{ decodeEntities(
										post.title?.rendered || ''
									) }
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
						action
						actionStyle="secondary"
						placeholders={ {
							action: __( 'Optional “View all” link', 'floe' ),
						} }
					/>
					{ source === 'latest' &&
						taxonomy &&
						showFilters &&
						!! topTerms.length && (
							<ul className="posts__filters">
								<li>
									<button
										type="button"
										className="posts__filter"
										aria-pressed="true"
									>
										{ __( 'All', 'floe' ) }
									</button>
								</li>
								{ topTerms.map( ( item ) => (
									<li key={ item.id }>
										<button
											type="button"
											className="posts__filter"
											aria-pressed="false"
										>
											{ decodeEntities( item.name ) }
										</button>
									</li>
								) ) }
							</ul>
						) }
					{ source === 'manual' && <div { ...innerBlocksProps } /> }
					{ source !== 'manual' && records === null && (
						<p>{ __( 'Loading posts…', 'floe' ) }</p>
					) }
					{ source !== 'manual' && records?.length === 0 && (
						<p className="floe-slot-hint">
							{ source === 'picker'
								? __( 'Choose posts in the sidebar.', 'floe' )
								: __(
										'Nothing published matches yet. This section is hidden on the site until there is.',
										'floe'
									) }
						</p>
					) }
					{ source !== 'manual' && !! records?.length && (
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
									termName={ termFor( post ) }
								/>
							) ) }
						</div>
					) }
					{ source === 'latest' && ! showAll && more !== 'none' && (
						<div className="posts__more">
							<span className="posts__more-button button button--secondary">
								{ more === 'scroll'
									? __(
											'Load more (automatic on scroll)',
											'floe'
										)
									: __( 'Load more', 'floe' ) }
							</span>
						</div>
					) }
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
