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
 * Exposes PHP classes via SOAP. This may be replaced in the future with
 * #@WebMethod interceptor. Currently #@WebMethod is just an annotation
 * that causes the method to be included during WSDL generation.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.soap
 * @abstract
 */
abstract class SOAPService extends BaseController {

		 /**
		  * Invokes the requested SOAP method.
		  * 
		  * @return void
		  * @see src/mvc/BaseController#index()
		  */
		 public function index() {

		 		$wsdlMethod = 'wsdl';

		 		$class = new AnnotatedClass($this);

		 		// Provide default targetNamespace in case #@WebService annotation is missing
		 		$targetNamespace = 'http://' . $_SERVER['HTTP_HOST'] . AgilePHP::getRequestBase() . '/' . MVC::getController();
	  		    $annotations = Annotation::getClassAsArray($class->getName());

		  		// Initalize web service configuration from #@WebService annotation if present
	  		 	if(count($annotations)) {

		  		 	foreach($annotations as $annotation) {

		  		 	 	if($annotation instanceof WebService) {

		  		 	 		if($annotation->serviceName)
		  		 	 			$serviceName = $annotation->serviceName;

		  		 	 		if($annotation->targetNamespace)
		  		 	 			$targetNamespace = $annotation->targetNamespace;
	  		 	 		}
		  		 	}
	  		 	}

	  		 	// Get method annotated with WSDL interceptor
	  		 	$methods = $class->getMethods();
	  		 	foreach($methods as $method) {

	  		 		foreach($method->getAnnotations() as $annotes) {

	  		 			foreach($annotes as $annote) {

	  		 				if($annote instanceof WSDL) {

	  		 					$wsdlMethod = $method->getName();
	  		 					break;
	  		 				}
	  		 			}
	  		 		}
	  		 	}

		 		if(AgilePHP::isInDebugMode())
					ini_set('soap.wsdl_cache_enabled', '0');

				$server = new SoapServer($targetNamespace . '/' . $wsdlMethod);
				$server->setClass($class->getName());
				$server->handle();
		 }
}
?>