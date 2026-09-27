import { registerBlockType, registerBlockStyle } from '@wordpress/blocks';
import { InnerBlocks, useInnerBlocksProps } from '@wordpress/block-editor';
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
	const blockProps = useFloeBlockProps( name );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'article__content' },
		{ allowedBlocks: metadata.allowedBlocks, template: TEMPLATE }
	);

	return (
		<>
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
