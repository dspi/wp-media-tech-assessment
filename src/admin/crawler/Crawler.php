<?php

namespace DSPI_ROCKET_WP_CRAWLER\Admin\Crawler;

use voku\helper\HtmlDomParser;

/**
 * Crawler Class
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 *
 * @param $url string The url to crawl.
 * @param $curl CurlWrapper A Curl wrapper object providing limited curl functionality.
 * @param $htmlDomParser HTMLParser A HtmlDomParser object providing limited html functionality.
 */
class Crawler {

	private $internalLinks = [];
	private $baseUrl;
    private $url;
	private $curl;
	private $htmlParser;


    function __construct($url, $curl, $htmlParser) {
        $this->url = $url;
		$this->curl = $curl;
		$this->htmlParser = $htmlParser;

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

		$parser = $this->htmlParser->get_parser($html);
        $linkElements = $parser->find("a");

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
                //$this->scrapeInternalLinksRecursively($trim_link);
				$this->recursiveHelper($trim_link);
            }
        }

		return $this->internalLinks;
    }

	//Instead of scrapeInternalLinksRecursively calling itself, making it difficult to test,
	//this helper does that job.
	private function recursiveHelper($link) {
		$this->scrapeInternalLinksRecursively($link);
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
		return $this->curl->exec_curl($url);
    }
}



/**
 * Curl Wrapper Class providing limited curl functionality, and allowing for easier testing.
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class CurlWrapper {

	public function exec_curl($url) {
		$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
		$res = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

		$res['html'] = curl_exec($ch);

		if (!curl_errno($ch)) {
			$res['resp'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}
		else{
			$res['resp'] = curl_strerror(curl_errno($ch));
		}

        curl_close($ch);

		return $res;
	}
}

/**
 * A HtmlDomParser Class providing limited functionality, and allowing for easier testing.
 *
 * @since      1.0.0
 *
 * @package    tech-assessment
 * @subpackage tech-assessment/admin/crawler
 */
class HTMLParser extends HtmlDomParser {

	public static function get_parser($html){
		return parent::str_get_html($html);
	}
}
