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
 * Responsible for processing DELETE requests for a REST resource.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * class MyRestAPI {
 * 
 * #@DELETE
 * public function deleteObject() {
 * 
 * 		 // This is invoked only when the resource is requested using HTTP DELETE request method.
 * }
 * }
 * </code>
 */
#@Interceptor
class DELETE {

	  /**
	   * Liason between REST client and service to handle data transformations and providing
	   * appropriate "204 No Content" HTTP status code header.
	   * 
	   * @param InvocationContext $ic The intercepted call state
	   * @return void
	   */
	  #@AroundInvoke
	  public function process(InvocationContext $ic) {

	  		 // Execute the REST service resource and return 204
	  		 call_user_func_array(array($ic->getTarget(), $ic->getMethod()), $ic->getParameters()); 
	  		 header('HTTP/1.1 204 No Content');
	  		 exit;
	  }
}
?>