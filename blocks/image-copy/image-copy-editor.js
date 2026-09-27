import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, BlockControls } from '@wordpress/block-editor';
import {
	CheckboxControl,
	PanelBody,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import { pullLeft, pullRight } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	LinkButton,
	MediaSlot,
	HeadingLevelControl,
	useIsEditing,
	hasText,
	hasLink,
} from '@floe/editor';
import { Eyebrow } from '@floe/components/eyebrow';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const { mediaPosition, headingLevel } = attributes;
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	const blockProps = useFloeBlockProps( name, {
		className: `image-copy--media-${ mediaPosition } has-media`,
	} );

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ pullLeft }
						label={ __( 'Media on the left', 'floe' ) }
						isPressed={ mediaPosition === 'left' }
						onClick={ () =>
							setAttributes( { mediaPosition: 'left' } )
						}
					/>
					<ToolbarButton
						icon={ pullRight }
						label={ __( 'Media on the right', 'floe' ) }
						isPressed={ mediaPosition === 'right' }
						onClick={ () =>
							setAttributes( { mediaPosition: 'right' } )
						}
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<CheckboxControl
						__nextHasNoMarginBottom
						label={ __( 'Switch side', 'floe' ) }
						help={ __(
							'Puts the image on the right and the text on the left.',
							'floe'
						) }
						checked={ mediaPosition === 'right' }
						onChange={ ( checked ) =>
							setAttributes( {
								mediaPosition: checked ? 'right' : 'left',
							} )
						}
					/>
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="image-copy__inner">
					<MediaSlot
						value={ attributes.media }
						onChange={ ( media ) => setAttributes( { media } ) }
						ratio="624/560"
						className="image-copy__media"
					/>
					<div className="image-copy__copy">
						{ ( editing || hasText( attributes.eyebrow ) ) && (
							<Eyebrow>
								{ text( 'eyebrow', __( 'Eyebrow', 'floe' ), {
									allowedFormats: [],
								} )( { tagName: 'span' } ) }
							</Eyebrow>
						) }
						{ text(
							'heading',
							__( 'Heading', 'floe' )
						)( {
							tagName: `h${ headingLevel }`,
							className: 'image-copy__heading',
						} ) }
						{ text( 'body', __( 'Body copy', 'floe' ), {
							disableLineBreaks: false,
						} )( { tagName: 'p', className: 'image-copy__body' } ) }
						{ ( editing || hasLink( attributes.action ) ) && (
							<div className="image-copy__action">
								<LinkButton
									value={ attributes.action }
									onChange={ ( action ) =>
										setAttributes( { action } )
									}
									placeholder={ __(
										'Optional action',
										'floe'
									) }
								/>
							</div>
						) }
					</div>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
