/*
 * Button: React twin of button.php. Blocks pass the label as a node (often a
 * RichText) so it can be edited in place.
 *
 * import { Button } from '@floe/components/button';
 * <Button style="primary" arrow label={ <RichText … /> } />
 */
import { Icon } from '@floe/components/icon';

export function Button( {
	style = 'primary',
	arrow = true,
	label,
	className = '',
} ) {
	return (
		<span className={ `button button--${ style } ${ className }`.trim() }>
			<span className="button__label">{ label }</span>
			{ arrow && <Icon name="arrow" className="button__icon" /> }
		</span>
	);
}
