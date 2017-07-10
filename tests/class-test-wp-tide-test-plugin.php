<?php
/**
 * Test_Wp_Tide_Test_Plugin
 *
 * @package WpTideTestPlugin
 */

namespace WpTideTestPlugin;

/**
 * Class Test_Wp_Tide_Test_Plugin
 *
 * @package WpTideTestPlugin
 */
class Test_Wp_Tide_Test_Plugin extends \WP_UnitTestCase {

	/**
	 * Test _wp_tide_test_plugin_php_version_error().
	 *
	 * @see _wp_tide_test_plugin_php_version_error()
	 */
	public function test_wp_tide_test_plugin_php_version_error() {
		ob_start();
		_wp_tide_test_plugin_php_version_error();
		$buffer = ob_get_clean();
		$this->assertContains( '<div class="error">', $buffer );
	}

	/**
	 * Test _wp_tide_test_plugin_php_version_text().
	 *
	 * @see _wp_tide_test_plugin_php_version_text()
	 */
	public function test_wp_tide_test_plugin_php_version_text() {
		$this->assertContains( 'Wp Tide Test Plugin plugin error:', _wp_tide_test_plugin_php_version_text() );
	}
}
