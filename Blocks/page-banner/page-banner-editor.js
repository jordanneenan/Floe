import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps, useText, LinkButton, MediaSlot, SurfaceControl } from '@floe/editor';
import { Breadcrumb } from '@floe/components/breadcrumb';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const { showBreadcrumb, showMedia, surface } = attributes;
	const text = useText( attributes, setAttributes );
	const title = useSelect( ( select ) => select( editorStore )?.getEditedPostAttribute( 'title' ), [] );
	const blockProps = useFloeBlockProps( name, { className: showMedia ? 'has-media' : 'no-media' }, { surface } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'floe' ) }>
					<SurfaceControl value={ surface } onChange={ ( next ) => setAttributes( { surface: next } ) } options={ [ 'tint', 'subtle', 'base', 'inverse' ] } />
					<ToggleControl __nextHasNoMarginBottom label={ __( 'Show breadcrumb', 'floe' ) } help={ __( 'Built automatically from the page hierarchy.', 'floe' ) } checked={ showBreadcrumb } onChange={ ( next ) => setAttributes( { showBreadcrumb: next } ) } />
					<ToggleControl __nextHasNoMarginBottom label={ __( 'Show media', 'floe' ) } checked={ showMedia } onChange={ ( next ) => setAttributes( { showMedia: next } ) } />
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="page-banner__inner">
					{ showBreadcrumb && <Breadcrumb current={ title } /> }
					<div className="page-banner__title-row">
						{ text( 'heading', title || __( 'Page title', 'floe' ) )( { tagName: 'h1', className: 'page-banner__heading' } ) }
						<div className="page-banner__supporting">
							{ text( 'intro', __( 'Intro', 'floe' ), { disableLineBreaks: false } )( { tagName: 'p', className: 'page-banner__intro' } ) }
							<LinkButton value={ attributes.link } onChange={ ( link ) => setAttributes( { link } ) } style="link" placeholder={ __( 'Optional link', 'floe' ) } />
						</div>
					</div>
					{ showMedia && <MediaSlot value={ attributes.media } onChange={ ( media ) => setAttributes( { media } ) } ratio="1248/440" className="page-banner__media" /> }
				</div>
			</section>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
