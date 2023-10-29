<?php

namespace ROCKET_WP_CRAWLER\Admin\Crawler;

/**
 * Wp_remote_get wrapper Class providing limited functionality, and allowing for easier testing.
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class RemoteGetWrapper {

	/**
	 * Executes a wp_remote_get request.
	 *
	 * @param string $url The url of the page to fetch.
	 */
	public function remote_get( $url ) {
		$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
		$res        = array();

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			$res['code'] = wp_remote_retrieve_response_code( $request );
		}

		$res['html'] = wp_remote_retrieve_body( $request );
		$res['code'] = wp_remote_retrieve_response_code( $request );

		return $res;
	}
}
