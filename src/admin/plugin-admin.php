<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin
 */

namespace DSPI_ROCKET_WP_CRAWLER\Admin;

class Plugin_Admin {

	/**
	 * The plugin ID.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The plugin ID.
	 */
	private $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The plugin version.
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @since    1.0.0
	 * @param      string    $version    The plugin version.
	 * @param      string    $plugin_name       The plugin name.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array( $this, 'addPluginAdminMenuToSettings' ), 10);
		add_action('admin_init', array( $this, 'registerAndBuildFields' ));

	}

	public function addPluginAdminMenuToSettings(){
		//add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null);
		add_options_page($this->plugin_name, 'Internal Crawler', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminSettings'), 1);
	}

	public function displayPluginAdminSettings() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/admin-display.php';
	}

	public function registerAndBuildFields() {
		/**
		 * Add_settings_section.
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'settings_page_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
				array( $this, 'admin_page_display_general_settings' ),
			// Page on which to add this section of options
			'admin_page_general_settings'
		);
	}

	public function admin_page_display_general_settings() {
		echo '<p>The Internal Crawler will fetch the internal links of this site and refresh the sitemap.</p>';
	}

}
