import { registerBlockType } from '@wordpress/blocks';
import {
	BlockControls,
	useBlockProps,
	LinkControl,
} from '@wordpress/block-editor';
import {
	ToolbarGroup,
	ToolbarButton,
	Popover,
	TextControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { link as linkIcon } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { useText, MediaSlot } from '@floe/editor';
import { Card } from '@floe/components/card';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context } ) {
	const style = context[ 'floe/cardStyle' ] === 'media' ? 'media' : 'feature';
	const level = Math.min(
		4,
		( context[ 'floe/cardsHeadingLevel' ] || 2 ) + 1
	);
	const [ anchor, setAnchor ] = useState();
	const [ isOpen, setOpen ] = useState( false );
	const text = useText( attributes, setAttributes );
	const link = attributes.link || {};
	const blockProps = useBlockProps( {
		className: 'card-item',
		ref: setAnchor,
	} );

	return (
		<div { ...blockProps }>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ linkIcon }
						label={ __( 'Card link', 'floe' ) }
						isPressed={ !! link.url }
						onClick={ () => setOpen( ! isOpen ) }
					/>
				</ToolbarGroup>
			</BlockControls>
			{ isOpen && (
				<Popover
					anchor={ anchor }
					placement="bottom"
					onClose={ () => setOpen( false ) }
				>
					<LinkControl
						value={ {
							url: link.url || '',
							opensInNewTab: !! link.newTab,
						} }
						onChange={ ( next ) =>
							setAttributes( {
								link: {
									...link,
									url: next.url || '',
									newTab: !! next.opensInNewTab,
								},
							} )
						}
						onRemove={ () =>
							setAttributes( { link: { ...link, url: '' } } )
						}
					/>
					{ style === 'feature' && (
						<div style={ { padding: '0 16px 16px' } }>
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Link text', 'floe' ) }
								value={ link.label || '' }
								placeholder={ __( 'Learn more', 'floe' ) }
								onChange={ ( label ) =>
									setAttributes( {
										link: { ...link, label },
									} )
								}
							/>
						</div>
					) }
				</Popover>
			) }
			<Card
				variant={ style }
				number="auto"
				headingLevel={ level }
				linked={ !! link.url }
				linkLabel={
					link.url ? link.label || __( 'Learn more', 'floe' ) : ''
				}
				media={
					<MediaSlot
						value={ attributes.media }
						onChange={ ( media ) => setAttributes( { media } ) }
						ratio="4/3"
						className="card__media"
						placeholder
					/>
				}
				title={ text( 'title', __( 'Card title', 'floe' ) ) }
				text={ text( 'text', __( 'A sentence or two', 'floe' ), {
					disableLineBreaks: false,
				} ) }
			/>
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
