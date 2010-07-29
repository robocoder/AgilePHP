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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * AgilePHP interceptor responsible for throwing an NotLoggedInException if
 * the current request does not have an authenticated Identity session.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * <code>
 * class MyClass {
 *  
 * #@LoggedIn
 * public function requiresLoginToInvoke($arg) {
 * 
 * 		  // Do something here that requires the user to be logged in
 * }
 * 
 * #@LoggedIn(message = 'My custom exception message')
 * public function requiresLoginToInvoke($arg) {
 * 
 * 		  // Do something here that requires the user to be logged in
 * }
 * 
 * public function someMethod() {
 * 
 * 		  // This can be invoked without being logged in
 * }
 * }
 * </code>
 */

#@Interceptor
class LoggedIn {

	  public $message;

	  #@AroundInvoke
	  public function process(InvocationContext $ic) {

	  		 $message = ($this->message) ? $this->message : 'You must be logged in to view the requested content!';

	  		 if(!Identity::isLoggedIn())
	  		 	throw new NotLoggedInException($message);
	  }
}
?>