<?php
/**
 * Instantiates the PwnedChecker plugin
 *
 * @package PwnedChecker
 */

namespace PwnedChecker;

/**
 * Pwned Checker Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	static $pwned_checker_plugin;

	if ( is_null( $pwned_checker_plugin ) ) {
		$pwned_checker_plugin = new Plugin( __DIR__ . '/pwned-checker.php' );

		if ( function_exists( 'wp_get_environment_type' ) ) {
			$pwned_checker_plugin->set_site_environment_type( wp_get_environment_type() );
		}

		$pwned_checker_plugin->init();
	}

	return $pwned_checker_plugin;
}
