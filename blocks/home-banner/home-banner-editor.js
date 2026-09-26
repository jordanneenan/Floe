import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
	useFloeBlockProps,
	useText,
	LinkButton,
	MediaSlot,
} from '@floe/editor';
import { Eyebrow } from '@floe/components/eyebrow';
import metadata from './block.json';

function Edit( { attributes, setAttributes, name } ) {
	const text = useText( attributes, setAttributes );
	const blockProps = useFloeBlockProps( name, {}, { surface: 'base' } );

	return (
		<section { ...blockProps }>
			<div className="home-banner__inner">
				<div className="home-banner__intro">
					<div className="home-banner__heading-group">
						<Eyebrow>
							{ text( 'eyebrow', __( 'Eyebrow', 'floe' ), {
								allowedFormats: [],
							} )( { tagName: 'span' } ) }
						</Eyebrow>
						{ text(
							'heading',
							__(
								'Headline (defaults to the page title)',
								'floe'
							)
						)( {
							tagName: 'h1',
							className: 'home-banner__heading',
						} ) }
					</div>
					<div className="home-banner__supporting">
						{ text( 'body', __( 'Supporting copy', 'floe' ), {
							disableLineBreaks: false,
						} )( {
							tagName: 'p',
							className: 'home-banner__body',
						} ) }
						<div className="home-banner__actions">
							<LinkButton
								value={ attributes.primaryAction }
								onChange={ ( primaryAction ) =>
									setAttributes( { primaryAction } )
								}
								style="primary"
								placeholder={ __( 'Primary action', 'floe' ) }
							/>
							<LinkButton
								value={ attributes.secondaryAction }
								onChange={ ( secondaryAction ) =>
									setAttributes( { secondaryAction } )
								}
								style="secondary"
								arrow={ false }
								placeholder={ __(
									'Optional second action',
									'floe'
								) }
							/>
						</div>
					</div>
				</div>
				<MediaSlot
					value={ attributes.media }
					onChange={ ( media ) => setAttributes( { media } ) }
					ratio="1248/600"
					radius="xl"
					className="home-banner__media"
				/>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
