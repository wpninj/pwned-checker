<?php
/**
 * Create the Settings Page.
 *
 * This is where all the settings for the plugin can be added/edited
 * through an interface.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

use PwnedChecker\Interfaces\Initable;

/**
 * Main class for Settings
 */
final class Settings implements Initable {
	/**
	 * Plugin Settings page ID.
	 *
	 * @var string
	 */
	const PAGE_ID = 'pluginPage';

	/**
	 * Plugin Settings section ID.
	 *
	 * @var string
	 */
	const SECTION_ID = 'pwned_checker_pluginPage_section';

	/**
	 * Settings option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'pwned_checker_settings';

	/**
	 * All setting fields
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->fields = array(
			new Settings\ApiKeyField(),
			new Settings\EmailListField(),
			new Settings\NotifyEmailsField(),
		);
	}

	/**
	 * Initialize settings.
	 */
	public function init() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 */
	protected function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Add tracker as a settings menu item.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Pwned Checker', 'pwned-checker' ),
			__( 'Pwned Checker', 'pwned-checker' ),
			'manage_options',
			'pwned_checker',
			array(
				$this,
				'render_settings_page',
			)
		);
	}

	/**
	 * Initialize tracker settings by registering it and adding
	 * sections and fields.
	 */
	public function settings_init() {
		register_setting( self::PAGE_ID, self::OPTION_NAME );

		add_settings_section(
			self::SECTION_ID,
			null,
			array( $this, 'settings_section_callback' ),
			self::PAGE_ID
		);

		foreach ( $this->fields as $field ) {
			$field->init( $this, self::PAGE_ID, self::SECTION_ID );
		}
	}

	/**
	 * Echo section callback text.
	 */
	public function settings_section_callback() {
		echo esc_html( __( 'Update Pwned Checker settings', 'pwned-checker' ) );
	}

	/**
	 * Create and Output the settings page.
	 */
	public function render_settings_page() {
		?>
		<form action='options.php' method='post'>
			<h1><?php echo esc_html( __( 'Pwned Checker Settings', 'pwned-checker' ) ); ?></h1>

			<?php
			settings_fields( self::PAGE_ID );
			do_settings_sections( self::PAGE_ID );
			submit_button();
			?>
		</form>
		<?php
	}

	/**
	 * Returns the plugin settings.
	 */
	public function get_settings() {
		$defult = array();
		foreach ( $this->fields as $field ) {
			$defult[ $field->get_name() ] = $field->get_default();
		}
		$options = get_option( self::OPTION_NAME, array() );

		return array_merge( $defult, $options );
	}
	
	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Hook name.
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'settings_page_pwned_checker' == $hook ) {
			$instance = Utils::plugin();
			
			wp_enqueue_style( 'pwned-checker-css', $instance->url_to( 'assets/style.css' ), array(), $instance->asset_version() );
		}
	}
}
