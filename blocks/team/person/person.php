<?php
/**
 * Person (child of Team): portrait (neutral placeholder when empty), name, role.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$name = trim( (string) $attributes['name'] );
if ( '' === $name ) {
	return;
}
$level = min( 4, (int) ( $block->context['floe/headingLevel'] ?? 2 ) + 1 );
$role  = trim( (string) $attributes['role'] );
?>
<li <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'media',
		(array) $attributes['portrait'] + array(
			'ratio'       => '4/5',
			'placeholder' => true,
			'sizes'       => '(min-width: 1440px) 294px, (min-width: 768px) 25vw, (min-width: 550px) 46vw, 92vw',
			'class'       => 'person__portrait',
		)
	);
	?>
	<h<?php echo (int) $level; ?> class="person__name"><?php echo wp_kses_post( $name ); ?></h<?php echo (int) $level; ?>>
	<?php if ( $role ) : ?>
		<p class="person__role"><?php echo wp_kses_post( $role ); ?></p>
	<?php endif; ?>
</li>
