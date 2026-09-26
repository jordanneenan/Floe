<?php
/** Document footer. */

defined( 'ABSPATH' ) || exit;

echo Floe\component( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output is escaped.
wp_footer();
?>
</body>
</html>
