import { registerBlockType } from '@wordpress/blocks';
import {
	BlockControls,
	useBlockProps,
	LinkControl,
} from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton, Popover } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { link as linkIcon } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { useText, MediaSlot } from '@floe/editor';
import { Card } from '@floe/components/card';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context } ) {
	const [ anchor, setAnchor ] = useState();
	const [ isOpen, setOpen ] = useState( false );
	const text = useText( attributes, setAttributes );
	const link = attributes.link || {};
	const level = Math.min(
		4,
		( context[ 'floe/postsHeadingLevel' ] || 2 ) + 1
	);
	const blockProps = useBlockProps( {
		className: 'post-item',
		ref: setAnchor,
	} );
	const small = ( key, placeholder ) =>
		text( key, placeholder, { allowedFormats: [] } )( { tagName: 'span' } );

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
									url: next.url || '',
									newTab: !! next.opensInNewTab,
								},
							} )
						}
						onRemove={ () => setAttributes( { link: {} } ) }
					/>
				</Popover>
			) }
			<Card
				variant="post"
				linked={ !! link.url }
				headingLevel={ level }
				media={
					<MediaSlot
						value={ attributes.media }
						onChange={ ( media ) => setAttributes( { media } ) }
						ratio="4/3"
						className="card__media"
					/>
				}
				category={ small( 'label', __( 'Label', 'floe' ) ) }
				date={ small( 'date', __( 'Date or detail', 'floe' ) ) }
				title={ text( 'title', __( 'Card title', 'floe' ) ) }
				text={ text( 'text', __( 'A sentence or two', 'floe' ), {
					disableLineBreaks: false,
				} ) }
			/>
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
