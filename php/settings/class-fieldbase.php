<?php
/**
 * Define Field Base abscract class.
 *
 * @package PwnedChecker\Settings
 */

namespace PwnedChecker\Settings;

/**
 * Defines base class for fields.
 */
abstract class FieldBase {
	/**
	 * Setting that current fields belong to.
	 *
	 * @var \PwnedChecker\Settings
	 */
	protected $settings;

	/**
	 * Initialize field.
	 *
	 * @param \PwnedChecker\Settings $settings settings that current fields belong to.
	 * @param string                 $page_id field page id.
	 * @param string                 $section_id field section id.
	 */
	public function init( $settings, $page_id, $section_id ) {
		add_settings_field(
			$this->get_id(),
			$this->get_title(),
			array( $this, 'render' ),
			$page_id,
			$section_id
		);

		$this->settings = $settings;
	}

	/**
	 * Get current field default value.
	 */
	public function get_default() {
		return '';
	}

	/**
	 * Get current field id.
	 */
	abstract protected function get_id();

	/**
	 * Get current field name.
	 */
	abstract public function get_name();

	/**
	 * Get current field title.
	 */
	abstract protected function get_title();

	/**
	 * Render form input.
	 */
	abstract public function render();
}
