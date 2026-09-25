/**
 * Card: React twin of card.php. Text parts can be nodes or render functions
 * receiving { tagName, className } (for RichText).
 *
 * import { Card } from '@floe/components/card';
 */
import { Icon } from '@floe/components/icon';

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

export function Card( { variant = 'post', media, category, date, number, title, text, linkLabel, headingLevel = 3, linked = false } ) {
	return (
		<article className={ `card card--${ variant }${ linked ? ' card--linked' : '' }` }>
			{ variant === 'feature' && number === 'auto' && <p className="card__number card__number--auto" aria-hidden="true" /> }
			{ variant === 'feature' && number && number !== 'auto' && part( number, 'p', 'card__number' ) }
			{ variant !== 'feature' && media }
			{ variant === 'post' && ( category || date ) && (
				<div className="card__meta">
					{ category && <span className="card__chip">{ category }</span> }
					{ date && <time className="card__date">{ date }</time> }
				</div>
			) }
			{ part( title, `h${ headingLevel }`, 'card__title' ) }
			{ part( text, 'p', 'card__text' ) }
			{ variant === 'feature' && linkLabel && (
				<span className="card__more button button--link" aria-hidden="true">
					<span className="button__label">{ linkLabel }</span>
					<Icon name="arrow" className="button__icon" />
				</span>
			) }
		</article>
	);
}
