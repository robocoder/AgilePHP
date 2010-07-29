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
 * @package com.makeabyte.agilephp.webservice.remoting
 */

/**
 * Handles all remoting exceptions. Output is returned in JSON format
 * with an application/json HTTP header.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.remoting
 * @throws RemotingException
 */
class RemotingException extends FrameworkException { 

	  /*
	   * Public context fields reduce the chance that AJAXRenderer will
	   * use reflection method setAccessible which requires PHP 5.3+
	   */ 
	  public $code;
	  public $message;
	  public $file;
	  public $trace;
	  public $line;
	  public $_class = 'RemotingException';

	  /**
	   * Deliver remoting exceptions in JSON format and halt execution.
	   * 
	   * @param String $message The exception message
	   * @return void
	   */
	  public function __construct( $message ) {

			 $this->message = $message;
			 $this->trace = parent::getTraceAsString();
	  		 $renderer = MVC::createRenderer('AJAXRenderer');
	  		 $renderer->render($this);
	  		 exit;
	  }
}
?>