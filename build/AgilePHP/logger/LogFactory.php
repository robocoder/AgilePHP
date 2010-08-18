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
 * Factory responsible for returning a LogProvider implementation
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.logger
 */
abstract class LogFactory {

         private static $level;
		 private static $logger;

		 /**
		  * Logging level accessor
		  *
		  * @return string The logging level
		  */
		 public static function getLevel() {

		        return self::$level;
		 }

		 /**
		  * Returns a LogProvider singleton instance
		  */
	     public static function getLogger($logger = null) {

	     		if(self::$logger == null) {

	     		   $xml = AgilePHP::getConfiguration();
			       if($xml && $xml->logger) {

			     	  $level = (string)$xml->logger->attributes()->level;
			     	  self::$level = ($level) ? $level : 'info';

			     	  $provider = (string)$xml->logger->attributes()->provider;
			     	  $provider = ($provider) ? $provider : 'FileLogger';

			     	  // Try to load the specified Logger from the framework/logger directory
			     	  $path = AgilePHP::getFrameworkRoot() . DIRECTORY_SEPARATOR . 'logger' .
			     	              DIRECTORY_SEPARATOR . 'FileLogger.php';
			     	  if(file_exists($path)) require_once $path;
			       }
			       else {

			     	  self::$level = 'info';
			     	  $provider = 'FileLogger';

				  	  require_once AgilePHP::getFrameworkRoot() .
				  						DIRECTORY_SEPARATOR . 'logger' . DIRECTORY_SEPARATOR . 'FileLogger.php';
			       }

			       // Logger type specifically requested
			       self::$logger = ($logger == null) ? new $provider : new $logger;
	     		}

	     		return self::$logger;
	     }

	     /**
	      * Returns a new LogProvider with each call
	      *
	      * @param string $logger The name of the LogProvider implementation to create
	      * @return LogProvider A new instance of the requested LogProvider
	      */
	     public static function createLogger($logger) {

				return new $logger;
	     }
}
?>