import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
	MediaSlot,
	useText,
	useFormSlotBlocks,
	useIsEditing,
	hasText,
} from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	const show = ( key ) => editing || hasText( attributes[ key ] );
	const allowedBlocks = useFormSlotBlocks( name );
	const blockProps = useFloeBlockProps( name, { className: 'has-form' } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'contact__form form-slot' },
		{
			allowedBlocks,
			renderAppender: InnerBlocks.ButtonBlockAppender,
			placeholder: (
				<p className="floe-slot-hint">
					{ __(
						'Form slot: add Floe’s Form block, or a form plugin’s block or shortcode. It picks up Floe’s form styling.',
						'floe'
					) }
				</p>
			),
		}
	);
	const row = ( label, key, placeholder, options ) =>
		show( key ) && (
			<div className="contact__row">
				<dt className="contact__label">{ label }</dt>
				<dd className="contact__value">
					{ text( key, placeholder, options )( { tagName: 'span' } ) }
				</dd>
			</div>
		);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<HeadingLevelControl
						value={ attributes.headingLevel }
						onChange={ ( headingLevel ) =>
							setAttributes( { headingLevel } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="contact__inner">
					<div className="contact__details">
						<EditableSectionHeader
							attributes={ attributes }
							setAttributes={ setAttributes }
							layout="stacked"
						/>
						{ [ 'email', 'phone', 'address', 'hours' ].some(
							show
						) && (
							<dl className="contact__list">
								{ row(
									__( 'Email', 'floe' ),
									'email',
									'hello@example.com',
									{ allowedFormats: [] }
								) }
								{ row(
									__( 'Phone', 'floe' ),
									'phone',
									'+61 7 3000 0000',
									{ allowedFormats: [] }
								) }
								{ show( 'address' ) && (
									<div className="contact__row">
										<dt className="contact__label">
											{ text(
												'addressLabel',
												__( 'Address', 'floe' ),
												{ allowedFormats: [] }
											)( { tagName: 'span' } ) }
										</dt>
										<dd className="contact__value">
											{ text(
												'address',
												__( 'Street address', 'floe' ),
												{ disableLineBreaks: false }
											)( { tagName: 'span' } ) }
										</dd>
									</div>
								) }
								{ row(
									__( 'Hours', 'floe' ),
									'hours',
									__( 'Opening hours', 'floe' ),
									{ disableLineBreaks: false }
								) }
							</dl>
						) }
						{ ( editing || attributes.map?.id ) && (
							<MediaSlot
								value={ attributes.map }
								onChange={ ( map ) => setAttributes( { map } ) }
								ratio="23/10"
								allowVideo={ false }
								label={ __( 'Optional map or image', 'floe' ) }
								className="contact__map"
							/>
						) }
					</div>
					<div { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
