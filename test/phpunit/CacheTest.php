<?php
/**
 * @package com.makeabyte.agilephp.test.cache
 */
class CacheTest extends PHPUnit_Framework_TestCase {

      private $expires = 'http://localhost/test/index.php/CacheController/testExpires';
      private $neverExpires = 'http://localhost/test/index.php/CacheController/testNeverExpires';

	  /**
	   * @test
	   */
	  public function expiresRealTimeProcessing() {

	  		 $curl = curl_init( $this->expires );
	  		 curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	  		 $response = curl_exec( $curl );
	  		 curl_close( $curl );
	  		 PHPUnit_Framework_Assert::assertFalse( strpos( $response, '<!-- Cached' ), 'Got cached data instead of real time data' );
	  }

	  /**
	   * @test
	   */
	  public function expiresCached() {

	  		 $curl = curl_init( $this->expires );
	  		 curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	  		 $response = curl_exec( $curl );
	  		 curl_close( $curl );

	  		 PHPUnit_Framework_Assert::assertEquals( 0, strpos( $response, '<!-- Cached' ), 'Failed to get cached data' );
	  }
}
?>