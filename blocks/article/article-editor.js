import { registerBlockType, registerBlockStyle } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { RangeControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, LinkButton } from '@floe/editor';
import metadata from './block.json';

// "Lead" paragraph style (Body L, ink) for the opening paragraph.
registerBlockStyle( 'core/paragraph', {
	name: 'lead',
	label: __( 'Lead', 'floe' ),
} );

const TEMPLATE = [
	[ 'core/heading', { level: 2, placeholder: __( 'Heading', 'floe' ) } ],
	[
		'core/paragraph',
		{
			className: 'is-style-lead',
			placeholder: __( 'Start writing…', 'floe' ),
		},
	],
];

function Edit( { attributes, setAttributes, name } ) {
	const { fullWidth, width } = attributes;
	const blockProps = useFloeBlockProps( name, {
		className: fullWidth ? 'article--full' : undefined,
		style:
			! fullWidth && width > 0
				? { '--article-width': `${ width }px` }
				: undefined,
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'article__content' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls group="advanced">
				<ToggleControl
					__nextHasNoMarginBottom
					label={ __( 'Full width', 'floe' ) }
					help={ __(
						'Use the whole content width instead of a reading column, for wide images, embeds or shortcodes.',
						'floe'
					) }
					checked={ fullWidth }
					onChange={ ( next ) =>
						setAttributes( { fullWidth: next } )
					}
				/>
				{ ! fullWidth && (
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Maximum width (px)', 'floe' ) }
						help={ __(
							'The reading column is 860px unless you change it here.',
							'floe'
						) }
						min={ 320 }
						max={ 1248 }
						step={ 10 }
						initialPosition={ 860 }
						allowReset
						resetFallbackValue={ 0 }
						value={ width || undefined }
						onChange={ ( next ) =>
							setAttributes( { width: next || 0 } )
						}
					/>
				) }
			</InspectorControls>
			<section { ...blockProps }>
				<div className="article__inner">
					<div { ...innerBlocksProps } />
					<div className="article__action">
						<LinkButton
							value={ attributes.action }
							onChange={ ( action ) =>
								setAttributes( { action } )
							}
							placeholder={ __( 'Optional button', 'floe' ) }
						/>
					</div>
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
