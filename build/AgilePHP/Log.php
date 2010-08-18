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

require_once 'logger/LogProvider.php';
require_once 'logger/LogFactory.php';
require_once 'logger/FileLogger.php';

/**
 * Performs application logging to #projectName#/logs/agilephp_MM-DD-YY.log
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
final class Log {

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Writes a 'debug' log level entry.
	   *
	   * @param String $message The debug message to log
	   * @return void
	   * @static
	   */
	  public static function debug($message) {

	         $logger = LogFactory::getLogger();

	         if(LogFactory::getLevel() == 'debug')
  		 	      $logger->debug($message);
	  }

	  /**
	   * Writes a 'warn' log level entry.
	   *
	   * @param String $message The warning message to log
	   * @return void
	   * @static
	   */
	  public static function warn($message) {

	         $logger = LogFactory::getLogger();

	         if(LogFactory::getLevel() != 'error')
	  		    $logger->warn($message);
	  }

	  /**
	   * Writes an 'info' log level entry.
	   *
	   * @param String $message The informative message to log
	   * @return void
	   * @static
	   */
	  public static function info($message) {

	         $logger = LogFactory::getLogger();
	         $level = LogFactory::getLevel();

	         if($level == 'info' || $level == 'debug')
	  		    $logger->info($message);
	  }

	  /**
	   * Writes an 'error' log level entry.
	   *
	   * @param String $message The error message to log.
	   * @return void
	   * @static
	   */
	  public static function error($message) {

	         $logger = LogFactory::getLogger();
	         $level = LogFactory::getLevel();

	         if($level == 'error' || $level == 'debug')
	  		    $logger->error($message);
	  }
}
?>