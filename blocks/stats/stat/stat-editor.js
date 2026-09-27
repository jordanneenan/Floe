import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useText, useIsEditing, hasText } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const text = useText( attributes, setAttributes );
	const editing = useIsEditing();
	const blockProps = useBlockProps( { className: 'stat' } );
	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Stat', 'floe' ) }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Prefix', 'floe' ) }
						help={ __(
							'Shown before the number at the same size, e.g. £, $ or ~. Count up only animates the number.',
							'floe'
						) }
						value={ attributes.prefix }
						onChange={ ( prefix ) => setAttributes( { prefix } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<p className="stat__figure">
				{ attributes.prefix && (
					<span className="stat__prefix">{ attributes.prefix }</span>
				) }
				{ text( 'value', '40', { allowedFormats: [] } )( {
					tagName: 'span',
					className: 'stat__value',
				} ) }
				{ ( editing || hasText( attributes.unit ) ) &&
					text( 'unit', __( 'unit', 'floe' ), {
						allowedFormats: [],
					} )( { tagName: 'span', className: 'stat__unit' } ) }
			</p>
			{ text(
				'label',
				__( 'What the number means', 'floe' )
			)( { tagName: 'p', className: 'stat__label' } ) }
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
