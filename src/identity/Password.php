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
 * AgilePHP interceptor responsible for encrypting passwords using the
 * AgilePHP Crypto component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * <code>
 * #@Password
 * public function setPassword( $myPassword) {
 * 
 * 		  $this->password = $myPassword;
 * }
 * </code>
 * 
 * <code>
 * #@Password( parameter = 1) // Hashes the second method parameter
 * public function updateUser( $username, $password) {
 * 
 * 		  // Update the user account
 * }
 * </code>
 */

#@Interceptor
class Password {

	  /**
	   * @var Integer Optional parameter index value. Use to apply interception
	   * 	  logic to a specific parameter in a method that takes multiple arguments.
	   */
	  public $parameter;

	  /**
	   * Hashes the intercepted parameter using the algorithm configured for the Crypto component.
	   * 
	   * @param InvocationContext $ic The intercepted invocation context
	   * @return mixed The InvocationContext if the call has been altered, void otherwise
	   * @throws InterceptionException if a specified parameter index is out of bounds
	   */
	  #@AroundInvoke
	  public function hash(InvocationContext $ic) {

	  		 if(!$ic->getParameters())
	  		 	throw new InterceptionException('#@Password::encrypt Requires a method which accepts at least one parameter.');

		  	 // Dont encrypt passwords coming from ORM 'find' operation.
	  		 $callee = $ic->getCallee();
	  		 $pieces = explode(DIRECTORY_SEPARATOR, $callee['file']);
	  		 $className = str_replace('.php', '', array_pop($pieces));

	  		 if(preg_match('/^(orm.*)|(.*dialect)$/i', $className))
	  		    return $ic->proceed();

	  		 // Hash the parameter
	  		 $crypto = new Crypto();
	  		 $params = $ic->getParameters();

	  		 $logMessage = '#@Password::hash ' . $callee['class'] . '::' . $ic->getMethod() . ' password hased using ' . $crypto->getAlgorithm();

	  		 if($this->parameter) {

	  		 	if(!array_key_exists($this->parameter, $params))
	  		 	   throw new InterceptionException('#@Password::parameter index out of bounds');

	  		 	$params[$this->parameter] = $crypto->getDigest($params[$this->parameter]);
	  		 	$ic->setParameters($params);

	  		 	Log::debug($logMessage);

	  		 	return $ic->proceed();
	  		 }

			 $ic->setParameters(array($crypto->getDigest($params[0])));

			 Log::debug($logMessage);

			 return $ic->proceed();
	  }
}
?>