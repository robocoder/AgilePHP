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
 * Responsible for system based logging. SYSLOG on unix or NT Event Log in windows.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.logger
 */
class SysLogger implements LogProvider {

	  private $log;

	  /**
	   * Opens the syslog LOG_USER facility
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 openlog(AgilePHP::getAppName(), LOG_PID | LOG_PERROR, LOG_USER);
  	  }

	  /**
	   * Writes a 'debug' log level entry.
	   * 
	   * @param String $message The debug message to log
	   * @return void
	   * @static
	   */
	  public function debug($message) {

  		 	 $this->write($message, LOG_DEBUG);
	  }

	  /**
	   * Writes a 'warn' log level entry.
	   * 
	   * @param String $message The warning message to log
	   * @return void
	   */
	  public function warn($message) {

	  		 $this->write($message, LOG_WARNING);
	  }

	  /**
	   * Writes an 'info' log level entry.
	   * 
	   * @param String $message The informative message to log
	   * @return void
	   */
	  public function info($message) {

	  		 $this->write($message, LOG_INFO);
	  }

	  /**
	   * Writes an 'error' log level entry.
	   * 
	   * @param String $message The error message to log.
	   * @return void
	   * @static
	   */
	  public function error($message) {

	  		 $this->write($message, LOG_ERR);
	  }

	  /**
	   * Write the log entry
	   * 
	   * @param string $message The log entry message
	   * @return void
	   */
	  private function write($message, $level) {

	  		  $requestURI = (isset($_SERVER['REQUEST_URI' ]) ? $_SERVER['REQUEST_URI'] : '/');
	  	      $header = '[' . $level . ']  ' . AgilePHP::getAppName() . '  ' . date("m-d-y g:i:sa", strtotime('now')) . '  ' . $requestURI;

	  		  if(is_object($message) || is_array($message))
	  	      	  $message = print_r($message, true);

	  	      syslog($level, $header . "\t" . $message . PHP_EOL); 
	  }
}
?>