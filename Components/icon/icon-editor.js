/**
 * Icon: React twin of icon.php for editor previews.
 * import { Icon } from '@floe/components/icon';
 */
const ICONS = {
	arrow: [ 16, { d: 'M3 8H13M9 12L13 8L9 4', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round' } ],
	'arrow-left': [ 16, { d: 'M13 8H3M7 12L3 8L7 4', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round' } ],
	download: [ 18, { d: 'M9 3V13M13.5 8.5L9 13L4.5 8.5M3.5 15.5H14.5', strokeWidth: 1.7, strokeLinecap: 'round', strokeLinejoin: 'round' } ],
	play: [ 22, { d: 'M7 4.8V17.2C7 18 7.9 18.5 8.6 18.1L18.3 11.9C18.9 11.5 18.9 10.5 18.3 10.1L8.6 3.9C7.9 3.5 7 4 7 4.8Z', fill: 'currentColor', stroke: 'none' } ],
	plus: [ 16, { d: 'M8 3V13M3 8H13', strokeWidth: 1.6, strokeLinecap: 'round' } ],
	minus: [ 16, { d: 'M3 8H13', strokeWidth: 1.6, strokeLinecap: 'round' } ],
	check: [ 18, { d: 'M4 9.5L7.2 12.5L14 5.5', strokeWidth: 1.8, strokeLinecap: 'round', strokeLinejoin: 'round' } ],
	dash: [ 18, { d: 'M5 9H13', strokeWidth: 1.6, strokeLinecap: 'round' } ],
	menu: [ 24, { d: 'M4 7H20M4 12H20M4 17H20', strokeWidth: 1.8, strokeLinecap: 'round' } ],
	close: [ 24, { d: 'M6 6L18 18M18 6L6 18', strokeWidth: 1.8, strokeLinecap: 'round' } ],
	chevron: [ 16, { d: 'M4 6L8 10L12 6', strokeWidth: 1.6, strokeLinecap: 'round', strokeLinejoin: 'round' } ],
	pause: [ 16, { d: 'M5.5 3.5V12.5M10.5 3.5V12.5', strokeWidth: 1.8, strokeLinecap: 'round' } ],
	'play-small': [ 16, { d: 'M5 3.6V12.4C5 13 5.6 13.3 6.1 13L12.9 8.6C13.4 8.3 13.4 7.7 12.9 7.4L6.1 3C5.6 2.7 5 3 5 3.6Z', fill: 'currentColor', stroke: 'none' } ],
};

export function Icon( { name, className = '' } ) {
	const icon = ICONS[ name ];
	if ( ! icon ) {
		return null;
	}
	const [ size, path ] = icon;
	return (
		<svg
			className={ `icon icon-${ name } ${ className }`.trim() }
			width={ size }
			height={ size }
			viewBox={ `0 0 ${ size } ${ size }` }
			fill="none"
			stroke="currentColor"
			aria-hidden="true"
			focusable="false"
		>
			<path { ...path } />
		</svg>
	);
}
