/*
 * SectionHeader: React twin of section-header.php.
 *
 * Each text part can be a node, or a function that receives
 * { tagName, className } and returns an element (e.g. a RichText), so the
 * editor produces the same markup as the front end.
 *
 * import { SectionHeader } from '@floe/components/section-header';
 */
import { Eyebrow } from '@floe/components/eyebrow';

const part = ( value, tagName, className ) => {
	if ( ! value ) {
		return null;
	}
	if ( typeof value === 'function' ) {
		return value( { tagName, className } );
	}
	const Tag = tagName;
	return <Tag className={ className }>{ value }</Tag>;
};

export function SectionHeader( {
	eyebrow,
	heading,
	headingLevel = 2,
	intro,
	action,
	aside,
	layout = 'split',
} ) {
	const eyebrowNode = eyebrow ? (
		<Eyebrow>
			{ typeof eyebrow === 'function'
				? eyebrow( { tagName: 'span', className: '' } )
				: eyebrow }
		</Eyebrow>
	) : null;
	const headingNode = part(
		heading,
		`h${ headingLevel }`,
		'section-header__heading'
	);
	const introNode = part( intro, 'p', 'section-header__intro' );
	const actionNode = action ? (
		<div className="section-header__action">{ action }</div>
	) : null;

	if ( layout === 'stacked' ) {
		return (
			<div className="section-header section-header--stacked">
				{ eyebrowNode }
				{ headingNode }
				{ introNode }
				{ actionNode }
				{ aside }
			</div>
		);
	}

	return (
		<div className="section-header section-header--split">
			<div className="section-header__main">
				{ eyebrowNode }
				{ headingNode }
			</div>
			{ ( introNode || actionNode || aside ) && (
				<div className="section-header__aside">
					{ introNode }
					{ actionNode }
					{ aside }
				</div>
			) }
		</div>
	);
}
