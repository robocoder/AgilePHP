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
 * Includes all annotation package dependancies
 */
require_once 'annotation/AnnotationParser.php';
require_once 'annotation/AnnotatedClass.php';
require_once 'annotation/AnnotatedMethod.php';
require_once 'annotation/AnnotatedProperty.php';

/**
 * Opens up the world of annotations to the PHP programming language :p
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Annotation {

	  private function __construct() {}
	  private function __clone() {}

	  /**
	   * Static factory method used to retrieve AnnotatedClass instances.
	   * 
	   * @param mixed $class The class name or instance to inspect
	   * @return void
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getClass( $class ) {

	  		 return new AnnotatedClass( $class );
	  }
	  
	  /**
	   * Static factory method used to retrieve AnnotatedMethod instances.
	   * 
	   * @param mixed $class The class name or instance to inspect.
	   * @param String $name The method name
	   * @return AnnotatedMethod
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getMethod( $class, $method ) {

	  		 return new AnnotatedMethod( $class, $method );
	  }

	  /**
	   * Static factory method used to retrieve AnnotatedProperty instances.
	   * 
	   * @param mixed $class The class name or instance to inspect
	   * @param String $property The property name
	   * @return AnnotatedProperty
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getProperty( $class, $property ) {

	  		 return new AnnotatedProperty( $class, $property );
	  }

	  /**
	   * Returns true if the specified class contains and class, method, or
	   * property level annotations.
	   * 
	   * @param mixed $class The class name or instance to inspect
	   * @return bool True if the class contains any class, method, or property
	   * 		 	   level annotations.
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function hasAnnotations( $class ) {

	  		 $clazz = new AnnotatedClass( $class );

	  		 if( $clazz->isAnnotated() ) return true;

	  		 foreach( $clazz->getMethods() as $method )
	  		 		  if( $method->isAnnotated() ) return true;

	  		 foreach( $clazz->getProperties() as $property )
	  		 		  if( $property->isAnnotated() ) return true;

	  		 return false;
	  }

	  /**
	   * Returns an array of class level annotations for the specified class.
	   * Tries to return a cached set of results first. If no annotations are
	   * found the specified class is then parsed and the new result is returned.
	   * 
	   * @param mixed $class The class name or instance to inspect.
	   * @return array Array of class level annotations
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getClassAsArray( $class ) {

			 $parser = AnnotationParser::getInstance();
			 $annotes = $parser->getClassAnnotationsAsArray( $class );

			 if( count( $annotes ) ) return $annotes;

			 $parser->parse( $class );
	  	     return $parser->getClassAnnotationsAsArray( $class );
	  }

	  /**
	   * Returns an array of method level annotations for the specified class/method.
	   * 
	   * @param mixed $class The class name or instance to inspect
	   * @return array Array of method level annotations
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getMethodsAsArray( $class ) {

			 $parser = AnnotationParser::getInstance();

			 $annotes = $parser->getMethodAnnotationsAsArray( $class );
			 if( count( $annotes ) ) return $annotes;

			 $parser->parse( $class );
	  	     return $parser->getMethodAnnotationsAsArray( $class );
	  }

	  /**
	   * Returns an array of property level annotations for the specified class/property.
	   * Tries to return a caches set of annotations first. If no annotations are
	   * found then the specified class is then parsed and the new result is returned.
	   * 
	   * @param mixed $class The class name or instance to inspect
	   * @param String $property The property/field name to inspect
	   * @return Array of class level annotations
	   * @throws AgilePHP_AnnotationException
	   * @static
	   */
	  public static function getPropertiesAsArray( $class ) {

			 $parser = AnnotationParser::getInstance();

			 $annotes = $parser->getPropertyAnnotationsAsArray( $class );
			 
			 if( count( $annotes ) ) return $annotes;

			 $parser->parse( $class );
	  	     return $parser->getPropertyAnnotationsAsArray( $class );
	  }
}
?>