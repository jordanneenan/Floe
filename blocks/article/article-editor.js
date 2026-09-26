import { registerBlockType, registerBlockStyle } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, LinkButton, SurfaceControl } from '@floe/editor';
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
	const { surface } = attributes;
	const blockProps = useFloeBlockProps( name, {}, { surface } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'article__content' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl
						value={ surface }
						onChange={ ( next ) =>
							setAttributes( { surface: next } )
						}
						options={ [ 'base', 'subtle' ] }
					/>
				</PanelBody>
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
