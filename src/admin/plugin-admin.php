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

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name.'-custom-scripts', plugin_dir_url( __FILE__ ) . 'js/plugin-admin.js', array( 'jquery' ), $this->version, false );

		if ( current_user_can('manage_options')) {
			// Pass the WordPress AJAX URL to the script
			//wp_localize_script($this->plugin_name.'-custom-scripts', 'ajax_object', array('ajax_url' => plugin_dir_url( __FILE__ ) .'partials/admin-display.php'));
			wp_localize_script($this->plugin_name.'-custom-scripts', 'ajax_object', array('ajax_url' => plugin_dir_url( __FILE__ ) .'crawler/CrawlRequestHandler.php'));
		}
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
		 * First, we add_settings_section.
		 * Second, add_settings_field
		 * Third, register_setting
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
		unset($args);

		$args = array (
			'type'      => 'textarea',
			'subtype'   => 'text',
			'id'    => 'admin_page_field_setting',
			'name'      => 'admin_page_field_setting',
			'required' => 'true',
			'get_options_list' => '',
			'value_type'=>'normal',
			'wp_data' => 'option'
		);

		add_settings_field(
			'admin_page_field_setting',
			'Latest Crawler Results',
			array( $this, 'admin_page_render_settings_field' ),
			'admin_page_general_settings',
			'settings_page_general_section',
			$args
		);


		register_setting(
						'admin_page_general_settings',
						'admin_page_field_setting'
						);

	}

	public function admin_page_display_general_settings() {
		echo '<p>The Internal Crawler will fetch the internal links of this site and refresh the sitemap.</p>';
	}

	public function admin_page_render_settings_field($args) {
			/* EXAMPLE INPUT
								'type'      => 'input',
								'subtype'   => '',
								'id'    => $this->plugin_name.'_example_setting',
								'name'      => $this->plugin_name.'_example_setting',
								'required' => 'required="required"',
								'get_option_list' => "",
									'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
			*/
		if($args['wp_data'] == 'option'){
			$wp_data_value = get_option($args['name']);
		} elseif($args['wp_data'] == 'post_meta'){
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
		}

		switch ($args['type']) {

			case 'textarea':
					$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
					echo '<textarea id="'.$args['id'].'" "'.$args['required'].'" name="'.$args['name'].'" rows="4" style="width: 100%;" />' . esc_attr($value) . '</textarea>';
					break;
			default:
					break;
		}
	}
}
