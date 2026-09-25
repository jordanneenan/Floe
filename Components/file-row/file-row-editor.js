/**
 * FileRow: React twin of file-row.php.
 * import { FileRow } from '@floe/components/file-row';
 */
import { Icon } from '@floe/components/icon';

const CODES = { docx: 'DOC', xlsx: 'XLS', pptx: 'PPT', jpeg: 'JPG' };

export const typeCode = ( extension = '' ) => CODES[ extension.toLowerCase() ] || extension.toUpperCase().slice( 0, 4 );

export function FileRow( { extension = '', title, meta } ) {
	return (
		<span className="file-row">
			<span className="file-row__type" aria-hidden="true">{ typeCode( extension ) }</span>
			<span className="file-row__info">
				<span className="file-row__title">{ title }</span>
				<span className="file-row__meta">{ meta }</span>
			</span>
			<span className="file-row__action" aria-hidden="true"><Icon name="download" /></span>
		</span>
	);
}
