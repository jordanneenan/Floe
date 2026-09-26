import { registerBlockType } from '@wordpress/blocks';
import {
	InspectorControls,
	RichText,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useFloeBlockProps } from '@floe/editor';
import { Button } from '@floe/components/button';
import metadata from './block.json';

// A preview field: the same markup as form.php, not focusable in the editor.
function Field( { label, placeholder, multiline = false } ) {
	const id = useInstanceId( Field, 'floe-form-field' );
	const Control = multiline ? 'textarea' : 'input';
	return (
		<div className="form__field">
			<label htmlFor={ id }>{ label }</label>
			<Control
				id={ id }
				placeholder={ placeholder }
				disabled
				tabIndex={ -1 }
			/>
		</div>
	);
}

function Edit( { attributes, setAttributes, clientId, name } ) {
	const { kind, submitLabel, showConsent, consent, success } = attributes;
	const parent = useSelect(
		( select ) => {
			const { getBlockRootClientId, getBlockName } =
				select( blockEditorStore );
			const root = getBlockRootClientId( clientId );
			return root ? getBlockName( root ) : '';
		},
		[ clientId ]
	);

	// A new form takes the type that suits where it was added.
	useEffect( () => {
		if ( ! kind ) {
			setAttributes( {
				kind: parent === 'floe/newsletter' ? 'signup' : 'enquiry',
			} );
		}
	}, [ kind, parent, setAttributes ] );

	const signup = kind === 'signup';
	const blockProps = useFloeBlockProps(
		name,
		{ className: `form--${ signup ? 'signup' : 'enquiry' }` },
		{ section: false }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form', 'floe' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Type', 'floe' ) }
						value={ signup ? 'signup' : 'enquiry' }
						options={ [
							{
								label: __( 'Enquiry form', 'floe' ),
								value: 'enquiry',
							},
							{
								label: __( 'Newsletter signup', 'floe' ),
								value: 'signup',
							},
						] }
						onChange={ ( next ) => setAttributes( { kind: next } ) }
					/>
					{ ! signup && (
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Ask for consent', 'floe' ) }
							checked={ showConsent }
							onChange={ ( next ) =>
								setAttributes( { showConsent: next } )
							}
						/>
					) }
					<TextareaControl
						__nextHasNoMarginBottom
						label={ __( 'Thank-you message', 'floe' ) }
						help={ __(
							'Shown in place of the form once it’s sent. Leave empty for the default.',
							'floe'
						) }
						value={ success }
						onChange={ ( next ) =>
							setAttributes( { success: next } )
						}
					/>
					<p className="components-base-control__help">
						{ __(
							'Entries are saved under Enquiries in the admin menu and emailed to the site’s administration email address (Settings → General).',
							'floe'
						) }
					</p>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="form__form">
					{ signup ? (
						<Field
							label={ __( 'Email address', 'floe' ) }
							placeholder={ __( 'name@company.com', 'floe' ) }
						/>
					) : (
						<>
							<div className="form__row">
								<Field
									label={ __( 'First name', 'floe' ) }
									placeholder={ __( 'Alex', 'floe' ) }
								/>
								<Field
									label={ __( 'Last name', 'floe' ) }
									placeholder={ __( 'Morgan', 'floe' ) }
								/>
							</div>
							<Field
								label={ __( 'Email address', 'floe' ) }
								placeholder={ __( 'name@company.com', 'floe' ) }
							/>
							<Field
								label={ __( 'Organisation', 'floe' ) }
								placeholder={ __( 'Company or team', 'floe' ) }
							/>
							<Field
								label={ __( 'How can we help?', 'floe' ) }
								placeholder={ __(
									'Tell us a little about your project',
									'floe'
								) }
								multiline
							/>
							{ showConsent && (
								<div className="form__consent">
									<input
										type="checkbox"
										disabled
										tabIndex={ -1 }
									/>
									<RichText
										tagName="span"
										value={ consent }
										onChange={ ( next ) =>
											setAttributes( { consent: next } )
										}
										placeholder={ __(
											'I agree to be contacted about my enquiry.',
											'floe'
										) }
										allowedFormats={ [ 'core/link' ] }
									/>
								</div>
							) }
						</>
					) }
					<Button
						arrow={ ! signup }
						className="form__submit"
						label={
							<RichText
								tagName="span"
								value={ submitLabel }
								onChange={ ( next ) =>
									setAttributes( { submitLabel: next } )
								}
								placeholder={
									signup
										? __( 'Subscribe', 'floe' )
										: __( 'Send message', 'floe' )
								}
								allowedFormats={ [] }
								withoutInteractiveFormatting
								disableLineBreaks
							/>
						}
					/>
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
