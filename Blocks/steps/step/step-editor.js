import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { useText } from '@floe/editor';
import metadata from './block.json';

function Edit( { attributes, setAttributes, context } ) {
	const text = useText( attributes, setAttributes );
	const level = Math.min( 4, ( context[ 'floe/headingLevel' ] || 2 ) + 1 );
	const blockProps = useBlockProps( { className: 'step' } );
	return (
		<li { ...blockProps }>
			<span className="step__marker" aria-hidden="true" />
			<div className="step__body">
				{ text( 'title', __( 'Step title', 'floe' ) )( { tagName: `h${ level }`, className: 'step__title' } ) }
				{ text( 'text', __( 'What happens in this step', 'floe' ) )( { tagName: 'p', className: 'step__text' } ) }
			</div>
		</li>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
