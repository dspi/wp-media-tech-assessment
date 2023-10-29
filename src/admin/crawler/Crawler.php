<?php

namespace ROCKET_WP_CRAWLER\Admin\Crawler;

use voku\helper\HtmlDomParser;

/**
 * Crawler Class
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class Crawler {

	/**
	 * The array of internal links found.
	 *
	 * @var array
	 */
	public $internal_links = array();

	/**
	 * The base url.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * The url to crawl.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * The remote_get object.
	 *
	 * @var object
	 */
	private $remote_get;

	/**
	 * The Crawler object.
	 *
	 * @since     1.0.0
	 *
	 * @param string $url string The url to crawl.
	 *
	 * @param object $remote_get RemoteGetWrapper A remote_get wrapper object providing limited remote_get functionality.
	 */
	public function __construct( $url, $remote_get ) {
		$this->url        = $url;
		$this->remote_get = $remote_get;

		$source_url     = wp_parse_url( $this->url );
		$this->base_url = $source_url['scheme'] . '://' . $source_url['host'];
	}

	/**
	 * Recursively scrapes the given url for  internal links.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The url to crawl.
	 *
	 * @return array The crawl results
	 */
	public function scrape_internal_links_recursively( $url = null ) {
		$scrape_url = null === $url ? $this->url : $url;
		$page       = $this->remote_get->remote_get( $scrape_url );
		if ( isset( $page['html'] ) ) {
			$html = $page['html'];
		} else {
			return;
		}

		$html_dom_parser = HtmlDomParser::str_get_html( $html );
		$link_elements   = $html_dom_parser->find( 'a' );

		foreach ( $link_elements as $link_elements ) {
			$link = $link_elements->getAttribute( 'href' );
			// Trim and filter duplicates, external links & admin links.
			if (
				! in_array( $link, $this->internal_links, true )
				&& str_starts_with( $link, $this->base_url ) // internal.
				&& ! str_contains( $link, '/wp-admin' ) // no admin.
				&& ! str_contains( $link, '/#' ) // no page anchors.
				&& ! str_contains( $link, '?' ) // no parameters.
			) {
				$trim_link = explode( '?', $link )[0];
				array_push( $this->internal_links, $trim_link );
				$this->recursive_helper( $trim_link );
			}
		}

		return $this->internal_links;
	}

	/**
	 * Instead of scrape_internal_links_recursively calling itself, making it difficult to test,
	 * This helper does that job.
	 *
	 * @param string $link The recursively obtained link.
	 */
	private function recursive_helper( $link ) {
		$this->scrape_internal_links_recursively( $link );
	}
}
