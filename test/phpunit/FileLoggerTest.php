<?php
/**
 * @package com.makeabyte.agilephp.test.logger
 */
class FileLoggerTest extends BaseTest {

	  private $timestamp;

	  public function setUp() {

	  		 $this->timestamp = date( 'c', strtotime( 'now' ) );
	  }

	  /**
	   * @test
	   */
	  public function info() {

	  		 $entry = 'info ' . $this->timestamp;

	  		 $logger = new FileLogger();
	  		 $logger->info( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written info entry in log' );
	  }
	  
	  /**
	   * @test
	   */
	  public function warn() {

	  		 $entry = 'warn ' . $this->timestamp;

	  		 $logger = new FileLogger();
	  		 $logger->warn( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written warn entry in log' );
	  }
	  
	  /**
	   * @test
	   */
	  public function error() {

	  		 $entry = 'error ' . $this->timestamp;

	  		 $logger = new FileLogger();
	  		 $logger->error( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written error entry in log' );
	  }

	  /**
	   * @test
	   */
	  public function debug() {

	  		 $entry = 'debug ' . $this->timestamp;

	  		 $logger = new FileLogger();
	  		 $logger->debug( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written debug entry in log' );
	  }
	  
	  /**
	   * @test
	   */
	  public function singleton_info() {

	  		 $entry = 'singleton_info ' . $this->timestamp;

	  		 $logger = LogFactory::getLogger( 'FileLogger' );
	  		 $logger->info( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written singleton_info entry in log' );
	  }
	  
	  /**
	   * @test
	   */
	  public function singleton_warn() {

	  		 $entry = 'singleton_warn ' . $this->timestamp;

	  		 $logger = LogFactory::getLogger( 'FileLogger' );
	  		 $logger->warn( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written singleton_warn entry in log' );
	  }
	  
	  /**
	   * @test
	   */
	  public function singleton_error() {

	  		 $entry = 'singleton_error ' . $this->timestamp;

	  		 $logger = LogFactory::getLogger( 'FileLogger' );
	  		 $logger->error( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written singleton_error entry in log' );
	  }

	  /**
	   * @test
	   */
	  public function singleton_debug() {

	  		 $entry = 'singleton_debug ' . $this->timestamp;

	  		 $logger = LogFactory::getLogger( 'FileLogger' );
	  		 $logger->debug( $entry );

	  		 PHPUnit_Framework_Assert::assertTrue( $this->exists( $entry ), 'Failed to find written singleton_debug entry in log' );
	  }

	  /**
	   * Unit test helper
	   * 
	   * @param string $text The text to search for in the log file
	   */
	  private function exists( $text ) {

	  		 $logDirectory = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'logs';

	  	     if( !file_exists( $logDirectory ) )  	      	
	  	      	 if( !mkdir( $logDirectory ) )
	  	      	   	 throw new FrameworkException( 'Logger component requires non-existent \'logs/\' directory at \'' . $logDirectory . '\'. An attempt to create it failed.' );

	  	     if( !is_writable( $logDirectory ) )
	  	     	 throw new FrameworkException( 'Logging directory is not writable. The PHP process requires write access to this directory.' );

	  	     $filename = $logDirectory . DIRECTORY_SEPARATOR . 'agilephp_' . date( "m-d-y" ) . '.log';
	  		 $data = file_get_contents( $filename );

	  		 return preg_match( '/' . $text . '/', $data ) ? true : false;
	  }
}
?>