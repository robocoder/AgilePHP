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
 * @package com.makeabyte.agilephp.webservice.rest
 */

/**
 * Accepts a valid RFC 2616 response code and throws a PHP Exception containing
 * its RFC 2616 status code and message.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * @throws RestClientException
 * @throws FrameworkException if the specified error code is not
 * 		   a valid RFC 2616 status code.
 */
class RestClientException extends RestServiceException { 

	  /**
	   * Accepts a valid RFC 2616 HTTP status code and thows an Exception
	   * which contains the corresponding code and status message.
	   * 
	   * @param Integer $code The HTTP status code to send.
	   * @return void
	   */
	  public function __construct($code) {

	  		 if(!array_key_exists($code, $this->codes))
	  		 	 throw new FrameworkException('Invalid HTTP Response code \'' . $code . '\'.');

			 $this->code = $code;
			 $this->message = $code . ' ' . $this->codes[$code];
	  }
}
?>