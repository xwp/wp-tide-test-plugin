<?php
/**
 * Instantiates the Wp Tide Test Plugin plugin
 *
 * @package WpTideTestPlugin
 */

namespace WpTideTestPlugin;

global $wp_tide_test_plugin_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$wp_tide_test_plugin_plugin = new Plugin();

/**
 * Wp Tide Test Plugin Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $wp_tide_test_plugin_plugin;
	return $wp_tide_test_plugin_plugin;
}
