import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	LinkButton,
	useIsEditing,
	hasText,
} from '@floe/editor';
import { Eyebrow } from '@floe/components/eyebrow';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	const blockProps = useFloeBlockProps( name );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Light up as you scroll', 'floe' ) }
						help={ __(
							'On the live site the words start faint and light up in reading order as visitors scroll.',
							'floe'
						) }
						checked={ attributes.lightUp }
						onChange={ ( lightUp ) => setAttributes( { lightUp } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="statement__inner">
					{ ( editing || hasText( attributes.eyebrow ) ) && (
						<Eyebrow>
							{ text( 'eyebrow', __( 'Eyebrow', 'floe' ), {
								allowedFormats: [],
							} )( { tagName: 'span' } ) }
						</Eyebrow>
					) }
					{ text(
						'statement',
						__(
							'Your statement. Make words bold to show them in the accent colour.',
							'floe'
						),
						{ allowedFormats: [ 'core/bold', 'core/italic' ] }
					)( { tagName: 'p', className: 'statement__text' } ) }
					<LinkButton
						value={ attributes.action }
						onChange={ ( action ) => setAttributes( { action } ) }
						style="secondary"
						placeholder={ __( 'Optional button', 'floe' ) }
					/>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
