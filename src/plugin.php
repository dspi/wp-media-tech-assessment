<?php
/**
 * Plugin main class
 *
 * @package     tech-assessment
 * @since       1.0.0
 * @author      David Spiller
 * @license     GPL-2.0-or-later
 */

namespace ROCKET_WP_CRAWLER;

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class Rocket_Wpc_Plugin_Class {

	/**
	 * The loader is responsible for maintaining and registering the plugins hooks.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Loader    $loader    Maintains and registers all plugin hooks.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Manages plugin initialization
	 *
	 * @return void
	 */
	public function __construct() {
		$this->plugin_name = ROCKET_CRWL_PLUGIN_NAME;
		$this->version     = ROCKET_CRWL_PLUGIN_VERSION;

		$this->load_dependencies();

		$this->define_admin_hooks();

		$this->define_public_hooks();

		// Register plugin lifecycle hooks.
		register_deactivation_hook( ROCKET_CRWL_PLUGIN_FILENAME, array( $this, 'wpc_deactivate' ) );

		$this->loader->run();
	}

	/**
	 * Load plugin dependencies
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_dependencies() {
		// Plugin_Loader. Orchestrates the hooks of the plugin.
		require_once plugin_dir_path( __DIR__ ) . 'src/plugin-loader.php';

		// Plugin_Admin. Defines all hooks for the admin area.
		require_once plugin_dir_path( __DIR__ ) . 'src/admin/plugin-admin.php';

		// Plugin_Public. Defines all hooks for the public area.
		require_once plugin_dir_path( __DIR__ ) . 'src/public/plugin-public.php';

		// Settings_Page_Public. Defines all hooks for the public area.

		$this->loader = new Plugin_Loader();
	}

	/**
	 * Handles plugin activation:
	 *
	 * @return void
	 */
	public static function wpc_activate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Initialize DB Table.
		self::initialize_db_table();

		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
	}

	/**
	 * Handles plugin deactivation
	 *
	 * @return void
	 */
	public function wpc_deactivate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
	}

	/**
	 * Handles plugin uninstall
	 *
	 * @return void
	 */
	public static function wpc_uninstall() {

		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
	}

	/**
	 * Retrieve the plugin name.
	 *
	 * @since     1.0.0
	 * @return    string    The plugin name.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the plugin version.
	 *
	 * @since     1.0.0
	 * @return    string    The plugin version.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Initializes the plugin database table.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	private static function initialize_db_table() {

		// WP Globals.
		global $table_prefix, $wpdb;

		$crawler_db_table = $table_prefix . ROCKET_CRWL_PLUGIN_NAME; // wp_crawler-plugin.

		// Create Customer Table if not exist.
		// if ( $wpdb->get_var( "SHOW TABLES LIKE '$crawler_db_table'" ) != $crawler_db_table ) {.
		$exists = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', $crawler_db_table ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		if ( ! $exists ) {

			// Query - Create Table.
			$sql  = 'START TRANSACTION;';
			$sql .= "CREATE TABLE IF NOT EXISTS `$crawler_db_table` (";
			$sql .= '`crawl_id` int NOT NULL AUTO_INCREMENT, ';
			$sql .= '`crawl_date` bigint NOT NULL, ';
			$sql .= '`crawl_result` json DEFAULT NULL, ';
			$sql .= 'PRIMARY KEY (`crawl_id`), ';
			$sql .= 'UNIQUE KEY `crawl_id` (`crawl_id`) ';
			$sql .= ') ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
			$sql .= 'COMMIT;';

			// Include Upgrade Script.
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';

			// Create Table.
			dbDelta( $sql );
		}
	}

	/**
	 * Register all of the hooks related to the admin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new \ROCKET_WP_CRAWLER\Admin\Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to public
	 *
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new \ROCKET_WP_CRAWLER\Public\Plugin_Public( $this->get_plugin_name(), $this->get_version() );
	}
}
