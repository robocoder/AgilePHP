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
 * Tells the REST request processing interceptor (#@GET|#@POST|#@PUT|#@DELETE) to
 * transform received HTTP data into a PHP data type before passing into the REST service
 * method. Conversions occur based on the specified #@ConsumeMime::type as follows:
 * 
 * 1) application/xml       = SimpleXMLElement
 * 2) application/json      = json_decoded data
 * 3) application/x-yaml    = yaml_parsed data
 * 4) application/xhtml+xml = raw data passed from the client
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 * <code>
 * #@ConsumeMime(type = 'application/xml')
 * public function updateObject($id, $data) {
 * 
 * 	     // $data is a SimpleXMLElement
 * }
 * }
 * </code>
 */
class ConsumeMime {

	  /**
	   * A supported AgilePHP REST mime type. The following mime types are supported:
	   * 
	   * 1) application/xml
	   * 2) application/json
	   * 3) application/x-yaml
	   * 4) application/xhtml+xml
	   * 
	   * @var string $type A supported mime type used to consume and transform client data
	   */
	  public $type;
}
?>