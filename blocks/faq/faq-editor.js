import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

const item = ( summary ) => [
	'core/details',
	{ summary },
	[ [ 'core/paragraph', { placeholder: __( 'Answer', 'floe' ) } ] ],
];
const TEMPLATE = [ item( '' ), item( '' ), item( '' ) ];

function Edit( { attributes, setAttributes, name, context } ) {
	const { oneOpen, schema } = attributes;
	const blockProps = useFloeBlockProps(
		name,
		{},
		{ surface: 'base', context }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'faq__items' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Only one answer open at a time', 'floe' ) }
						checked={ oneOpen }
						onChange={ ( next ) =>
							setAttributes( { oneOpen: next } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Add FAQ structured data', 'floe' ) }
						help={ __(
							'Helps search engines show these questions. Use once per page, for genuine questions and answers.',
							'floe'
						) }
						checked={ schema }
						onChange={ ( next ) =>
							setAttributes( { schema: next } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="faq__inner">
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
