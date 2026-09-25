/*
 * PlayControl: React twin of play-control.php.
 * import { PlayControl } from '@floe/components/play-control';
 */
import { __ } from '@wordpress/i18n';
import { Icon } from '@floe/components/icon';

export function PlayControl( { duration } ) {
	return (
		<span className="play-control">
			<span className="play-control__circle">
				<Icon name="play" />
			</span>
			<span className="play-control__text">
				<span className="play-control__label">
					{ __( 'Play video', 'floe' ) }
				</span>
				{ duration && (
					<span className="play-control__duration">{ duration }</span>
				) }
			</span>
		</span>
	);
}
