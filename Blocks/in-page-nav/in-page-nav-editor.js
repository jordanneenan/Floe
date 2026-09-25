import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button as WPButton, Flex, FlexBlock } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { close } from '@wordpress/icons';
import { useText, LinkButton, plain } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { items } = attributes;
	const text = useText( attributes, setAttributes );
	const auto = useSelect(
		( select ) =>
			select( blockEditorStore )
				.getBlocks()
				.filter( ( block ) => block.name.startsWith( 'floe/' ) && block.attributes.anchor )
				.map( ( block ) => ( {
					anchor: block.attributes.anchor,
					label: plain( block.attributes.eyebrow || '' ) || plain( block.attributes.heading || '' ) || block.attributes.anchor,
				} ) ),
		[]
	);
	const shown = items.length ? items : auto;
	const blockProps = useBlockProps( { className: 'in-page-nav' } );
	const update = ( index, key, value ) => setAttributes( { items: items.map( ( item, i ) => ( i === index ? { ...item, [ key ]: value } : item ) ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Links', 'floe' ) }>
					<p>
						{ items.length
							? __( 'Using the links below. Remove them all to go back to automatic links.', 'floe' )
							: __( 'Automatic: every section on this page with an HTML anchor (Block settings → Advanced) gets a link, labelled with its eyebrow or heading.', 'floe' ) }
					</p>
					{ items.map( ( item, index ) => (
						<Flex key={ index } align="flex-end" style={ { marginBottom: 8 } }>
							<FlexBlock>
								<TextControl __next40pxDefaultSize __nextHasNoMarginBottom label={ __( 'Label', 'floe' ) } value={ item.label || '' } onChange={ ( value ) => update( index, 'label', value ) } />
							</FlexBlock>
							<FlexBlock>
								<TextControl __next40pxDefaultSize __nextHasNoMarginBottom label={ __( 'Anchor', 'floe' ) } value={ item.anchor || '' } onChange={ ( value ) => update( index, 'anchor', value.replace( /^#/, '' ) ) } />
							</FlexBlock>
							<WPButton icon={ close } label={ __( 'Remove', 'floe' ) } onClick={ () => setAttributes( { items: items.filter( ( _, i ) => i !== index ) } ) } />
						</Flex>
					) ) }
					<WPButton variant="secondary" onClick={ () => setAttributes( { items: [ ...( items.length ? items : auto ), { label: '', anchor: '' } ] } ) }>
						{ items.length ? __( 'Add link', 'floe' ) : __( 'Set links by hand', 'floe' ) }
					</WPButton>
				</PanelBody>
			</InspectorControls>
			<nav { ...blockProps }>
				<div className="in-page-nav__inner">
					<div className="in-page-nav__links">
						{ text( 'label', __( 'On this page', 'floe' ), { allowedFormats: [] } )( { tagName: 'p', className: 'in-page-nav__label' } ) }
						<ul className="in-page-nav__list">
							{ shown.length ? (
								shown.map( ( item, index ) => (
									<li key={ item.anchor + index }>
										<span className={ `in-page-nav__link${ index === 0 ? ' is-active' : '' }` }>{ item.label || item.anchor }</span>
									</li>
								) )
							) : (
								<li className="floe-slot-hint">{ __( 'Give sections an HTML anchor to list them here.', 'floe' ) }</li>
							) }
						</ul>
					</div>
					<div className="in-page-nav__action">
						<LinkButton value={ attributes.action } onChange={ ( action ) => setAttributes( { action } ) } arrow={ false } placeholder={ __( 'Optional action', 'floe' ) } />
					</div>
				</div>
			</nav>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
