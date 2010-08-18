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
 * Helper annotation which the #@RestService interceptor uses to route REST resource
 * requests to the proper service/action method. "Expression Language (EL)" type syntax
 * can be used in the #@Path::type definition to capture one or more values from
 * the HTTP request. The #@Path::type definition also supports RegEx's for complex
 * pattern matching. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * 
 * #@RestService
 * class MyRestAPI {
 * 
 * // example 1
 * #@GET
 * #@Path(resource = '/{id}')
 * public function getObject1($id) {
 * 
 * 	     // request is sent to http://mydomain.com/index.php/MyRestAPI/33
 *       // $id has a value of 33 
 * }
 * 
 * // example 2
 * #@GET
 * #@Path(resource = '/{id}/some/deeper/resource{newId}')
 * public function getObject2($id, $newId) {
 * 
 * 		  // request is sent to http://mydomain.com/index.php/MyRestAPI/33/some/deeper/resource/55
 * 		  // $id has a value of 33 and $newId has a value of 55
 * }
 * 
 * // example 3
 * #@GET
 * #@Path(resource = '/{id}/.*regex$')
 * public function getObject3($id) {
 * 
 * 		  // request is sent to http://mydomain.com/index.php/MyRestAPI/33/some/long/resource/that/you/want/to/match/with/a/regex
 * 		  // $id has a value of 33 and the method gets executed since its regex matches the request
 * }
 * </code>
 */
class Path {

	  /**
	   * The REST resource path which MUST begin with a forward slash (/).
	   *  
	   * @var string $resource The REST resource path to match against requests
	   */
	  public $resource;
}
?>