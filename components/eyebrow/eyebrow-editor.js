/*
 * Eyebrow: React twin of eyebrow.php. Pass the text as a node.
 * import { Eyebrow } from '@floe/components/eyebrow';
 */
export function Eyebrow( { children, className = '' } ) {
	return (
		<p className={ `eyebrow ${ className }`.trim() }>
			<span className="eyebrow__dot" aria-hidden="true" />
			<span className="eyebrow__text">{ children }</span>
		</p>
	);
}
