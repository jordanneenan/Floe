import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

function Edit( { clientId, name } ) {
	const count = useSelect(
		( select ) => select( blockEditorStore ).getBlockCount( clientId ),
		[ clientId ]
	);
	const blockProps = useFloeBlockProps(
		name,
		{ className: `cta--count-${ Math.min( 3, Math.max( 1, count ) ) }` },
		{ surface: 'base' }
	);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'cta__inner' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [ [ 'floe/cta-panel' ] ],
			orientation: 'horizontal',
			renderAppender: count < 3 ? InnerBlocks.ButtonBlockAppender : false,
		}
	);

	return (
		<section { ...blockProps }>
			<div { ...innerBlocksProps } />
		</section>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
