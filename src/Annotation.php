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
 * @package com.makeabyte.agilephp
 */

/**
 * Includes all annotation package dependencies
 */
require_once 'annotation/AnnotationParser.php';
require_once 'annotation/AnnotatedClass.php';
require_once 'annotation/AnnotatedMethod.php';
require_once 'annotation/AnnotatedProperty.php';

/**
 * Static facade for AgilePHP annotation data types.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Annotation {

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Static factory method used to retrieve AnnotatedClass instances. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect
	   * @return void
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getClass($class) {

	         if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_CLASS_' . $class;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	         $c = new AnnotatedClass($class); 
	         if(isset($cacher)) $cacher->set($cacheKey, $c);
	  		 return $c;
	  }

	  /**
	   * Static factory method used to retrieve AnnotatedMethod instances. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect.
	   * @param String $name The method name
	   * @return AnnotatedMethod
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getMethod($class, $method) {

	         if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_METHOD_' . $class . $method;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	         $m = new AnnotatedMethod($class, $method);
	         if(isset($cacher)) $cacher->set($cacheKey, $m);
	  		 return $m;
	  }

	  /**
	   * Static factory method used to retrieve AnnotatedProperty instances. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect
	   * @param String $property The property name
	   * @return AnnotatedProperty
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getProperty($class, $property) {

	         if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_PROPERTY_' . $class . $property;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	         $p = new AnnotatedProperty($class, $property);
	         if(isset($cacher)) $cacher->set($cacheKey, $p);
	         return $p;
	  }

	  /**
	   * Returns true if the specified class contains and class, method, or
	   * property level annotations.
	   *
	   * @param mixed $class The class name or instance to inspect
	   * @return bool True if the class contains any class, method, or property
	   * 		 	   level annotations.
	   * @throws AnnotationException
	   * @static
	   */
	  public static function hasAnnotations($class) {

	  		 $clazz = new AnnotatedClass($class);

	  		 if($clazz->isAnnotated()) return true;

	  		 foreach($clazz->getMethods() as $method)
	  		 		  if($method->isAnnotated()) return true;

	  		 foreach($clazz->getProperties() as $property)
	  		 		  if($property->isAnnotated()) return true;

	  		 return false;
	  }

	  /**
	   * Returns an array of class level annotations for the specified class.
	   * Tries to return a cached set of results first. If no annotations are
	   * found the specified class is then parsed and the new result is returned. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect.
	   * @return array Array of class level annotations
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getClassAsArray($class) {

	        if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_CLASS_ARRAY_' . $class;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	  	     $annotes = AnnotationParser::getClassAnnotationsAsArray($class);
	  	     if(isset($cacher)) $cacher->set($cacheKey, $annotes);

	  	     return $annotes;
	  }

	  /**
	   * Returns an array of method level annotations for the specified class/method. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect
	   * @return array Array of method level annotations
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getMethodsAsArray($class) {

	         if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_METHODS_ARRAY_' . $class;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	  	     $annotes = AnnotationParser::getMethodAnnotationsAsArray($class);
	  	     if(isset($cacher)) $cacher->set($cacheKey, $annotes);
	  	     return $annotes;
	  }

	  /**
	   * Returns an array of property level annotations for the specified class/property.
	   * Tries to return a caches set of annotations first. If no annotations are
	   * found then the specified class is then parsed and the new result is returned. Uses
	   * AgilePHP CacheProvider if enabled.
	   *
	   * @param mixed $class The class name or instance to inspect
	   * @return Array of class level annotations
	   * @throws AnnotationException
	   * @static
	   */
	  public static function getPropertiesAsArray($class) {

	         if($cacher = AgilePHP::getCacher()) {

	            $cacheKey = 'AGILEPHP_ANNOTATION_PROPERTIES_ARRAY_' . $class;
	            if($cacher->exists($cacheKey))
	               return $cacher->get($cacheKey);
	         }

	  	     $annotes = AnnotationParser::getPropertyAnnotationsAsArray($class);
	  	     if(isset($cacher)) $cacher->set($cacheKey, $annotes);
	  	     return $annotes;
	  }
}
?>