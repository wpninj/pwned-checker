<?php
/**
 * Bootstrap the WP testing environment.
 *
 * @package PwnedChecker
 */

require_once dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php';

// Load WP unit test helper library.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

// Enable our plugin.
tests_add_filter(
	'muplugins_loaded',
	function() {
		require dirname( dirname( __DIR__ ) ) . '/pwned-checker.php';
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
