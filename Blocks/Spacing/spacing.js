import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const PRESETS = {
	large: [ 120, 120, 86, 67 ],
	medium: [ 80, 80, 57, 44 ],
	small: [ 40, 40, 29, 22 ],
	none: [ 0, 0, 0, 0 ],
};
const BREAKPOINTS = [ [ 'largeDesktop', 'Large desktop · 1280px and up' ], [ 'desktop', 'Desktop · 768–1279px' ], [ 'tablet', 'Tablet · 550–767px' ], [ 'mobile', 'Mobile · under 550px' ] ];
const clamp = ( value ) => Math.max( 0, Math.min( 500, Number( value ) || 0 ) );

function Edit( { attributes, setAttributes } ) {
	const { size = 'large', custom, automatic } = attributes;
	const preset = PRESETS[ size ] || PRESETS.large;
	const legacyCustom = automatic !== undefined && attributes.desktop >= 0;
	const isCustom = custom || legacyCustom;
	const values = BREAKPOINTS.map( ( [ key ], index ) => isCustom && attributes[ key ] >= 0 ? clamp( attributes[ key ] ) : preset[ index ] );
	if ( isCustom && attributes.largeDesktop < 0 && attributes.desktop >= 0 ) values[ 0 ] = values[ 1 ];
	const style = Object.fromEntries( BREAKPOINTS.map( ( [ key ], index ) => [ '--floe-spacing-' + key.replace( /[A-Z]/g, ( letter ) => '-' + letter.toLowerCase() ), values[ index ] + 'px' ] ) );
	const blockProps = useBlockProps( { className: 'floe-spacing floe-spacing--' + size, style, 'data-floe-spacing-label': ( isCustom ? 'Custom' : size ) + ' spacing' } );
	return <>
		<InspectorControls><PanelBody title={ __( 'Spacing', 'floe' ) }>
			<SelectControl label={ __( 'Preset', 'floe' ) } value={ size } options={ [ { label: __( 'Large', 'floe' ), value: 'large' }, { label: __( 'Medium', 'floe' ), value: 'medium' }, { label: __( 'Small', 'floe' ), value: 'small' }, { label: __( 'No spacing', 'floe' ), value: 'none' } ] } onChange={ ( next ) => setAttributes( { size: next } ) } />
			<ToggleControl label={ __( 'Custom values', 'floe' ) } checked={ !! isCustom } onChange={ ( next ) => setAttributes( { custom: next, automatic: undefined } ) } />
			{ isCustom && BREAKPOINTS.map( ( [ key, label ], index ) => <TextControl key={ key } label={ label } type="number" min="0" max="500" value={ attributes[ key ] < 0 ? '' : attributes[ key ] } placeholder={ String( preset[ index ] ) } onChange={ ( next ) => setAttributes( { [ key ]: next === '' ? -1 : clamp( next ) } ) } /> ) }
		</PanelBody></InspectorControls>
		<div { ...blockProps } aria-hidden="true" />
	</>;
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
