<?php
/**
 * Video: section header, then a YouTube cover with the Play control. The
 * privacy-enhanced (youtube-nocookie.com) iframe is created only on click.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

/** The 11-character YouTube ID from watch, youtu.be, shorts, embed or live URLs. */
$floe_video_id = static function ( string $url ): string {
	if ( preg_match( '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $match ) ) {
		return $match[1];
	}
	return '';
};

$video_id = $floe_video_id( (string) $attributes['url'] );
$title    = trim( wp_strip_all_tags( (string) $attributes['heading'] ) );
$cover    = component(
	'media',
	(array) $attributes['cover'] + array(
		'ratio'       => '16/9',
		'radius'      => 'xl',
		'placeholder' => true,
		'sizes'       => '(min-width: 1440px) 1248px, 92vw',
		'class'       => 'video__cover',
	)
);
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="video__inner">
		<?php
		echo component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'intro'         => $attributes['intro'],
			)
		);
		?>
		<div class="video__player"<?php echo $video_id ? ' data-video-id="' . esc_attr( $video_id ) . '" data-video-title="' . esc_attr( $title ?: __( 'Video', 'floe' ) ) . '"' : ''; ?>>
			<?php echo $cover; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $video_id ) : ?>
				<div class="video__control">
					<?php echo component( 'play-control', array( 'title' => $title, 'duration' => $attributes['duration'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
