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
 * @package com.makeabyte.agilephp.form
 */

/**
 * AgilePHP interceptor responsible for populating class properties with
 * HTTP POST variables (gotten from RequestScope).
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.form
 * <code>
 * class MyFormProcessor {
 * 
 * // Defaults to the name of the property/field
 * #@RequestParam
 * public $fname;
 * 
 * // Optionally, we can specify the name of the input element
 * #@RequestParam(name = 'email')
 * public $emailaddress;
 * 
 * // Optionally, we can also specify no sanitation
 * #@RequestParam(name = 'password', sanitize = false)
 * public $plainTextPassword;
 * 
 * public function showEmail() {
 * 
 * 		  // Displays the first name entered in <input name="fname"/>
 * 		  echo $this->fname;
 * 
 * 		  // Displays the email address entered in <input name="email"/>
 * 		  echo $this->emailaddress;
 * 
 * 		  // Displays a plain text password as entered in <input type="password" name="password"/>
 * 		  echo $this->plainTextPassword;
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
	   * @var String Optional HTML input name to grab the value from. Defaults to the name of the annotated property.
	   */
	  public $name;

	  /**
	   * Boolean flag indicating whether or not to sanitize the input. (Default is to sanitize all input)
	   * 
	   * @var bool True to sanitize the form input, false to grab the raw data. (Default is sanitize)
	   */
	  public $sanitize = true;

	  /**
	   * Boolean flag indicating whether or not the field is required. Defaults to false (not required).
	   * 
	   * @var bool True if required, false otherwise.
	   */
	  public $required = false;

	  /**
	   * Sets the annotated property value with the HTML input value
	   * 
	   * @param InvocationContext $ic The InvocationContext of the intercepted call
	   * @return void
	   */
	  #@AroundInvoke
	  public function setFormValue(InvocationContext $ic) {

	  		 if($_SERVER['REQUEST_METHOD'] == 'POST') {

	  		 	$request = Scope::getRequestScope();
	  		 	$name = ($this->name) ? $ic->getInterceptor()->name : $ic->getField();

	  		 	if($this->required && !$request->get($name))
	  		 	   throw new FrameworkException($name . ' is required');

	  		 	return ($ic->getInterceptor()->sanitize) ? $request->getSanitized($name) : $request->get($name);
	  		 }
	  }
}
?>