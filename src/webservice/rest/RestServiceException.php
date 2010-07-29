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
 * Send a valid RFC 2616 response header to the client and
 * terminate the application. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * @throws RestServiceException
 * @throws FrameworkException if the specified error code is not
 * 		   a valid RFC 2616 status code.
 */
class RestServiceException extends FrameworkException { 

	  protected $codes = array(
             100 => 'Continue',
             101 => 'Switching Protocols',
             200 => 'OK',
             201 => 'Created',
             202 => 'Accepted',
             203 => 'Non-Authoritative Information',
             204 => 'No Content',
             205 => 'Reset Content',
             206 => 'Partial Content',
             300 => 'Multiple Choices',
             301 => 'Moved Permanently',
             302 => 'Found',
             303 => 'See Other',
             304 => 'Not Modified',
             305 => 'Use Proxy',
             306 => '(Unused)',
             307 => 'Temporary Redirect',
             400 => 'Bad Request', 
             401 => 'Unauthorized',
             402 => 'Payment Required',
             403 => 'Forbidden',
             404 => 'Not Found',
             405 => 'Method Not Allowed',
             406 => 'Not Acceptable',
             407 => 'Proxy Authentication Required',
             408 => 'Request Timeout',
             409 => 'Conflict',
             410 => 'Gone',
             411 => 'Length Required',
             412 => 'Precondition Failed',
             413 => 'Request Entity Too Large',
             414 => 'Request-URI Too Long',
             415 => 'Unsupported Media Type',
             416 => 'Requested Range Not Satisfiable',
             417 => 'Expectation Failed',
             500 => 'Internal Server Error',
             501 => 'Not Implemented',
             502 => 'Bad Gateway',
             503 => 'Service Unavailable',
             504 => 'Gateway Timeout',
             505 => 'HTTP Version Not Supported'
     );

	  /**
	   * Accepts a valid RFC 2616 HTTP status code and sends the appropriate
	   * RFC 2616 response header to the client.
	   * 
	   * @param Integer $code The HTTP status code to send.
	   * @return void
	   */
	  public function __construct($code = null) {

	  		 if(!array_key_exists($code, $this->codes))
	  		 	 throw new FrameworkException('Invalid HTTP Response code \'' . $code . '\'.');

	  		 $this->code = $code;
	  		 $this->message = $code . ' ' . $this->codes[$code];
	  		 $this->trace = debug_backtrace();

	  		 header('HTTP/1.1 ' . $code . ' ' . $this->codes[$code]);
	  		 Log::error('RestServiceException ' . $this->message);
	  		 echo $this->message;
	  		 exit;
	  }
}
?>