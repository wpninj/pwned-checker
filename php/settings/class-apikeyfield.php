<?php
/**
 * Create the API Key field.
 *
 * @package PwnedChecker\Settings
 */

namespace PwnedChecker\Settings;

/**
 * Stores the API Key.
 */
final class ApiKeyField extends FieldBase {

	/**
	 * Get field option name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'api_key';
	}

	/**
	 * Get current field id.
	 */
	protected function get_id() {
		return 'api_key';
	}

	/**
	 * Get current field title.
	 */
	protected function get_title() {
		return __( 'API Key', 'pwned-checker' );
	}

	/**
	 * Render API Key form input.
	 */
	public function render() {
		$options = $this->settings->get_settings();
		?>
		<input type='text' name='pwned_checker_settings[<?php echo esc_attr( $this->get_name() ); ?>]'
			value='<?php echo esc_attr( $options[ $this->get_name() ] ); ?>'
			aria-label="<?php echo esc_attr( __( 'API Key', 'pwned-checker' ) ); ?>">
		<?php
	}
}
