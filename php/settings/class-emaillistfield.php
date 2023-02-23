<?php
/**
 * Create the Email List to check pwned field.
 *
 * @package PwnedChecker\Settings
 */

namespace PwnedChecker\Settings;

/**
 * Stores the Email List.
 */
final class EmailListField extends FieldBase {

	/**
	 * Get field option name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'email_list';
	}

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return 'email_list';
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'Emails', 'pwned-checker' );
	}

	/**
	 * Render.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		?>
		<input type='text' name='pwned_checker_settings[<?php echo esc_attr( $this->get_name() ); ?>]'
			value='<?php echo esc_attr( $options[ $this->get_name() ] ); ?>'
			aria-label="<?php echo esc_attr( __( 'Emails', 'pwned-checker' ) ); ?>">
		<p><?php echo esc_html__( 'Emails to check pwned in comma separated format.', 'pwned-checker' ); ?></p>
		<?php
	}
}
