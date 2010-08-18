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

/**
 * Annotation which authorizes a method for inclusion during #@WSDL generation.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.soap
 * <code>
 * #@WebService(serviceName = 'MyAPIService', targetNameSpace = 'http://www.mydomain.com/index.php/MyAPI')
 * class MyAPI {
 * 
 * 		 #@WSDL
 * 		 public function wsdl() { 
 * 
 * 				// When this method gets requested the #@WSDL interceptor
 * 				// will reverse engineer this class and output a WSDL
 * 				// file any SOAP client can consume to execute a #@WebMethod.
 * 		 }
 *
 * 		 #@WebMethod
 * 		 public function someMethod() {
 * 
 * 				// I will get included during WSDL generation
 * 		 }
 *
 * 		 public function someMethod2() {
 * 
 * 				// I will not get included during WSDL generation
 * 		 }
 * }
 * </code>
 */
class WebMethod {}
?>