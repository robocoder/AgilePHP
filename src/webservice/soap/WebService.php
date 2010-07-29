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
 * @package com.makeabyte.agilephp.webservice.soap
 */

require_once 'webservice/soap/SOAPBinding.php';
require_once 'webservice/soap/SOAPService.php';
require_once 'webservice/soap/WebMethod.php';
require_once 'webservice/soap/WSDL.php';

/**
 * Annotation responsible for exposing standard PHP classes via SOAP.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.soap
 * <code>
 * #@WebService(serviceName = 'MyAPIService', targetNameSpace = 'http://www.mydomain.com/index.php/MyAPI')
 * class MyAPI {
 *
 * }
 * </code>
 */
class WebService {

	  /**
	   * @var String The SOAP web service name
	   */
	  public $serviceName;

	  /**
	   * @var String The target namespace of the web service. This is be the location to your web service. (http://api.example.org/index.php/TestAPI)
	   */
	  public $targetNamespace;
}
?>