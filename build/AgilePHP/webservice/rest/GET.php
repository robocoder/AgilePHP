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
 * @package com.makeabyte.agilephp.webservice.rest
 */

/**
 * Responsible for processing REST GET requests
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * class MyRestAPI {
 * 
 * #@GET
 * public function getObject() {
 * 
 * 		  // This is invoked only when the resource is requested using HTTP GET request method.
 * }
 * }
 * </code>
 */
#@Interceptor
class GET {

	  /**
	   * Liason between REST client and service to handle data transformations and providing
	   * appropriate "200 OK" HTTP status code header.
	   * 
	   * @param InvocationContext $ic The intercepted call state
	   * @return void
	   */
	  #@AroundInvoke
	  public function invoke(InvocationContext $ic) {

	  	     $callee = $ic->getCallee();
	  		 $class = $callee['class'];
	  		 $target = $ic->getTarget();
	  		 $method = $ic->getMethod();
	  		 $parameters = $ic->getParameters();

	  		 $return = ($parameters) ? call_user_func_array(array($target, $method), $parameters):
	  		  				call_user_func(array($target, $method));

	  		 $negotiation = RestUtil::negotiate($class, $ic->getMethod());
			 $ProduceMime = $negotiation['ProduceMime'];

			 // Format the return value according to the negotiated mime type and exit the application.
	  		 $out = RestUtil::serverTransform($return, $ProduceMime);
	  		 header('HTTP/1.1 200 OK');
	  		 die($out);
	  }
}
?>