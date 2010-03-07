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
 * AgilePHP interceptor responsible for populating class properties with
 * HTTP POST variables (gotten from RequestScope).
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception.interceptors
 * @version 0.1a
 * <code>
 * class MyFormProcessor {
 * 
 * #@RequestParam( name = 'email' )
 * public $email;
 * 
 * public function showEmail() {
 * 
 * 		  // Displays the email address entered in <input name="email"/>
 * 		  echo $this->email;
 * }
 * 
 * }
 * </code>
 */

#@Interceptor
class RequestParam {

	  /**
	   * The HTML input name to grab the value from 
	   *  
	   * @var String The HTML input name to grab the value from
	   */
	  public $name;

	  /**
	   * Boolean flag indicating whether or not to sanitize the input. (Default is to sanitize all input)
	   * 
	   * @var bool True to sanitize the form input, false to grab the raw data. (Default is sanitize)
	   */
	  public $sanitize = true;

	  /**
	   * Sets the annotated property value with the HTML input value
	   * 
	   * @param InvocationContext $ic The InvocationContext of the intercepted call
	   * @return void
	   */
	  #@AroundInvoke
	  public function setFormValue( InvocationContext $ic ) {

	  		 if( $_SERVER['REQUEST_METHOD'] == 'POST' )
			 	return ($ic->getInterceptor()->sanitize) ? Scope::getRequestScope()->getSanitized( $ic->getInterceptor()->name ) :
			 				Scope::getRequestScope()->get( $ic->getInterceptor()->name );
	  }
}
?>