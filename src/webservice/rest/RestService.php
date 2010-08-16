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
 * Interceptor responsible for capturing REST resource requests from the MVC
 * component and routing them to the proper service method and resource based
 * on URI composition.
 *
 * NOTE: It seems that ironically enough, REST does not really fit the MVC paradigm,
 * since MVC requests use /service-or-controller/action-also-known-as-a-verb whereas
 * REST requests use /service-or-controller/nouns-only.
 * The #@RestService acts as a router or front controller to ensure requests to REST
 * resources stay "restful".
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * #@RestService
 * class MyRestAPI {
 *
 * 		 // #@RestService interceptor will route the HTTP request to the proper resource
 * 	     // defined in the service based on #@Path::resource declarations.
 * }
 * </code>
 */
#@Interceptor
class RestService {

	  /**
	   * Intercepts a REST web service before the MVC executes an
	   * action method. This allows introspection of the REST web service
	   * as its instantiated so this class can perform routing of the
	   * REST request to the proper action method.
	   *
	   * @param InvocationContext $ic The call state of the interception
	   * @return void
	   * @throws RestException
	   */
	  #@AroundInvoke
	  public function intercept(InvocationContext $ic) {

	  		 // Parse the REST service class name, resource, and parameters from the HTTP URL
	  		 $service = preg_replace('/_Intercepted/', '', get_class($ic->getTarget()));
	  		 $action = MVC::getAction();
	  		 $parameters = MVC::getParameters();
	  		 array_unshift($parameters, $action);
	  		 $request = '/' . implode('/', $parameters);

	  		 Log::debug('#@RestService::intercept Routing REST service \'' . $service . '\' resource request \'' . $request . '\'.');

			 $annotes = Annotation::getMethodsAsArray($service);
			 foreach($annotes as $method => $annotations) {

			 		  // Process default action method first. It will never have any variables to extract
			 		  if($request == '/index') return $ic->proceed();

			 		  foreach($annotations as $annote) {

			 				   if($annote instanceof Path) {

			 				   	   // All #@Path annotations should have a resource at this point since the default /index
			 				   	   // request has already been processed. Ignore this method.
			 				 	   if(!$annote->resource) continue;

			 				 	   // No variables to extract simply invoke the requested resource
			 				 	   if($annote->resource == $request) {

			 				 	      $ic->setMethod($method);
			 				 	      return $ic->proceed();
			 				 	   }

			 				 	   // Create regex based on the #@Path resource definition
			 				 	   $escapedAnnote = preg_replace('/\//', '\/', $annote->resource);
			 				 	   $regex = '^' . preg_replace('/{.+?}/', '(.+)', $escapedAnnote) . '$';

			 				 	   // Extract variable names and values
			 				 	   preg_match('/' . $regex . '/', $annote->resource, $variables); // Extract variable names from the #@Path::resource definition
			 				 	   preg_match('/' . $regex . '/', $request, $values);	// Extract values from the request URI

			 				 	   if(!isset($values[1])) continue; // No match

			 				 	   // First elements are the text that was matched - remove
			 				 	   array_shift($variables);
			 				 	   array_shift($values);

			 				 	   // Use the extracted variable names and values to construct #@Path and $request matchers. If
			 				 	   // the $method #@Path resource matches the $request and the extracted values match up with
			 				 	   // EL {variable} braces, then route the call to this method.
			 				 	   $req = preg_replace('/\//', '\\\\\0', $request);
			 				 	   $resMatcher = "/^$req";
			 				 	   $pathMatcher = "/^$req";
			 				 	   for($i=0; $i<count($values); $i++) {

			 				 	   		$var = preg_replace('/\{|\}/', '\\\\\0', $variables[$i]); // escpape { and } chars
			 				 	 	    $pathMatcher = str_replace($values[$i], $var, $pathMatcher);
			 				 	 	    $resMatcher = str_replace($var, $values[$i], $resMatcher);
			 				 	   }
			 				 	   $pathMatcher .= '$/';
			 				 	   $resMatcher .= '$/';

			 				 	   // Execute the REST service method if its #@Path resource matches the current request
			 				 	   if(preg_match($pathMatcher, $annote->resource) && preg_match($resMatcher, $request)) {

			 				 	   	   $verb = strtoupper($_SERVER['REQUEST_METHOD']);
			 				 	   	   $hasVerb = false;
			 				 	   	   foreach($annotations as $a)
			 				 	   	   		if($a instanceof $verb) $hasVerb = true;

			 				 	   	   if(!$hasVerb) continue;

			 				 	   	   $ic->setMethod($method);
				 				 	   $ic->setParameters($values);

				 				 	   return $ic->proceed();
			 				   	   }
			 				 }
			 		}
			 }

			 Log::debug('#@RestService::intercept Failed to route \'' . $request . '\' to a \'' . $service . '\' service method.');
			 throw new RestServiceException(404);
	  }
}
?>