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
 * @package com.makeabyte.agilephp.logger
 */

/**
 * Responsible for disk based file logging to #projectName#/logs
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.logger
 */
class FileLogger implements LogProvider {

	  private $log;

	  /**
	   * Create a log file handle
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 $logDirectory = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . 'logs';

	  	     if(!file_exists($logDirectory))  	      	
	  	      	 if(!mkdir($logDirectory))
	  	      	   	 throw new FrameworkException('Logger component requires non-existent \'logs/\' directory at \'' . $logDirectory . '\'. An attempt to create it failed.');

	  	     if(!is_writable($logDirectory))
	  	     	 throw new FrameworkException('Logging directory is not writable. The PHP process requires write access to this directory.');

	  	     $filename = $logDirectory . DIRECTORY_SEPARATOR . 'agilephp_' . date("m-d-y") . '.log';
	  	     if(!file_exists($filename)) {

	  	     	 if(!touch($filename))
	  	     	 	 throw new FrameworkException('Unable to create log file at \'' . $filename . '\'.');

	  	     	 @chmod($filename, 0777);
	  	     }

	  		 $this->log = fopen( $filename, 'a+');
  	  }

	  /**
	   * Writes a 'debug' log level entry.
	   * 
	   * @param String $message The debug message to log
	   * @return void
	   * @static
	   */
	  public function debug($message) {

  		 	 $this->write($message, 'DEBUG');
	  }

	  /**
	   * Writes a 'warn' log level entry.
	   * 
	   * @param String $message The warning message to log
	   * @return void
	   */
	  public function warn($message) {

	  		 $this->write($message, 'WARN');
	  }

	  /**
	   * Writes an 'info' log level entry.
	   * 
	   * @param String $message The informative message to log
	   * @return void
	   */
	  public function info($message) {

	  		 $this->write($message, 'INFO');
	  }

	  /**
	   * Writes an 'error' log level entry.
	   * 
	   * @param String $message The error message to log.
	   * @return void
	   * @static
	   */
	  public function error($message) {

	  		 $this->write($message, 'ERROR');
	  }

	  /**
	   * Write the log entry
	   * 
	   * @param string $message The log entry message
	   * @return void
	   */
	  private function write($message, $level) {

	  		  $host = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : AgilePHP::getAppName();
	  		  $requestURI = (isset($_SERVER['REQUEST_URI' ]) ? $_SERVER['REQUEST_URI'] : '/');
	  	      $header = '[' . $level . ']  ' . $host . '  ' . date("m-d-y g:i:sa", strtotime('now')) . '  ' . $requestURI;

	  		  if(is_object($message) || is_array($message))
	  	      	  $message = print_r($message, true);

	  	      fputs($this->log, $header . "\t" . $message . PHP_EOL);
	  }
}
?>