import { registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import metadata from './block.json';

function Edit( { clientId, name, context } ) {
	const count = useSelect(
		( select ) => select( blockEditorStore ).getBlockCount( clientId ),
		[ clientId ]
	);
	const blockProps = useFloeBlockProps( name, {}, { context } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'steps__list' },
		{
			allowedBlocks: metadata.allowedBlocks,
			template: [
				[ 'floe/step' ],
				[ 'floe/step' ],
				[ 'floe/step' ],
				[ 'floe/step' ],
			],
			orientation: 'horizontal',
			renderAppender: count < 5 ? InnerBlocks.ButtonBlockAppender : false,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<p>
						{ __(
							'Three to five steps. They are numbered automatically.',
							'floe'
						) }
					</p>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="steps__inner">
					<ol { ...innerBlocksProps } />
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
