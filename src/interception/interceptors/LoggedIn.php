<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp.interception.interceptors
 */

/**
 * AgilePHP interceptor responsible for throwing an AgilePHP_NotLoggedInException if
 * the current request does not have an authenticated Identity session.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception.interceptors
 * @version 0.1a
 * @example #@LoggedIn
 * @example #@LoggedIn( message = "My custom exception message" )
 */

#@Interceptor
class LoggedIn {

	  public $message;

	  #@AroundInvoke
	  public function process( InvocationContext $ic ) {

	  		 $message = ($this->message) ? $this->message : 'You must be logged in to view the requested content!';

	  		 if( !Identity::getInstance()->isLoggedIn() )
	  		 	 throw new AgilePHP_NotLoggedInException( $message );
	  }
}
?>