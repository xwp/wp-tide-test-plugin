<?php
/**
 * Bootstraps the Wp Tide Test Plugin plugin.
 *
 * @package WpTideTestPlugin
 */

namespace WpTideTestPlugin;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {
	var $config;
	public function init() {
		$this->config=apply_filters( 'wp_tide_test_plugin_plugin_config', $this->config, $this );
	}
	public function register_scripts(\WP_Scripts $wp_scripts){}
	public function register_styles(\WP_Styles $wp_styles){}
}
