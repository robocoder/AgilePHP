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
 * Includes all interception package dependancies
 */
require_once 'interception/InterceptorProxy.php';
require_once 'interception/InvocationContext.php';

/**
 * Performs interceptions by creating a dynamic proxy for intercepted
 * classes. The proxy invokes the intended calls after inspecting (and/or
 * intercepting) it according to the annotations in the intercepted object.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp
 */

class Interception {

	  private $class;
	  private $method;
	  private $property;
	  private $interceptor;

	  /**
	   * Initalizes the Interception
	   * 
	   * @param String $class The target class name
	   * @param String $method The method name
	   * @param Object $interceptor The instance of the interceptor which will intercept calls
	   * @return void
	   */
	  public function __construct( $class, $method, $property, $interceptor ) {

	  		 $this->class = $class;
	  		 $this->method = $method;
	  		 $this->property = $property;
	  		 $this->interceptor = $interceptor;

	  		 $this->createInterceptedTarget();
	  		 $this->createInterceptorProxy();
	  }

	  /**
	   * Returns the name of the target class being intercepted
	   * 
	   * @return The interception target class name
	   */
	  public function getClass() {

	  		 return $this->class;
	  }

	  /**
	   * Returns the name of the target method to intercept
	   */
	  public function getMethod() {

	  		 return $this->method;
	  }

	  /**
	   * Returns the name of the property to intercept
	   */
	  public function getProperty() {
	  	
	  		 return $this->property;
	  }

	  /**
	   * Returns the interceptor instance which handles the intercepted
	   * call.
	   * 
	   * @return void
	   */
	  public function getInterceptor() {

	  		 return $this->interceptor;
	  }

	  /**
	   * Creates a new intercepted target instance. The target is created by modifying
	   * the source code of the class being intercepted to *classname*_Intercepted.
	   * 
	   * @return Object The new intercepted target instance
	   */
	  public function createInterceptedTarget() {

	  		 // php namespace support
			 $namespace = explode( '\\', $this->class );
			 $className = $namespace[count($namespace)-1];
		 	 $namespace = implode( '\\', $namespace );
	  		 $class = str_replace( '\\', '::', $this->class );
	  		 
	  		 if( class_exists( $class, false ) ) return;

	  		 $code = $this->getSourceCode( $this->class );
	  		 $code = preg_replace( '/class\s' . $className . '\s/', 'class ' . $className . '_Intercepted ', $code );

			 $code = $this->clean( $code );
	  		 if( eval( $code ) === false )
	  		 	 throw new AgilePHP_InterceptionException( 'Failed to create intercepted target' );
	  }

	  /**
	   * Creates and loads a dynamic proxy class which performs interceptions
	   * on the class created by Interception::createInterceptedTarget().
	   * 
	   * @return void
	   */
	  public function createInterceptorProxy() {

	  		 // php namespace support
			 $namespace = explode( '\\', $this->class );
			 $className = $namespace[count($namespace)-1];
			 array_pop( $namespace );
			 $namespace = implode( '\\', $namespace );
	  		 $class = str_replace( '\\', '::', $this->class );

	  		 if( class_exists( $className, false ) ) return;

	  	     $code = ($namespace) ? 'namespace ' . $namespace . ';' : ''; 
	  		 $code .= $this->getSourceCode( 'InterceptorProxy' );
	  		 $code = preg_replace( '/InterceptorProxy/', $className, $code );

	  		 $stubs = $this->getMethodStubs();
	  		 $proxyMethods = array( 'getInstance', 'getInterceptedInstance',
	  		 						   '__get', '__set', '__isset', '__unset', '__call' );

	  		 $constructor = null;

	  		 // Create method stubs in the proxy which match those in the intercepted class
	  		 for( $i=0; $i<count( $stubs['signatures'] ); $i++ ) {

	  		 		if( $stubs['methods'][$i] == '__construct' ) {

	  		 			$constructor = $stubs['signatures'][$i];
	  		 			continue;
	  		 		}
	  		 		else if( in_array( $stubs['methods'][$i], $proxyMethods ) ) continue;

	  		 		$call = $stubs['signatures'][$i] . ' { return $this->__call( "' . $stubs['methods'][$i] . '", array' . $stubs['params'][$i] . ' ); } ';
	  		 		$code = preg_replace( '/\}\s*\?>/m', "\t" . $call . PHP_EOL . '}' . PHP_EOL . '?>', $code );
	  		 }

	  		 // Make sure the proxy constructor matches the intercepted class
	  		 if( $constructor )
	  		 	 $code = preg_replace( '/public\sfunction\s__construct.*\)/', $constructor, $code );

	  		 $code = $this->clean( $code );
	  		 if( eval( $code ) === false )
	  		 	 throw new AgilePHP_InterceptionException( 'Failed to create interceptor proxy for \'' . $this->class . '\'.' );
	  }

	  /**
	   * Creates public method stubs in the proxy class that match public methods
	   * in the intercepted target class. Without this in place, when using reflection
	   * on the intercepted target class name, the reflection results will actually be
	   * taking place on the InterceptorProxy class and not return expected results. 
	   * 
	   * @return void
	   */
	  private function getMethodStubs() {

	  		  $code = $this->getSourceCode( $this->class );
	  		  preg_match_all( '/(public\s+function\s+(.*?)(\(.*\)))\s/', $code, $matches );

	  		  if( !isset( $matches[1] ) )
	  		 	   return array();

	  		  // Parameter names are gotten from the method signature
	  		  foreach( $matches[3] as &$param ) {

	  		  	  // Remove type hinting
	  		 	  $param = preg_replace( '/[^\$a-zA-Z0-9][a-zA-Z0-9]+?\s/', ' ', $param );
	  		 	  $param = preg_replace( '/=\s*\)/', ')', $param ); // @todo spend countless more hours trying to get everything into one regex
	  		  }

	  		  $a['signatures'] = $matches[1]; 
	  		  $a['methods'] = $matches[2];
	  		  $a['params'] = $matches[3];

	  		  return $a;
	  }

	  /**
	   * Strips PHP open/close tags from source code document so it can be
	   * passed to PHP eval().
	   * 
	   * @param $code The PHP code to clean
	   * @return The cleaned code
	   */
	  private function clean( $code ) {

	  		  $code = preg_replace( '/<\?php/', '', $code );
	  		  $code = preg_replace( '/\?>/', '', $code );

	  		  return $code;
	  }

	  /**
	   * Returns the PHP file content to be parsed.
	   * 
	   * @return String PHP code
	   * @throws AgilePHP_InterceptionException if the source could not be loaded
	   */
	  public function getSourceCode( $class ) {

	  		 // php namespace support
			 $namespace = explode( '\\', $class );
		 	 $class = array_pop( $namespace );
		 	 $namespace = implode( DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;

			 // Load framework classes
			 $path = AgilePHP::getFramework()->getFrameworkRoot() . DIRECTORY_SEPARATOR . $namespace . $class . '.php';
		     if( file_exists( $path ) ) {
	
		     	 return file_get_contents( $path );
		     }
			 $it = new RecursiveDirectoryIterator( AgilePHP::getFramework()->getFrameworkRoot() );
			 foreach( new RecursiveIteratorIterator( $it ) as $file ) {
	
			   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {
	
			   	      	  $array = explode( DIRECTORY_SEPARATOR, $file );
				 		  if( array_pop( $array ) == $class . '.php' ) {
	
			     	 		  return file_get_contents( $file );
				 		  }
				      }
			 }
	
			 // Load web application classes
	  	     $path = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'control' .
	  	     		 DIRECTORY_SEPARATOR . $namespace . $class . '.php';
	  	     if( file_exists( $path ) ) {
	
			     return file_get_contents( $path );
	  	     }
	  	     $path = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'model' .
	  	     		 DIRECTORY_SEPARATOR . $namespace . $class . '.php';
		     if( file_exists( $path ) ) {
	
			      return file_get_contents( $path );
		     }
		     $path = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'classes' .
		     		 DIRECTORY_SEPARATOR . $namespace . $class . '.php';
	  	     if( file_exists( $path ) ) {
	
			     return file_get_contents( $path );
	  	     }
			 $path = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'components' .
			 		 DIRECTORY_SEPARATOR . $namespace . $class . '.php';
		     if( file_exists( $path ) ) {
	
			     return file_get_contents( $path );
		     }
	  	     // Not found in the usual places - perform deep scan
		  	 $it = new RecursiveDirectoryIterator( AgilePHP::getFramework()->getWebRoot() );
			 foreach( new RecursiveIteratorIterator( $it ) as $file ) {
	
			   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..'  &&
			   	      	  substr( $file, -4 ) != 'view' ) {
	
			   	      	  $pieces = explode( DIRECTORY_SEPARATOR, $file );
				 		  if( array_pop( $pieces ) == $class . '.php' ) {
	
			     	 		  return file_get_contents( $file );
				 		  }
				      }
			 }

			 throw new AgilePHP_InterceptionException( 'Failed to load source code for class \'' . $class . '\'.' );
	  }
}

?>