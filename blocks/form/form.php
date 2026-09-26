<?php
/**
 * Form (child of Contact and Newsletter): Floe's built-in form, so a site
 * needs no form plugin. "enquiry" is the Contact form from Figma (name,
 * email, organisation, message, consent); "signup" is Newsletter's single
 * email field. It posts to admin-post.php, which works without JavaScript;
 * form.js sends it in the background and shows the result in place.
 * Saving, emailing and spam checks are in form-server.php.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;
use Floe\Blocks\Form;

defined( 'ABSPATH' ) || exit;

$kind    = Form\kind( $attributes['kind'] );
$id      = Form\next_id( $kind );
$status  = Form\status( $kind );
$signup  = 'signup' === $kind;
$field   = static fn( string $name ): string => $id . '-' . $name;
$submit  = trim( wp_strip_all_tags( (string) $attributes['submitLabel'] ) ) ?: ( $signup ? __( 'Subscribe', 'floe' ) : __( 'Send message', 'floe' ) );
$consent = trim( (string) $attributes['consent'] ) ?: __( 'I agree to be contacted about my enquiry.', 'floe' );
$success = trim( (string) $attributes['success'] ) ?: ( $signup ? __( 'Thanks, you’re on the list.', 'floe' ) : __( 'Thanks for getting in touch. We’ll reply within one working day.', 'floe' ) );
$error   = __( 'Sorry, that didn’t send. Please check your details and try again.', 'floe' );
?>
<div <?php echo Floe\block_attributes( $block, array( 'class' => 'form--' . $kind, 'id' => $id, 'data-floe-form' => '', 'data-error' => $error ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<form class="form__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"<?php echo 'sent' === $status ? ' hidden' : ''; ?>>
		<?php if ( $signup ) : ?>
			<div class="form__field">
				<label for="<?php echo esc_attr( $field( 'email' ) ); ?>"><?php esc_html_e( 'Email address', 'floe' ); ?></label>
				<input id="<?php echo esc_attr( $field( 'email' ) ); ?>" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'name@company.com', 'floe' ); ?>" required>
			</div>
		<?php else : ?>
			<div class="form__row">
				<div class="form__field">
					<label for="<?php echo esc_attr( $field( 'first' ) ); ?>"><?php esc_html_e( 'First name', 'floe' ); ?></label>
					<input id="<?php echo esc_attr( $field( 'first' ) ); ?>" type="text" name="first_name" autocomplete="given-name" placeholder="<?php esc_attr_e( 'Alex', 'floe' ); ?>" maxlength="100" required>
				</div>
				<div class="form__field">
					<label for="<?php echo esc_attr( $field( 'last' ) ); ?>"><?php esc_html_e( 'Last name', 'floe' ); ?></label>
					<input id="<?php echo esc_attr( $field( 'last' ) ); ?>" type="text" name="last_name" autocomplete="family-name" placeholder="<?php esc_attr_e( 'Morgan', 'floe' ); ?>" maxlength="100">
				</div>
			</div>
			<div class="form__field">
				<label for="<?php echo esc_attr( $field( 'email' ) ); ?>"><?php esc_html_e( 'Email address', 'floe' ); ?></label>
				<input id="<?php echo esc_attr( $field( 'email' ) ); ?>" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'name@company.com', 'floe' ); ?>" required>
			</div>
			<div class="form__field">
				<label for="<?php echo esc_attr( $field( 'organisation' ) ); ?>"><?php esc_html_e( 'Organisation', 'floe' ); ?></label>
				<input id="<?php echo esc_attr( $field( 'organisation' ) ); ?>" type="text" name="organisation" autocomplete="organization" placeholder="<?php esc_attr_e( 'Company or team', 'floe' ); ?>" maxlength="150">
			</div>
			<div class="form__field">
				<label for="<?php echo esc_attr( $field( 'message' ) ); ?>"><?php esc_html_e( 'How can we help?', 'floe' ); ?></label>
				<textarea id="<?php echo esc_attr( $field( 'message' ) ); ?>" name="message" placeholder="<?php esc_attr_e( 'Tell us a little about your project', 'floe' ); ?>" maxlength="5000" required></textarea>
			</div>
			<?php if ( $attributes['showConsent'] ) : ?>
				<label class="form__consent"><input type="checkbox" name="consent" value="1" required> <?php echo wp_kses_post( $consent ); ?></label>
			<?php endif; ?>
		<?php endif; ?>
		<div class="form__trap" aria-hidden="true">
			<label for="<?php echo esc_attr( $field( 'website' ) ); ?>"><?php esc_html_e( 'Leave this field empty', 'floe' ); ?></label>
			<input id="<?php echo esc_attr( $field( 'website' ) ); ?>" type="text" name="<?php echo esc_attr( Form\TRAP ); ?>" tabindex="-1" autocomplete="off">
		</div>
		<input type="hidden" name="action" value="<?php echo esc_attr( Form\ACTION ); ?>">
		<input type="hidden" name="floe_kind" value="<?php echo esc_attr( $kind ); ?>">
		<input type="hidden" name="floe_page" value="<?php echo (int) get_queried_object_id(); ?>">
		<input type="hidden" name="floe_token" value="<?php echo esc_attr( Form\token() ); ?>">
		<p class="form__message" role="alert"<?php echo 'error' === $status ? '' : ' hidden'; ?>><?php echo 'error' === $status ? esc_html( $error ) : ''; ?></p>
		<?php
		echo component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'button',
			array(
				'label' => $submit,
				'type'  => 'submit',
				'arrow' => ! $signup,
				'class' => 'form__submit',
			)
		);
		?>
	</form>
	<div class="form__success" role="status" tabindex="-1"<?php echo 'sent' === $status ? '' : ' hidden'; ?>>
		<?php echo component( 'icon', array( 'name' => 'check' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p><?php echo wp_kses_post( $success ); ?></p>
	</div>
</div>
