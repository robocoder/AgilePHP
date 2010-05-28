<?php
/**
 * @package com.makeabyte.agilephp.test.core
 */
class LoggerTest extends BaseTest {

	  /**
	   * @test
	   */
	  public function write() {

	  		 Logger::debug( 'This is a test' );
	  }
}
?>