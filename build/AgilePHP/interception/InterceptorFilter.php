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
 * @package com.makeabyte.agilephp.interception
 */

/**
 * Filters the specified class for interceptor annotations. If any interceptor
 * annotations are found, an InterceptorProxy instance is created for the specified
 * class and each interceptor is loaded in the order they were implemented.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception
 */
class InterceptorFilter {

	  public function __construct($class) {

	         AnnotationParser::parse($class);

	  		 // Process class level annotations
			 $classAnnotations = Annotation::getClassAsArray($class);
	  	     if(count($classAnnotations)) {

                foreach($classAnnotations as $annotation) {

			  	  		$annote = new AnnotatedClass($annotation);
				   	   	if($annote->hasAnnotation('Interceptor')) {

				   	   	   $interception = new Interception($class, null, null, $annotation);
				   	   	   $interception->createInterceptorProxy($interception->createInterceptedTarget());

				   	   	   AgilePHP::addInterception($interception);

				   	   	   if($interception->isStatic()) $class::__initstatic();
				   	   	}
				 }
	  	     }

	  	     // Process property/field level annotations
	  	     $annotatedProperties = Annotation::getPropertiesAsArray($class);
		 	 if(count($annotatedProperties)) {

				foreach($annotatedProperties as $fieldName => $fieldAnnotation) {

				     foreach($fieldAnnotation as $annotation) {

				  	   		 $annote = new AnnotatedClass($annotation);
					   	   	 if($annote->hasAnnotation('Interceptor')) {

					   	   	  	$interception = new Interception($class, null, $fieldName, $annotation);
					   	   	  	$interception->createInterceptorProxy($interception->createInterceptedTarget());

					   	   	  	AgilePHP::addInterception($interception);

					   	   	  	if($interception->isStatic()) $class::__initstatic();
					   	   	  }
					 }
				 }
	  	     }

	  	     // Process method level annotations
			 $annotatedMethods = Annotation::getMethodsAsArray($class);
		 	 if(count($annotatedMethods)) {

				foreach($annotatedMethods as $methodName => $methodAnnotation) {

				     foreach($methodAnnotation as $annotation) {

				  	   		 $annote = new AnnotatedClass($annotation);
					   	   	 if($annote->hasAnnotation('Interceptor')) {

					   	   	  	$interception = new Interception($class, $methodName, null, $annotation);
					   	   	  	$interception->createInterceptorProxy($interception->createInterceptedTarget());

					   	   	  	AgilePHP::addInterception($interception);

					   	   	  	if($interception->isStatic()) $class::__initstatic();
					   	   	 }
					 }
				 }
	  	     }
	  }
}
?>