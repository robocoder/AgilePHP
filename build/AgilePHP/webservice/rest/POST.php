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
 * Responsible for processing POST (create) requests for a REST resource.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * class MyClassRemote {
 * 
 * #@POST
 * #@Path(resource = '/{id}')
 * public function createObject($id) {
 * 
 * 		 // This is invoked only when the resource is requested using HTTP POST request method.
  * }
 * }
 * </code>
 */
#@Interceptor
class POST {

	  /**
	   * Liason between REST client and service to handle data transformations and providing
	   * appropriate "201 Created" HTTP status code header. Missing content-type header result
	   * in a 406 Not Acceptable.
	   * 
	   * @param InvocationContext $ic The intercepted call state
	   * @return void
	   */
	  #@AroundInvoke
	  public function process(InvocationContext $ic) {

	  		 $callee = $ic->getCallee();
			 $class = $callee['class'];

			 // Get the negotiated mime types used to format the request and response data 
			 $negotiation = RestUtil::negotiate($class, $ic->getMethod());
			 $ProduceMime = $negotiation['ProduceMime'];
			 $ConsumeMime = $negotiation['ConsumeMime'];

	  		 // Read the PUT data
	  		 $data = trim(file_get_contents('php://input'));

	  		 // Transform data if the REST service resource has a #@ConsumeMime annotation
	  		 if($ConsumeMime) $data = RestUtil::consumeTransform($data, $ConsumeMime);

	  		 // Add the data to the parameters passed into the intercepted REST resource action method
	  		 $params = $ic->getParameters();
	  		 array_push($params, $data);

	  		 // Execute the REST service resource and store the return value
	  		 $return = call_user_func_array(array($ic->getTarget(), $ic->getMethod()), $params); 

	  		 // Format the return value according to the negotiated mime type and exit the application.
	  		 $out = RestUtil::serverTransform($return, $ProduceMime);
	  		 header('HTTP/1.1 201 Created');
	  		 die($out);
	  }
}
?>