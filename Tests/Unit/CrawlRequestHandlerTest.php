<?php

namespace ROCKET_WP_CRAWLER\Tests;

use WPMedia\PHPUnit\Unit\TestCase;

use ROCKET_WP_CRAWLER\Admin\Crawler\CrawlRequestHandler;
use ROCKET_WP_CRAWLER\Admin\Crawler\DatabaseHandler;

class CrawlRequestHandlerTest extends TestCase {

	public function testCrawl() {

		$url1 = 'http://localhost/00_learn/php-wordpress-dev/wordpress/';

		$html1 = "<!DOCTYPE html>
		<html lang='en-US'>
		<head>
			<meta charset='UTF-8' />
		</head>
		<body>
			<a href='http://localhost/00_learn/php-wordpress-dev/wordpress/'>Internal</a>
			<a href='http://external/00_learn/php-wordpress-dev/wordpress/'>External</a>
		</body>
		";

		$expectedResponse1 = [
			'html' => $html1,
			'code' => 200,
		];


		$url2 = 'http://localhost/00_learn/php-wordpress-dev/wordpress/';

		$html2 = "<!DOCTYPE html>
		<html lang='en-US'>
		<head>
			<meta charset='UTF-8' />
		</head>
		<body>
			<a href='http://localhost/00_learn/php-wordpress-dev/wordpress/'>Internal</a>
			<a href='http://localhost/00_learn/php-wordpress-dev/wordpress/blog/'>Internal Blog</a>
		</body>
		";

		$expectedResponse2 = [
			'html' => $html2,
			'code' => 200,
		];


		$mockRemoteGetCurl = $this->getMockBuilder(RemoteGetWrapper::class)
		->setMethods(['remote_get']) // Specify the method to mock
		->getMock();
		$mockRemoteGetCurl->method('remote_get')
        ->with($url1)
		->willReturn($expectedResponse1);


		$mockRemoteGetCurl2 = $this->getMockBuilder(RemoteGetWrapper::class)
		->setMethods(['remote_get'])
		->getMock();
		$mockRemoteGetCurl2->method('remote_get')
        ->with($url2)
		->willReturn($expectedResponse2);


		$mockDatabaseHandler = $this->getMockBuilder(DatabaseHandler::class)
		->setMethods(['insert_crawl_results'])
		->getMock();

		$mockDatabaseHandler->method('insert_crawl_results')
		->willReturn(true);


		$handler    = new CrawlRequestHandler();
		$testResult = $handler->crawl('http://localhost/00_learn/php-wordpress-dev/wordpress/', $mockRemoteGetCurl, $mockDatabaseHandler);

		$exp_res = '["http:\/\/localhost\/00_learn\/php-wordpress-dev\/wordpress\/"]';

		$this->assertEquals($exp_res, $testResult);

	}

}
