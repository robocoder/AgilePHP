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
 * AgilePHP interceptor responsible for throwing an AccessDeniedException if
 * the current logged in user (represented by the state of the Identity component)
 * does not contain the specified role.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * <code>
 * #@Restrict(role = 'admin')
 * public function methodThatRequiresCertainRole() {
 * 
 * 		  // Some logic that requires the current logged in user
 * 		  // to be a member of the 'admin' role.
 * }
 * </code>
 * 
 * <code>
 * #@Restrict(role = 'admin', message = 'Your not allowed here!')
 * public function methodThatRequiresCertainRole() {
 * 
 * 		  // Some logic that requires the current logged in user
 * 		  // to be a member of the 'admin' role and will use the 
 * 		  // message 'Your not allowed here!' in the AccessDeniedException
 * }
 * </code>
 * 
 * <code>
 * #@Restrict(roles = { 'admin', 'member' }, message = 'Your not allowed here!')
 * public function methodThatRequiresCertainRole() {
 * 
 * 		  // Some logic that requires the current logged in user
 * 		  // to be a member of one of the specified roles in the roles array
 * }
 * </code>
 * 
 * <code>
 * #@Restrict(roles = { 'admin', 'member' }, message = 'Your not allowed here!')
 * public function methodThatRequiresCertainRole() {
 * 
 * 		  // Some logic that requires the current logged in user
 * 		  // to be a member of one of the specified roles in the array argument.
 * 		  // If not a member, use the message parameter in the exception message.
 * }
 * </code> 
 */

#@Interceptor
class Restrict {

	  /**
	   * Restrict annotation argument containing the name of the required role.
	   *  
	   * @var String The required role name
	   */
	  public $role;
	  
	  /**
	   * Restrict annotation argument containing an array of required role names.
	   * 
	   * @var Array An array of required role names
	   */
	  public $roles;

	  /**
	   * Restrict annotation optional argument containing the message to display
	   * if the current identity does not contain any of the required roles.
	   *  
	   * @var String Optional message used in AccessDeniedException error message
	   */
	  public $message;

	  #@AroundInvoke
	  public function requireRole(InvocationContext $ic) {

	  		 $message = $ic->getInterceptor()->message ? $ic->getInterceptor()->message : 'Access Denied';

	  		 $requiredRole = $ic->getInterceptor()->role;
	  	     if(Identity::hasRole(new Role($requiredRole)))
	  	     	 return $ic->proceed();

	  	     $roles = $ic->getInterceptor()->roles;
	  	     if(is_array($roles)) {

	  	     	 foreach($roles as $role)
	  	     	 	if(Identity::hasRole(new Role($role)))
	  	     	 		return $ic->proceed();
	  	     }

	  	     $this->audit($message, $ic);
	  }

	  /**
	   * Writes an entry in the log file for security auditing.
	   * 
	   * @param String $message Custom error message as defined in the annotation defintion
	   * @param InvocationContext $ic The InvocationContext which contains the current call state
	   * @return void
	   */
	  private function audit($message, $ic) {

	  		  Log::error('#@Restrict::audit Access Denied ' . print_r(Identity::getModel(), true));
	  		  throw new AccessDeniedException($message);
	  }
}
?>