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
 * Forces the REST method processor (#@GET|#@POST|#@PUT|#@DELETE) to transform
 * data returned by the service to the specified mime type before passing the response
 * back to the client. This annotation also overrides the client HTTP Accept header. If
 * the client does not list the specified #@ProduceMime::type then a "406 Not Acceptable"
 * is returned to the client. The returned service resource data is transformed and sent
 * to the client based on the specified #@ProduceMime::type as follows:
 * 
 * 1) application/xml       = Well formed XML		(expects SimpleXMLElement returned)
 * 2) application/json      = Well formed JSON		(any return type supported)
 * 3) application/x-yaml    = Well formed YAML		(any return type supported)
 * 4) application/xhtml+xml = raw data passed from the service resource
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * #@ProduceMime(type = 'application/xml')
 * public function updateObject($id, $data) {
 * 
 * 		  // code here to process the request
 * 
 * 		  return $response; // $response is a SimpleXMLElement that will be transformed to well formed XML
 * }
 * }
 * </code>
 */
class ProduceMime {

	  /**
	   * A supported AgilePHP REST mime type. The following mime types are supported:
	   * 
	   * 1) application/xml
	   * 2) application/json
	   * 3) application/x-yaml
	   * 4) application/xhtml+xml
	   * 
	   * @var string $type A supported mime type used to produce and transform service return data
	   */
	  public $type;
}
?>