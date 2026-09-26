import { registerBlockType, store as blocksStore } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	EditableSectionHeader,
	HeadingLevelControl,
	LinkButton,
	useText,
} from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId, name } ) {
	const text = useText( attributes, setAttributes );
	const allowedBlocks = useSelect(
		( select ) =>
			select( blocksStore )
				.getBlockTypes()
				.map( ( type ) => type.name )
				.filter( ( type ) => ! type.startsWith( 'floe/' ) ),
		[]
	);
	const hasForm = useSelect(
		( select ) => select( blockEditorStore ).getBlockCount( clientId ) > 0,
		[ clientId ]
	);
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'newsletter__form form-slot' },
		{
			allowedBlocks,
			renderAppender: InnerBlocks.ButtonBlockAppender,
			placeholder: (
				<p className="floe-slot-hint">
					{ __(
						'Form slot: add your mailing provider’s signup block or shortcode. Leave empty and add a button instead to use this as a slim call to action.',
						'floe'
					) }
				</p>
			),
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<HeadingLevelControl
						value={ attributes.headingLevel }
						onChange={ ( headingLevel ) =>
							setAttributes( { headingLevel } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="newsletter__inner">
					<div className="newsletter__panel surface-tint">
						<EditableSectionHeader
							attributes={ attributes }
							setAttributes={ setAttributes }
							layout="stacked"
						/>
						<div className="newsletter__side">
							<div { ...innerBlocksProps } />
							{ ! hasForm && (
								<LinkButton
									value={ attributes.action }
									onChange={ ( action ) =>
										setAttributes( { action } )
									}
									placeholder={ __(
										'Button (used when there’s no form)',
										'floe'
									) }
								/>
							) }
							{ text(
								'note',
								__( 'Optional privacy note', 'floe' )
							)( {
								tagName: 'p',
								className: 'newsletter__note',
							} ) }
						</div>
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
