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
		{ className: 'team__grid' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [
				[ 'floe/person' ],
				[ 'floe/person' ],
				[ 'floe/person' ],
				[ 'floe/person' ],
			],
			orientation: 'horizontal',
		}
	);

	return (
		<section { ...blockProps }>
			<div className="team__inner">
				<ul { ...innerBlocksProps } />
			</div>
		</section>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
