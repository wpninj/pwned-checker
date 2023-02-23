<?php
/**
 * Bootstraps the PwnedChecker plugin.
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Initiate the plugin resources.
	 */
	public function init() {
		// Init every module.
		foreach ( $this->get_modules() as $module ) {
			$module->init();
		}
	}

	/**
	 * Register every module in this array.
	 *
	 * @return Module[]
	 */
	private function get_modules() {
		return array(
			new Module( Settings::class ),
			new Module( Cron::class ),
		);
	}
}
