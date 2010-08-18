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
 * @package com.makeabyte.agilephp.scope
 */

/**
 * Responsible for managing the state of a class which is annotated with
 * #@Stateful.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 * <code>
 * #@Stateful
 * class MyStatefulClass {
 * 
 * 		 private $foo;		// retains value between page hits
 * 
 * 		 public function __construct() { }
 * 
 * 		 // ......
 * }
 * </code>
 */
#@Interceptor
class Stateful {

	  /**
	   * Loads the class from the current session if it exists, otherwise a new
	   * instance is created and stored in the current session.
	   * 
	   * @param InvocationContext $ic The context of the intercepted call
	   * @return InvocationContext if the authentication was successful.
	   * @throws FrameworkException
	   */
	  #@AroundInvoke
	  public function restore(InvocationContext $ic) {

	  		 $reflector = new ReflectionClass($ic->getTarget());
	  		 $className = $reflector->getName();

	  		 $session = Scope::getSessionScope();
	  		 $instance = $session->get('STATEFUL_' . $className);

	  		 if($instance) {

	  		 	 $ic->setTarget($instance);
	  		 	 return $ic->proceed();
	  		 }
	  }

	  #@AfterInvoke
	  public function persist(InvocationContext $ic) {

	  		 $reflector = new ReflectionClass($ic->getTarget());
	  		 $className = $reflector->getName();

	  		 $session = Scope::getSessionScope();
	  		 $session->set('STATEFUL_' . $className, $ic->getTarget());
	  }
}
?>