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

require 'interception/InterceptionException.php';
require 'interception/InterceptorFilter.php';
require 'interception/InterceptorProxy.php';
require 'interception/InvocationContext.php';
require 'interception/AroundInvoke.php';
require 'interception/AfterInvoke.php';

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
	  private $static;

	  /**
	   * Initalizes the Interception
	   *
	   * @param String $class The target class name
	   * @param String $method The method name if this is a method level interception
	   * @param String $property The property name if this is a field level interception
	   * @param Object $interceptor The instance of the interceptor which will intercept calls
	   * @return void
	   */
	  public function __construct($class, $method, $property, $interceptor) {

	  		 $this->class = $class;
	  		 $this->method = $method;
	  		 $this->property = $property;
	  		 $this->interceptor = $interceptor;
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
	   * Returns boolean flag used to indicate whether or not the intercepted class
	   * is static.
	   * 
	   * @return boolean True if the class is static, false otherwise.
	   */
	  public function isStatic() {

	         return $this->static;
	  }

	  /**
	   * Creates a new intercepted target instance. The target is created by modifying
	   * the source code of the class being intercepted to *classname*_Intercepted.
	   *
	   * @return Object The new intercepted target instance
	   */
	  public function createInterceptedTarget() {

	         if(class_exists($this->class, false)) return;

	         // Use cache if caching is enabled
		     if($cacher = AgilePHP::getCacher()) {

		        $cacheKey = 'AGILEPHP_INTERCEPTION_TARGET_' . $this->class;
		        if($cacher->exists($cacheKey)) {

		            $o = $cacher->get($cacheKey);
    		        if(@eval($o->code) === false) {

        	  		   Log::error('Interception::createInterceptorProxy ' . PHP_EOL . $code);
        	  		   throw new InterceptionException('Failed to create intercepted target');
    	  		    }
    	  		    return $o->prototype;
		        }
		     }

 	  		 // php namespace support
			 $namespace = explode('\\', $this->class);
			 $className = array_pop($namespace);
			 $namespace = implode('\\', $namespace);

		 	 if(strpos($className, 'phar://') !== false) {

		 	     $code = file_get_contents($className);
		 	 	 $namespace = explode('/', $className);
		 	 	 $className = array_pop($namespace);
		 	 	 $className = preg_replace('/\.php/', '', $className);
		 	 }
		 	 else {

		 	 	 try {
		 	 	       $code = ($namespace) ? 'namespace ' . $namespace . ';' : '';
	  		 	 	   $code .= AgilePHP::getSource($this->class);
		 	 	 }
		 	 	 catch(FrameworkException $e) {

		 	 	 		throw new InterceptionException($e->getMessage(), $e->getCode());
		 	 	 }
		 	 }

		 	 preg_match('/(class\s+.*){/', $code, $matches);
	  		 $code = str_replace('class ' . $className . ' ', 'class ' . $className . '_Intercepted ', $code);
			 $code = $this->clean($code);

			 // Log::debug('Interception::createInterceptedTarget ' . PHP_EOL . $code);

	  		 if(@eval($code) === false) {

	  		    Log::error('Interception::createInterceptorProxy ' . PHP_EOL . $code);
	  		 	throw new InterceptionException('Failed to create intercepted target');
	  		 }

	  		 if(isset($cacher)) {

	  		    $o = new stdClass;
	  		    $o->code = $code;
	  		    $o->prototype = $matches[1];

	  		    $cacher->set($cacheKey, $o);
	  		 }

	  		 return $matches[1];
	  }

	  /**
	   * Creates and loads a dynamic proxy class which performs interceptions
	   * on the class created by Interception::createInterceptedTarget().
	   *
	   * @return void
	   * @throws InterceptionException if there was an issue creating the InterceptorProxy
	   */
	  public function createInterceptorProxy($prototype) {

	         if(class_exists($this->class, false)) return;

	         // Use cache if caching is enabled
		     if($cacher = AgilePHP::getCacher()) {

		        $cacheKey = 'AGILEPHP_INTERCEPTION_PROXY_' . $this->class;
		        if($cacher->exists($cacheKey)) {

		            $code = $cacher->get($cacheKey);
    		        if(@eval($code) === false) {

    	  		       Log::error('Interception::createInterceptorProxy ' . PHP_EOL . $code);
    	  		 	   throw new InterceptionException('Failed to create interceptor proxy for \'' . $this->class . '\'.');
    	  		    }
    	  		    return;
		        }
		     }
	      
	  		 // php namespace support
			 $namespace = explode('\\', $this->class);
			 $className = array_pop($namespace);
			 $namespace = implode('\\', $namespace);

	 	     // Phar support
	  		 if(strpos($className, 'phar://') !== false) {

		     	 $className = str_replace('phar://', '', $className);
		     	 $nspieces = explode('/', $className);
		     	 array_pop($nspieces);
		     	 $namespace = implode('\\', $nspieces);

	  		 	 $pieces = explode('/', $className);
	  		 	 $className = array_pop($pieces);
	  		 	 $className = str_replace('.php', '', $className);
		     }

		     // Create a new class using the intercepted target class prototype (class name and
		     // other associated keywords - namespace, extends, implements, etc).
	  	     try {
	  	            $code = ($namespace) ? 'namespace ' . $namespace . ';' : '';
	  		 		$code .= AgilePHP::getSource('InterceptorProxy');
	  	     }
	  	     catch(FrameworkException $e) {

	  	     		throw new InterceptionException($e->getMessage(), $e->getCode());
	  	     }

	  	     // Replace the class declaration with that of the intercepted target's prototype
	  	     // so that keywords such as "extends" and "implements" and their parameters are
	  	     // preserved. This regex also copies the intercepted target's properties/fields
	  	     // into the proxy.
	  		 $code = preg_replace('/class\s.*{/',
	  		                      $prototype . '{' . PHP_EOL . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $this->getPropertyStubs()),
	  		                      $code);

	  		 $stubs = $this->getMethodStubs();
	  		 $proxyMethods = array('getInterceptedInstance', '__call', '__callstatic', '__initstatic');
	  		 $constructor = null;

	  		 // Create method stubs in the proxy which match those in the intercepted class
	  		 for($i=0; $i<count($stubs['signatures']); $i++) {

	  		 		if($stubs['methods'][$i] == '__construct') {

	  		 			$constructor = $stubs['signatures'][$i];
	  		 			continue;
	  		 		}
	  		 		else if(in_array($stubs['methods'][$i], $proxyMethods)) continue;

	  		 		$call = preg_match('/static\s+/', $stubs['signatures'][$i]) ? 'self::__callstatic' : '$this->__call';
	  		 		$stub = $stubs['signatures'][$i] . ' { return ' . $call . '("' . $stubs['methods'][$i] . '", array' . $stubs['params'][$i] . '); } ';
	  		 		$code = preg_replace('/\}\s*\?>/m', "\t" . $stub . PHP_EOL . '}' . PHP_EOL . '?>', $code);
	  		 }

	  		 // Replace the InterceptorProxy constructor with that of the intercepted target.
	  		 if($constructor) $code = preg_replace('/(public|protected|private)?\sfunction\s__construct\(.*?\)\s{/sm', $constructor . ' {', $code);

	  		 $code = $this->clean($code);

	  		 // Log::debug('Interception::createInterceptorProxy ' . PHP_EOL . $code);

	  		 if(@eval($code) === false) {

	  		    Log::error('Interception::createInterceptorProxy ' . PHP_EOL . $code);
	  		 	throw new InterceptionException('Failed to create interceptor proxy for \'' . $this->class . '\'.');
	  		 }

	  		 // Cache the source code for subsequent requests
	  		 if(isset($cacher)) $cacher->set($cacheKey, $code);
	  }

	  /**
	   * Extracts all property declarations from the intercepted target.
	   * 
	   * @return mixed An array of property declarations or void if no properties could be extracted
	   */
	  private function getPropertyStubs() {

	           $code = AgilePHP::getSource($this->class);

	           // If more than one class exists in the document, only the first class is parsed
	           preg_match('/^class\s.*?}.*?\n}\n/ms', $code, $classes);

	           if($classes[0]) {

	               preg_match_all('/(private|protected|public|[^@]var)\s*(\$.*?;)/sm', $classes[0], $matches);
	               if(isset($matches[0])) return $matches[0];
	           }
	  }
	  
	  /**
	   * Creates public method stubs in the proxy class that match public methods
	   * in the intercepted target class. Without this in place, when using reflection
	   * on the intercepted target class name, the reflection results will actually be
	   * taking place on the InterceptorProxy class and not return expected results.
	   *
	   * @return array Returns an associative array which contains all method signatures,
	   * 			   methods, and their parameters for the intercepted class.
	   */
	  private function getMethodStubs() {

	  		  $code = AgilePHP::getSource($this->class);

	  		  preg_match_all('/[^static]\s(public\s+function\s+(.*?)(\(.*?\)))\s/sm', $code, $methods);
	  		  preg_match_all('/(static\s+.*function\s+(.*?)(\(.*\)))\s/', $code, $statics);

	  		  if(!isset($methods[1]) && !isset($statics[1])) return array();

	  		  $methods[1] = array_merge($methods[1], $statics[1]);
	  		  $methods[2] = array_merge($methods[2], $statics[2]);
	  		  $methods[3] = array_merge($methods[3], $statics[3]);

	  		  // Parameter names are gotten from the method signature
	  		  foreach($methods[3] as &$params) {

	  		       // Remove type hinting
	  		       preg_match_all('/\$[a-zA-Z0-9_]+/', $params, $args);
	  		       $params = '(' . implode(', ', $args[0]) . ')';
	  		  }

	  		  $a['signatures'] = $methods[1];
	  		  $a['methods'] = $methods[2];
	  		  $a['params'] = $methods[3];
	  		  $this->static = count($methods[2]) == count($statics[2]);

	  		  return $a;
	  }

	  /**
	   * Strips PHP open/close tags from source code document so it can be
	   * passed to PHP eval().
	   *
	   * @param $code The PHP code to clean
	   * @return The cleaned code
	   */
	  private function clean($code) {

	  		  $code = str_replace('<?php', '', $code);
	  		  $code = str_replace('?>', '', $code);

	  		  return $code;
	  }
}
?>