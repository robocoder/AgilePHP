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
 * @package com.makeabyte.agilephp.test.classes.interceptors
 */

/**
 * Simple interceptor demostrating how to create and use a simple
 * interceptor in AgilePHP.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes.interceptors
 */

#@Interceptor
class TestInterceptor {

	  /**
	   * An example using the InvocationContext getMethod in logic criteria
	   */
	  #@AroundInvoke
	  public function property1Setter(InvocationContext $ic) {

	  		 $method = $ic->getMethod();

	  		 if($method == 'setProperty1' || $method == 'setProperty2' || $method = 'test') {

	  		 	 // these are the original parameters passed into setProperty1
	  		 	 $params = $ic->getParameters();

	  		 	 // here we alter the parameter value and update InvocationContext
	  		 	 $params[0] = 'intercepted value';
	  		 	 $ic->setParameters($params);

	  		 	 // return the InvocationContext to the proxied class for invocation
	  		 	 return $ic->proceed();
	  		 }

	  		 // Note: The interceptor chain stops here since $ic->proceed is not being returned!
	  }
}
?>