<?php

namespace ROCKET_WP_CRAWLER\Admin\Crawler;

require_once plugin_dir_path( __DIR__ ) . 'crawler/RemoteGetWrapper.php';
require_once plugin_dir_path( __DIR__ ) . 'crawler/Crawler.php';
require_once plugin_dir_path( __DIR__ ) . 'crawler/DatabaseHandler.php';

/**
 * Handles Crawl Requests
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class CrawlRequestHandler {

	/**
	 * Crawls the internal links from this url.
	 *
	 * @since 1.0
	 *
	 * @param string $url_to_crawl The url to crawl.
	 * @param object $remote_get_wrapper The wp_remote_get wrapper.
	 * @param object $db_handler The database handler.
	 *
	 * @return mixed|false  The crawl results, or false on error.
	 */
	public function crawl( $url_to_crawl, $remote_get_wrapper, $db_handler ) {

		$crawler = new Crawler( $url_to_crawl, $remote_get_wrapper );

		$internal_links = $crawler->scrape_internal_links_recursively( $url_to_crawl );
		sort( $internal_links );

		$crawl_result = wp_json_encode( $internal_links );

		if ( $db_handler->insert_crawl_results( time(), $crawl_result ) ) {
			return $crawl_result;
		}

		return false;
	}

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
	public function insert_crawl_results_old( $crawl_date, $crawl_result ) {
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
