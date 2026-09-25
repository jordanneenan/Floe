import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, MediaPlaceholder, MediaUpload, MediaUploadCheck, BlockControls, RichText } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { FileRow } from '@floe/components/file-row';
import metadata from './block.json';

const formatSize = ( bytes ) => {
	if ( ! bytes ) {
		return '';
	}
	if ( bytes >= 1048576 ) {
		return `${ ( bytes / 1048576 ).toFixed( 1 ) } MB`;
	}
	return `${ Math.max( 1, Math.round( bytes / 1024 ) ) } KB`;
};

function Edit( { attributes, setAttributes } ) {
	const id = attributes.file?.id;
	const file = useSelect( ( select ) => ( id ? select( coreStore ).getEntityRecord( 'postType', 'attachment', id, { context: 'view' } ) : null ), [ id ] );
	const blockProps = useBlockProps( { className: 'download' } );
	const onSelect = ( media ) => setAttributes( { file: { id: media.id } } );

	if ( ! id ) {
		return (
			<li { ...blockProps }>
				<MediaPlaceholder icon="media-document" labels={ { title: __( 'File', 'floe' ), instructions: __( 'Upload or choose a PDF, Word, Excel or PowerPoint file.', 'floe' ) } } onSelect={ onSelect } accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.csv,.txt,.zip" allowedTypes={ [ 'application', 'text' ] } />
			</li>
		);
	}

	const extension = ( file?.source_url || '' ).split( '.' ).pop().split( '?' )[ 0 ];
	const meta = [ extension.toUpperCase(), formatSize( file?.media_details?.filesize ) ].filter( Boolean ).join( ' · ' );

	return (
		<li { ...blockProps }>
			<BlockControls>
				<MediaUploadCheck>
					<ToolbarGroup>
						<MediaUpload onSelect={ onSelect } allowedTypes={ [ 'application', 'text' ] } value={ id } render={ ( { open } ) => <ToolbarButton onClick={ open }>{ __( 'Replace file', 'floe' ) }</ToolbarButton> } />
					</ToolbarGroup>
				</MediaUploadCheck>
			</BlockControls>
			<FileRow
				extension={ extension }
				meta={ meta }
				title={
					<RichText
						tagName="span"
						value={ attributes.title }
						onChange={ ( title ) => setAttributes( { title } ) }
						placeholder={ file?.title?.rendered || __( 'File title', 'floe' ) }
						allowedFormats={ [] }
						disableLineBreaks
					/>
				}
			/>
		</li>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
