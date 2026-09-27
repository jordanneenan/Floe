/*
 * Carousel: React twin of carousel.php for editor previews. The editor shows
 * the same markup without the behaviour: slides scroll sideways (the buttons
 * are only a preview), and a ticker sits still so each item can be edited.
 *
 * import { Carousel, CarouselControls } from '@floe/components/carousel';
 *
 * Pass the track's props (from useInnerBlocksProps, say) as `trackProps`,
 * or the slides as children.
 */
import { Icon } from '@floe/components/icon';

export function CarouselControls() {
	return (
		<div className="carousel__controls" aria-hidden="true">
			<button type="button" className="carousel__prev" tabIndex={ -1 }>
				<Icon name="arrow-left" />
			</button>
			<button type="button" className="carousel__next" tabIndex={ -1 }>
				<Icon name="arrow" />
			</button>
		</div>
	);
}

export function Carousel( {
	mode = 'slides',
	perView = [ 1, 1, 1 ],
	list = false,
	className = '',
	trackProps = {},
	children,
} ) {
	const classes = `carousel carousel--${ mode } ${ className }`.trim();
	const track = {
		...trackProps,
		className: `carousel__track ${ trackProps.className || '' }`.trim(),
	};

	if ( mode === 'ticker' ) {
		const Track = list ? 'ul' : 'div';
		return (
			<div className={ classes }>
				<div className="carousel__viewport">
					<Track { ...track }>{ children }</Track>
				</div>
			</div>
		);
	}

	const [ mobile, tablet = mobile, desktop = tablet ] = perView;
	return (
		<div
			className={ classes }
			style={ {
				'--carousel-mobile': mobile,
				'--carousel-tablet': tablet,
				'--carousel-desktop': desktop,
			} }
		>
			<div { ...track }>{ children }</div>
			<CarouselControls />
		</div>
	);
}
