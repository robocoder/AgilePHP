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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Prompts the user for authentication using HTTP basic authentication.
 * Authentication is performed using the AgilePHP Identity component
 * by default, or a custom authenticator method inside the calling
 * class can be specified.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * <code>
 * #@BasicAuthentication
 * public function basicAuth() { }
 * </code>
 *
 * <code>
 * #@BasicAuthentication(realm = 'mydomain.com')
 * public function basicAuth() { }
 * </code>
 *
 * <code>
 * #@BasicAuthentication(authenticator = 'customMethodAuthenticatorInMyCallingClass')
 * public function basicAuth() { }
 * </code>
 *
 * <code>
 * #@BasicAuthentication(authenticator = 'customAuthenticator', realm = 'mydomain.com')
 * public function basicAuth() { }
 * </code>
 */
#@Interceptor
class BasicAuthentication {

	  /**
	   *  @var string An optional realm. Defaults to the HTTP HOST header.
	   *  <code>
	   *  Example:
	   *  #@BasicAuthentication(realm = 'mydomain.com')
	   *  </code>
	   */
	  public $realm;

	  /**
	   *  @var string An optional method name in the callee that will perform
	   *  			  authentication logic. The custom authenticator should
	   *  			  return true if authentication was successful, or false
	   *  			  for anything else. The interceptor will handle throwing
	   *  			  an AccessDeniedException if false is returned.
	   *  <code>
	   *  Example:
	   *  #@BasicAuthentication(authenticator = 'myAuthenticator')
	   *  </code>
	   */
	  public $authenticator;

	  /**
	   * Prompts the user for HTTP basic authentication.
	   *
	   * @param InvocationContext $ic The context of the intercepted call
	   * @return InvocationContext if the authentication was successful.
	   * @throws AccessDeniedException
	   */
	  #@AroundInvoke
	  public function prompt(InvocationContext $ic) {

	  		 if(isset($_SERVER['PHP_AUTH_USER'])) {

	  		 	 if($this->authenticator) {

	  		 	 	 $callee = $ic->getCallee();
	  		 	 	 $object = $callee['class'];
		  	     	 $authenticator = $this->authenticator;

		  	     	 // Static authenticator
		  	     	 if(preg_match('/::/' , $authenticator)) {

		  	     	    $pieces = explode('::', $authenticator);
		  	     	    $class = array_shift($pieces);
		  	     	    $method = array_shift($pieces);

		  	     	    if($class::$method($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
		  	     	       return $ic->proceed();

		  	     	    header('HTTP/1.0 401 Unauthorized');
		  	     	    throw new AccessDeniedException('Invalid username/password');
		  	     	 }

		  	     	 // Use authenticator method defined inside of the intercepted target class
		  	     	 if($object->$authenticator($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
		  	     	 	return $ic->proceed();

		  	     	 header('HTTP/1.0 401 Unauthorized');
		  	     	 throw new AccessDeniedException('Invalid username/password');
	  		 	 }

	  		 	 if(Identity::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
	  		 	 	return $ic->proceed();

	  		 	 header('HTTP/1.0 401 Unauthorized');
	  		 	 throw new AccessDeniedException('Invalid username/password');
	  		 }

	  		 $realm = ($this->realm == null) ? $_SERVER['HTTP_HOST'] : $this->realm;
	  		 header('HTTP/1.0 401 Unauthorized');
	  		 header('WWW-Authenticate: Basic realm=' . $realm);
		     throw new AccessDeniedException('Unauthorized');
	  }
}
?>