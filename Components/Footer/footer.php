<?php
/** Shared site footer. */

defined( 'ABSPATH' ) || exit;
?>
<footer class="floe-site-footer">
	<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
</footer>
