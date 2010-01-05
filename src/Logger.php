<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp
 */

/**
 * Performs basic logging functionality
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.2a
 */
class Logger {

	  private static $instance;
	  private $debug = false;

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Factory method that creates an instance of the configured Logger. If
	   * a logger has not been configured in agilephp.xml, an instance of
	   * 'FileLogger' is returned. 
	   * 
	   * @return An instance of the configured logger in agilephp.xml. Default is 'FileLogger'.
	   * @static
	   */
	  public static function getInstance() {

	  		 if( self::$instance == null )
				 self::$instance = new self;

	  		 return self::$instance;
	  }

	  /**
	   * Used by AgilePHP main framework class to automatically load Logger based on agilephp.xml
	   * configuration.
	   * 
	   * @param SimpleXMLElement $config The SimpleXMLElement containing the agilephp.xml Logger configuration 
	   * @return void
	   */
	  public function setConfig( SimpleXMLElement $config ) {

			 if( $config->attributes()->level == 'debug' )
			  	 $this->debug = true;
	  }

	  /**
	   * Writes a 'debug' log level entry.
	   * 
	   * @param String $message The debug message to log
	   * @return void
	   */
	  public function debug( $message ) {

	  	 	 if( $this->debug || AgilePHP::getFramework()->isInDebugMode() )
	  		 	 $this->write( $message, 'DEBUG' );
	  }

	  /**
	   * Writes a 'warn' log level entry.
	   * 
	   * @param String $message The warning message to log
	   * @return void
	   */
	  public function warn( $message ) {

	  		 $this->write( $message, 'WARN' );
	  }

	  /**
	   * Writes an 'info' log level entry.
	   * 
	   * @param String $message The informative message to log
	   * @return void
	   */
	  public function info( $message ) {

	  		 $this->write( $message, 'INFO' );
	  }

	  /**
	   * Writes an 'error' log level entry.
	   * 
	   * @param String $message The error message to log.
	   * @return void
	   */
	  public function error( $message ) {

	  		 $this->write( $message, 'ERROR' );
	  }

	  /**
	   * Writes a log entry.
	   * 
	   * @param String $message The message to log
	   * @param String $level The 'log level' (warn|info|error|debug)
	   * @return void
	   */
	  private function write( $message, $level ) {

	  	      $header = '[' . $level . ']  ' . $_SERVER["REMOTE_ADDR"] . '  ' . date( "m-d-y g:i:sa", strtotime( 'now' ) ) .
	  	      	  			     '  ' . $_SERVER["REQUEST_URI"];

	  	      $logDirectory = AgilePHP::getFramework()->getWebRoot() . '/logs';

	  	      if( !file_exists( $logDirectory ) )  	      	
	  	      	  if( !mkdir( $logDirectory ) )
	  	      	   	  throw new AgilePHP_Exception( 'Logger component requires non-existent \'logs/\' directory at \'' . $logDirectory . '\'. An attempt to create it failed.' );

	  	      if( !is_writable( $logDirectory ) )
	  	      	  throw new AgilePHP_Exception( 'Logging directory is not writable. The PHP process requires write access to this directory.' );

	  	      $filename = $logDirectory . '/agilephp_' . date( "m-d-y" ) . '.log';

	  	      if( is_object( $message ) || is_array( $message ) )
	  	      	  $message = print_r( $message, true );

	  	      $h = fopen(  $filename, 'a+' );
	  	      fputs( $h, $header . '    ' . $message . PHP_EOL );
	  	      fclose( $h );
	  }
}
?>