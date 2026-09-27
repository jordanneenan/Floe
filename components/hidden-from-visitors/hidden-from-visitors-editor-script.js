/*
 * Hidden from visitors, in the editor: a "Hide from visitors" switch in every
 * Floe block's Advanced settings, and the same outline and tag on hidden
 * blocks as editors see on the site. The server does the hiding
 * (hidden-from-visitors.php).
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const isFloe = ( name ) => name.startsWith( 'floe/' );

addFilter(
	'blocks.registerBlockType',
	'floe/hidden-from-visitors/attribute',
	( settings, name ) =>
		isFloe( name )
			? {
					...settings,
					attributes: {
						...settings.attributes,
						hiddenFromVisitors: { type: 'boolean', default: false },
					},
				}
			: settings
);

addFilter(
	'editor.BlockEdit',
	'floe/hidden-from-visitors/control',
	createHigherOrderComponent(
		( BlockEdit ) => ( props ) => {
			if ( ! isFloe( props.name ) ) {
				return <BlockEdit { ...props } />;
			}
			return (
				<>
					<BlockEdit { ...props } />
					<InspectorAdvancedControls>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Hide from visitors', 'floe' ) }
							help={ __(
								'Only people who can edit this page will see this block, tagged “Hidden from visitors”. Use it for anything that isn’t ready to go live.',
								'floe'
							) }
							checked={ !! props.attributes.hiddenFromVisitors }
							onChange={ ( next ) =>
								props.setAttributes( {
									hiddenFromVisitors: next,
								} )
							}
						/>
					</InspectorAdvancedControls>
				</>
			);
		},
		'withHideFromVisitors'
	)
);

addFilter(
	'editor.BlockListBlock',
	'floe/hidden-from-visitors/marker',
	createHigherOrderComponent(
		( BlockListBlock ) => ( props ) => {
			if (
				! isFloe( props.name ) ||
				! props.attributes.hiddenFromVisitors
			) {
				return <BlockListBlock { ...props } />;
			}
			return (
				<BlockListBlock
					{ ...props }
					className={ [ props.className, 'hidden-from-visitors' ]
						.filter( Boolean )
						.join( ' ' ) }
					wrapperProps={ {
						...props.wrapperProps,
						'data-hidden-label': __(
							'Hidden from visitors',
							'floe'
						),
					} }
				/>
			);
		},
		'withHiddenFromVisitorsMarker'
	)
);
