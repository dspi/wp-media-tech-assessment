<?php
/**
 * Crawler Class
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin
 */

namespace DSPI_ROCKET_WP_CRAWLER\Admin\Crawler;

const USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36";

use voku\helper\HtmlDomParser;

class Crawler {

    private $url;
    public $baseUrl;
    private $internalLinks = [];

    function __construct($url) {
        $this->url = $url;
        $sourceUrl = parse_url($this->url);
        $this->baseUrl = $sourceUrl['scheme'] . '://' . $sourceUrl['host'];
    }

	/**
	 * Recursively scrapes the given url for  internal links.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url	The url to crawl.
	 *
	 * @return array		The crawl results
	 */
    public function scrapeInternalLinksRecursively($url = null) {
        $scrape_url = $url == null ? $this->url : $url;
        $page = $this->getPage($scrape_url);
		$html = $page['html'];

        $htmlDomParser = HtmlDomParser::str_get_html($html);
        $linkElements = $htmlDomParser->find("a");

        foreach ($linkElements as $linkElement) {
            $link = $linkElement->getAttribute("href");
            //trim and filter duplicates, external links & admin links
           if (
               	!in_array($link, $this->internalLinks)
               	&& str_starts_with($link, $this->baseUrl) //internal
				&& !str_contains($link, '/wp-admin') //no admin
				&& !str_contains($link, '/#') //no page anchors
				&& !str_contains($link, '?') //no parameters
            ) {
                $trim_link = explode('?', $link)[0];
                array_push($this->internalLinks, $trim_link);
                $this->scrapeInternalLinksRecursively($trim_link);
            }
        }

		return $this->internalLinks;
    }

	/**
	 * Gets html page content using curl.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url	The url of the page to fetch.
	 *
	 * @return array		The fetched results
	 */
    public function getPage($url) {
		$res = [];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

		$res['html'] = curl_exec($curl);

		if (!curl_errno($curl)) {
			$res['resp'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		}
		else{
			$res['resp'] = curl_strerror(curl_errno($curl));
		}

        curl_close($curl);

		return $res;
    }
}
