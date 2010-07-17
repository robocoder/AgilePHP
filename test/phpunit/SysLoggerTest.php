<?php
/**
 * @package com.makeabyte.agilephp.test.logger
 */
class SysLoggerTest extends PHPUnit_Framework_TestCase {

	  private $timestamp;

	  public function setUp() {

	  		 $this->timestamp = date( 'c', strtotime( 'now' ) );
	  }

	  /**
	   * @test
	   */
	  public function info() {

	  		 $entry = 'info ' . $this->timestamp;

	  		 $logger = new SysLogger();
	  		 $logger->info( $entry );
	  }
	  
	  /**
	   * @test
	   */
	  public function warn() {

	  		 $entry = 'warn ' . $this->timestamp;

	  		 $logger = new SysLogger();
	  		 $logger->warn( $entry );
	  }
	  
	  /**
	   * @test
	   */
	  public function error() {

	  		 $entry = 'error ' . $this->timestamp;

	  		 $logger = new SysLogger();
	  		 $logger->error( $entry );
	  }

	  /**
	   * @test
	   */
	  public function debug() {

	  		 $entry = 'debug ' . $this->timestamp;

	  		 $logger = new SysLogger();
	  		 $logger->debug( $entry );
	  }
}
?>