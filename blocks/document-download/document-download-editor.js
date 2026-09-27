import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useInnerBlocksProps } from '@wordpress/block-editor';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

function Edit( { name, context } ) {
	const blockProps = useFloeBlockProps(
		name,
		{},
		{ surface: 'base', context }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'document-download__files' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [ [ 'floe/download' ] ],
		}
	);

	return (
		<section { ...blockProps }>
			<div className="document-download__inner">
				<ul { ...innerBlocksProps } />
			</div>
		</section>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
