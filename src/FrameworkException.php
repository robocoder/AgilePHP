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
 * Base AgilePHP exception class
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @throws FrameworkException
 */
class FrameworkException extends Exception {

	  /**
	   * Creates a new instance of FrameworkException.
	   *
	   * @param String $message The exception message
	   * @param Integer $code Optional error code.
	   * @param String $file Optional file path to the exception
	   * @param Integer $line The line number the exception / error occurred
	   * @return void
	   */
	  public function __construct( $message, $code = null, $file = null, $line = null ) {

			 $this->message = $message;
	  		 if( $code ) $this->code = $code;
	  		 $this->file = ($file == null) ? __FILE__ : $file;
  		 	 $this->line = ($line == null ) ? __LINE__ : $line;
	  		 $this->trace = debug_backtrace();
	  }
}
?>