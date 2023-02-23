<?php
/**
 * Plugin test class.
 */
class Test_Plugin extends WP_UnitTestCase {

	/**
	 * Check if WordPress and the plugin is loaded.
	 */
	public function test_wordpress_and_plugin_are_loaded() {
		$this->assertTrue( function_exists( 'do_action' ), 'WP is present' );
		$this->assertTrue( function_exists( 'PwnedChecker\get_plugin_instance' ), 'Plugin bootstrap function is present' );
	}

}
