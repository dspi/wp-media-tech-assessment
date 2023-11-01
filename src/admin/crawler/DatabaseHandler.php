<?php

namespace ROCKET_WP_CRAWLER\Admin\Crawler;

/**
 * Handles Database Queries
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class DatabaseHandler {

	/**
	 * Inserts the crawl results into the database.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $crawl_date    Unix timestamp.
	 * @param string  $crawl_result  Json encoded crawl results.
	 *
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert_crawl_results( $crawl_date, $crawl_result ) {
		global $table_prefix, $wpdb;
		$crawler_db_table = $table_prefix . ROCKET_CRWL_PLUGIN_NAME; // wp_crawler_plugin.

		include_once ABSPATH . 'wp-load.php';

		global $wpdb;
		$bind_params = array(
			'crawl_date'   => $crawl_date,
			'crawl_result' => $crawl_result,
		);
		$params_type = array( '%d', '%s' );
		$wpdb->show_errors();
		return $wpdb->insert( $crawler_db_table, $bind_params, $params_type ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	}
}
