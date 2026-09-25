/*
 * Media: React twin of media.php for editor previews. Takes resolved URLs.
 * Blocks normally use MediaSlot from @floe/editor, which selects media and
 * renders this.
 *
 * import { Media } from '@floe/components/media';
 */
export function Media( {
	url,
	alt = '',
	type = 'image',
	poster,
	ratio = '',
	radius = 'lg',
	cover = false,
	placeholder = false,
	className = '',
	children,
} ) {
	const classes = [ 'media', `media--radius-${ radius }` ];
	if ( type === 'video' ) {
		classes.push( 'media--video' );
	}
	if ( ! url ) {
		if ( ! placeholder ) {
			return null;
		}
		classes.push( 'media--placeholder' );
	}
	if ( className ) {
		classes.push( className );
	}
	const style = /^[0-9.]+\s*\/\s*[0-9.]+$/.test( ratio )
		? { aspectRatio: ratio }
		: undefined;
	if ( style || cover ) {
		classes.splice( 2, 0, 'media--cover' );
	}

	return (
		<div className={ classes.join( ' ' ) } style={ style }>
			{ url && type === 'image' && (
				<img className="media__image" src={ url } alt={ alt } />
			) }
			{ url && type === 'video' && (
				<video
					className="media__video"
					src={ url }
					poster={ poster }
					muted
					loop
					playsInline
					autoPlay
					aria-hidden="true"
				/>
			) }
			{ children }
		</div>
	);
}
