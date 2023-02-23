<?php
/**
 * Plugin Name: Pwned Checker
 * Plugin URI: https://github.com/wpninj/pwned-checker
 * Description: Plugin to check if email addresses pwned by using haveibeenpwned.com API.
 * Version: 0.0.1
 * Author:  Patrick Heird
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: pwned-checker
 *
 * @package PwnedChecker
 */

if ( version_compare( phpversion(), '7.4', '>=' ) ) {
	require_once __DIR__ . '/instance.php';
	require_once __DIR__ . '/php/class-plugin-base.php';
	require_once __DIR__ . '/php/class-plugin.php';

	add_action( 'init', 'PwnedChecker\\get_plugin_instance' );

} else {
	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::warning( _pwned_checker_php_version_text() );
	} else {
		add_action( 'admin_notices', '_pwned_checker_php_version_error' );
	}
}

/**
 * Admin notice for incompatible versions of PHP.
 */
function _pwned_checker_php_version_error() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( _pwned_checker_php_version_text() ) );
}

/**
 * String describing the minimum PHP version.
 *
 * @return string
 */
function _pwned_checker_php_version_text() {
	return esc_html__( 'Pwned Checker plugin error: Your version of PHP is too old to run this plugin. You must be running PHP 7.4 or higher.', 'pwned-checker' );
}
