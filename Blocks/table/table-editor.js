import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
} from '@floe/editor';
import metadata from './block.json';

const TEMPLATE = [ [ 'core/table', { hasFixedLayout: false } ] ];

function Edit( { attributes, setAttributes, name } ) {
	const { highlight, badge, emphasiseLastRow, headingLevel } = attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const frame = {
		className: `table__frame${ emphasiseLastRow ? ' table--emphasise-last' : '' }`,
		'data-highlight': highlight || undefined,
		style:
			highlight && badge
				? { '--badge': `"${ badge.replace( /["\\]/g, '' ) }"` }
				: undefined,
	};
	const innerBlocksProps = useInnerBlocksProps( frame, {
		allowedBlocks: metadata.allowedBlocks,
		template: TEMPLATE,
		templateLock: 'insert',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Table', 'floe' ) }>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Highlighted column', 'floe' ) }
						help={ __(
							'0 for none. 1 is the first column after the row labels.',
							'floe'
						) }
						min={ 0 }
						max={ 6 }
						value={ highlight }
						onChange={ ( next ) =>
							setAttributes( { highlight: next } )
						}
					/>
					{ highlight > 0 && (
						<TextControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __(
								'Badge on the highlighted column',
								'floe'
							) }
							placeholder={ __( 'e.g. Popular', 'floe' ) }
							value={ badge }
							onChange={ ( next ) =>
								setAttributes( { badge: next } )
							}
						/>
					) }
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Emphasise the last row', 'floe' ) }
						help={ __( 'For a totals or price row.', 'floe' ) }
						checked={ emphasiseLastRow }
						onChange={ ( next ) =>
							setAttributes( { emphasiseLastRow: next } )
						}
					/>
					<p>
						{ __(
							'Type ✓ in a cell for “included” and — for “not included”. They show as icons on the page.',
							'floe'
						) }
					</p>
					<HeadingLevelControl
						value={ headingLevel }
						onChange={ ( next ) =>
							setAttributes( { headingLevel: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="table__inner">
					<EditableSectionHeader
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
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
