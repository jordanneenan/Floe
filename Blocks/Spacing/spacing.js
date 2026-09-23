import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const DEFAULTS = {
	large: [ 120, 86, 67 ],
	medium: [ 80, 57, 44 ],
	small: [ 40, 29, 22 ],
	none: [ 0, 0, 0 ],
};

function numberFromInput( value ) {
	if ( value === '' ) {
		return -1;
	}
	const number = Number( value );
	return Number.isFinite( number ) ? Math.max( 0, Math.min( 500, number ) ) : -1;
}

function Edit( { attributes, setAttributes } ) {
	const { size, desktop, tablet, mobile, automatic } = attributes;
	const defaults = DEFAULTS[ size ] || DEFAULTS.large;
	const desktopValue = desktop >= 0 ? desktop : defaults[ 0 ];
	const tabletValue = desktop >= 0 && ( automatic || tablet < 0 ) ? Math.round( desktopValue / 1.4 ) : ( tablet >= 0 && ! automatic ? tablet : defaults[ 1 ] );
	const mobileValue = desktop >= 0 && ( automatic || mobile < 0 ) ? Math.round( desktopValue / 1.8 ) : ( mobile >= 0 && ! automatic ? mobile : defaults[ 2 ] );
	const blockProps = useBlockProps( {
		className: `floe-spacing floe-spacing--${ size }`,
		style: {
			'--floe-spacing-desktop': `${ desktopValue }px`,
			'--floe-spacing-tablet': `${ tabletValue }px`,
			'--floe-spacing-mobile': `${ mobileValue }px`,
		},
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Spacing settings', 'floe' ) }>
					<SelectControl
						label={ __( 'Size', 'floe' ) }
						value={ size }
						options={ [
							{ label: __( 'Large', 'floe' ), value: 'large' },
							{ label: __( 'Medium', 'floe' ), value: 'medium' },
							{ label: __( 'Small', 'floe' ), value: 'small' },
							{ label: __( 'No spacing', 'floe' ), value: 'none' },
						] }
						onChange={ ( value ) => setAttributes( { size: value } ) }
					/>
					<TextControl
						label={ __( 'Desktop override (px)', 'floe' ) }
						type="number"
						min="0"
						max="500"
						value={ desktop < 0 ? '' : desktop }
						onChange={ ( value ) => setAttributes( { desktop: numberFromInput( value ) } ) }
					/>
					<ToggleControl
						label={ __( 'Calculate responsive values automatically', 'floe' ) }
						checked={ automatic }
						onChange={ ( value ) => setAttributes( { automatic: value } ) }
					/>
					{ ! automatic && (
						<>
							<TextControl
								label={ __( 'Tablet override (px)', 'floe' ) }
								type="number"
								min="0"
								max="500"
								value={ tablet < 0 ? '' : tablet }
								onChange={ ( value ) => setAttributes( { tablet: numberFromInput( value ) } ) }
							/>
							<TextControl
								label={ __( 'Mobile override (px)', 'floe' ) }
								type="number"
								min="0"
								max="500"
								value={ mobile < 0 ? '' : mobile }
								onChange={ ( value ) => setAttributes( { mobile: numberFromInput( value ) } ) }
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } aria-hidden="true" />
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
