import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const PRESETS = {
	large: [ 120, 120, 86, 67 ],
	medium: [ 80, 80, 57, 44 ],
	small: [ 40, 40, 29, 22 ],
	none: [ 0, 0, 0, 0 ],
};

const BREAKPOINTS = [
	[ 'large', __( 'Large desktop (1280px and up)', 'floe' ) ],
	[ 'desktop', __( 'Desktop (768–1279px)', 'floe' ) ],
	[ 'tablet', __( 'Tablet (550–767px)', 'floe' ) ],
	[ 'mobile', __( 'Mobile (under 550px)', 'floe' ) ],
];

const clamp = ( value ) => Math.max( 0, Math.min( 500, parseInt( value, 10 ) || 0 ) );

function Edit( { attributes, setAttributes } ) {
	const { size, custom } = attributes;
	const preset = PRESETS[ size ] || PRESETS.large;
	const values = BREAKPOINTS.map( ( [ key ], index ) =>
		custom && Number.isFinite( attributes[ key ] ) ? attributes[ key ] : preset[ index ]
	);
	const style = Object.fromEntries( BREAKPOINTS.map( ( [ key ], index ) => [ `--spacing-${ key }`, `${ values[ index ] }px` ] ) );
	const label = custom ? __( 'Custom spacing', 'floe' ) : `${ size } ${ __( 'spacing', 'floe' ) }`;
	const blockProps = useBlockProps( { className: 'spacing', style, 'data-label': label } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Spacing', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Size', 'floe' ) }
						value={ size }
						options={ [
							{ label: __( 'Large', 'floe' ), value: 'large' },
							{ label: __( 'Medium', 'floe' ), value: 'medium' },
							{ label: __( 'Small', 'floe' ), value: 'small' },
							{ label: __( 'No space', 'floe' ), value: 'none' },
						] }
						onChange={ ( next ) => setAttributes( { size: next } ) }
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Set exact values per screen size', 'floe' ) }
						checked={ custom }
						onChange={ ( next ) => setAttributes( { custom: next } ) }
					/>
					{ custom &&
						BREAKPOINTS.map( ( [ key, text ], index ) => (
							<NumberControl
								__next40pxDefaultSize
								key={ key }
								label={ text }
								min={ 0 }
								max={ 500 }
								suffix="px"
								value={ Number.isFinite( attributes[ key ] ) ? attributes[ key ] : '' }
								placeholder={ String( preset[ index ] ) }
								onChange={ ( next ) => setAttributes( { [ key ]: next === '' || next === undefined ? undefined : clamp( next ) } ) }
							/>
						) ) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } aria-hidden="true" />
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
