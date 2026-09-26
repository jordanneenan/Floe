<?php
/**
 * FAQ: heading group plus core Details items. Optionally only one open at a
 * time (native exclusive <details>), and optional FAQPage structured data.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( false === strpos( $content, '<details' ) ) {
	return;
}

if ( $attributes['oneOpen'] ) {
	$name      = wp_unique_id( 'faq-' );
	$processor = new WP_HTML_Tag_Processor( $content );
	while ( $processor->next_tag( 'details' ) ) {
		$processor->set_attribute( 'name', $name );
	}
	$content = $processor->get_updated_html();
}

$schema = '';
if ( $attributes['schema'] ) {
	$entities = array();
	foreach ( $block->inner_blocks as $item ) {
		$question = trim( wp_strip_all_tags( (string) ( $item->attributes['summary'] ?? '' ) ) );
		$answer   = '';
		foreach ( $item->inner_blocks as $part ) {
			$answer .= $part->render();
		}
		$answer = trim( wp_strip_all_tags( $answer ) );
		if ( $question && $answer ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
	}
	if ( $entities ) {
		$schema = '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
		) . '</script>';
	}
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="faq__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'layout'        => 'stacked',
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'intro'         => $attributes['intro'],
				'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => 'link' ) ),
			)
		);
		?>
		<div class="faq__items"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
	<?php echo $schema; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with JSON_HEX_TAG. ?>
</section>
