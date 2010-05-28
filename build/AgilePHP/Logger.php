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
 * Responsible for basic disk logging
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Logger {

	  private static $instance;
	  private static $level;

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Used by AgilePHP main framework class to automatically load Logger based on agilephp.xml
	   * configuration.
	   * 
	   * @param string $level The logging level (INFO|WARN|ERROR|DEBUG) 
	   * @return void
	   * @static
	   */
	  public static function setLevel( $level) {

		  	  self::$level = strtolower( $level );
	  }

	  /**
	   * Writes a 'debug' log level entry.
	   * 
	   * @param String $message The debug message to log
	   * @return void
	   * @static
	   */
	  public static function debug( $message ) {

	  	 	 if( self::$level == 'debug' || AgilePHP::getFramework()->isInDebugMode() )
	  		 	 self::write( $message, 'DEBUG' );
	  }

	  /**
	   * Writes a 'warn' log level entry.
	   * 
	   * @param String $message The warning message to log
	   * @return void
	   * @static
	   */
	  public static function warn( $message ) {

	  		 self::write( $message, 'WARN' );
	  }

	  /**
	   * Writes an 'info' log level entry.
	   * 
	   * @param String $message The informative message to log
	   * @return void
	   * @static
	   */
	  public static function info( $message ) {

	  		 self::write( $message, 'INFO' );
	  }

	  /**
	   * Writes an 'error' log level entry.
	   * 
	   * @param String $message The error message to log.
	   * @return void
	   * @static
	   */
	  public static function error( $message ) {

	  		 self::write( $message, 'ERROR' );
	  }

	  /**
	   * Writes a log entry.
	   * 
	   * @param String $message The message to log
	   * @param String $level The 'log level' (warn|info|error|debug)
	   * @return void
	   * @static
	   */
	  private static function write( $message, $level ) {

	  		  $address = (isset( $_SERVER['REMOTE_ADDR'] )) ? $_SERVER['REMOTE_ADDR'] : 'localhost';

	  		  $requestURI = (isset( $_SERVER['REQUEST_URI' ] ) ? $_SERVER['REQUEST_URI'] : '/' );

	  	      $header = '[' . $level . ']  ' . $address . '  ' . date( "m-d-y g:i:sa", strtotime( 'now' ) ) .
	  	      	  			     '  ' . $requestURI;

	  	      $logDirectory = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'logs';

	  	      if( !file_exists( $logDirectory ) )  	      	
	  	      	  if( !mkdir( $logDirectory ) )
	  	      	   	  throw new AgilePHP_Exception( 'Logger component requires non-existent \'logs/\' directory at \'' . $logDirectory . '\'. An attempt to create it failed.' );

	  	      if( !is_writable( $logDirectory ) )
	  	      	  throw new AgilePHP_Exception( 'Logging directory is not writable. The PHP process requires write access to this directory.' );

	  	      $filename = $logDirectory . DIRECTORY_SEPARATOR . 'agilephp_' . date( "m-d-y" ) . '.log';

	  	      if( is_object( $message ) || is_array( $message ) )
	  	      	  $message = print_r( $message, true );

	  	      $h = fopen(  $filename, 'a+' );
	  	      fputs( $h, $header . '    ' . $message . PHP_EOL );
	  	      fclose( $h );
	  }
}
?>