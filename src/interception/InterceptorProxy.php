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
 * Proxy / State Machine responsible for intercepting method calls,
 * invoking interceptors, and maintaining interceptor chain state.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.interception
 */
class InterceptorProxy {

	  private $object;  		  	// Stores the intercepted object
	  private static $instance;	  	// Stores a static instance of the intercepted class

	  /**
	   * Initalizes the intercepted class by creating a new instance, passing in
	   * any constructor arguments as required. Class level interceptors are invoked
	   * here, as well as dependancy injections via #@In.
	   *
	   * @return void
	   */
	  public function __construct() {

	  		 $proxiedClass = get_class($this);
	  		 $intercepted = get_class($this) . '_Intercepted';

	  		 // Create the intercepted class using constructor arguments if we have any
	  		 if($args = func_get_args()) {

	  		 	 $interceptedClass = new \ReflectionClass($intercepted);
		  		 $this->object = $interceptedClass->newInstanceArgs($args);
	  		 }
	  		 else
		  	 	$this->object = new $intercepted();

	  	 	 $class = new \ReflectionClass($this->object);

	  		 foreach(\AgilePHP::getInterceptions() as $interception) {

	  		 		 // Invoke class level interceptors
		     		 if($interception->getClass() == $proxiedClass &&
		     		 	 !$interception->getMethod() && !$interception->getProperty()) {

	     		 	 	 $interceptorClass = new \AnnotatedClass($interception->getInterceptor());
     	 	 	 		 foreach($interceptorClass->getMethods() as $interceptorMethod) {

     		 	 	 	 	      if($interceptorMethod->hasAnnotation('AroundInvoke')) {

		     		 	 	 	   	  $invocationCtx = new \InvocationContext($this->object, null, null, $interception->getInterceptor());
							          $ctx = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
							          if($ctx instanceof InvocationContext && $ctx->proceed) {

							          	  $this->object = $ctx->getTarget();
								          if($ctx->getMethod()) $this->__call($ctx->getMethod(), $ctx->getParameters());
							          }
		     		 	 	 	  }
	     		 	 	 }
		     		 }

		     		 // Perform property/field injections
		     		 if($interception->getClass() == $proxiedClass && $interception->getProperty()) {

		     		 	 	 // Execute property level interceptors
		     		 	 	 $p = new \ReflectionProperty($this->object, $interception->getProperty());
		     		 	 	 if(!$p->isPublic())
		     		 	 	 	 throw new \InterceptionException('Property level interceptor requires public context at \'' . $proxiedClass .
		     		 	 	 	 		 '::' . $interception->getProperty() . '\'.');

     		 	 	 		$interceptorClass = new \AnnotatedClass($interception->getInterceptor());
     		 	 	 		foreach($interceptorClass->getMethods() as $interceptorMethod) {

	     		 	 	 	 	     if($interceptorMethod->hasAnnotation('AroundInvoke')) {

			     		 	 	 	   	 $invocationCtx = new \InvocationContext($this->object, null, null, $interception->getInterceptor(), $interception->getProperty());
								         $value = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
								         $p->setValue($this->object, $value);
			     		 	 	 	 }
     		 	 	 				 if($interceptorMethod->hasAnnotation('AfterInvoke')) {

	     		 	 	 		  	 	 $invocationCtx = new \InvocationContext($this->object, null, null, $interception->getInterceptor(), $interception->getProperty());
						              	 $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx );
	     		 	 	 		  	 }
		     		 	 	 }
		     		 }
		     }
	  }

	  /**
	   * Returns a singleton instance of the intercepted class
	   *
	   * @return Singleton instance of the intercepted class
	   * @static
	   */
	  public static function getInstance() {

	  		 $backtrace = debug_backtrace();
	  		 $name = $backtrace[0]['class'] . '_Intercepted';

        	 $class = new \ReflectionClass($name);
	  		 $m = $class->getMethod('getInstance');
	  		 $args = func_get_args();
	  		 self::$instance = ($args) ? $m->invokeArgs($class, $args) : $m->invoke($class);

	  		 return self::$instance;
 	  }

	  /**
	   * Returns the instance of the intercepted class. This is the original class
	   * that was requested, renamed to '<class_name>_Intercepted'.
	   *
	   * @return The intercepted class instance
	   */
	  public function getInterceptedInstance() {

	  		 return $this->object;
	  }

	  /**
	   * Magic PHP property accessor used to intercept calls to properties.
	   *
	   * @param String $property The property/field name being accessed
	   * @return The property/field value
	   * @throws InterceptionException
	   */
	  public function __get($property) {

	  	     try {
		  		   $rp = new \ReflectionProperty($this->object, $property);
	  	  		   return $rp->getValue($this->object);
	  	     }
	  	     catch(ReflectionException $re) {

	  	     		throw new \InterceptionException($re->getMessage(), $re->getCode());
	  	     }
  	  }

  	  /**
  	   * Magic PHP property mutator used to intercept mutation calls.
  	   *
  	   * @param String $property The property/field name being set
  	   * @param mixed $value The value to set
  	   * @return void
  	   * @throws InterceptionException
  	   */
  	  public function __set($property, $value) {

  	  		 try {
		  		   $rp = new \ReflectionProperty($this->object, $property);
	  	  		   return $rp->setValue($this->object, $value);
	  	     }
	  	     catch(ReflectionException $re) {

	  	     		throw new \InterceptionException($re->getMessage(), $re->getCode());
	  	     }
      }

      /**
       * Magic PHP isset. Checks a property to see if its set
       *
       * @param $property The property/field being tested
       * @return True if the property/field is set, false otherwise
       * @throws InterceptionException
       */
  	  public function __isset($property) {

  	  		 return $this->__get($property) ? true : false;
  	  }

  	  /**
  	   * Magic PHP unset function used to intercept unset calls.
  	   *
  	   * @param String $property The property/field being unset
  	   * @return void
  	   * @throws InterceptionException
  	   */
  	  public function __unset($property) {

  	 		 $this->__set($property, null);
  	  }

  	  /**
  	   * Returns a list of #@AroundInvoke and #@AfterInvoke interceptors that need to be executed for the specified method.
  	   *
  	   * @param string $class The name of the target/intercepted class
  	   * @param string $method The name of the method to retrieve interceptors for
  	   * @return array An associative array containing AroundInvoke and AfterInvoke interceptor methods to execute
  	   */
  	  private function getInterceptorsByMethod($class, $method) {

  	          // Serve from cache if present
	  		  $key = 'AGILEPHP_INTERCEPTION_' . $class . '_' . $method;
		      if($cacher = \AgilePHP::getCacher())
	             if($cacher->exists($key))
		            return $cacher->get($key);

  	          $prehooks = array();
	  		  $posthooks = array();

  	          // Invoke interceptor if AgilePHP contains an Interception for this method call
	  		  $interceptions = \AgilePHP::getInterceptions();
		      for($i=0; $i<count($interceptions); $i++) {

				  // Phar support
				  if(strpos($interceptions[$i]->getClass(), 'phar://') !== false) {

					 $className = preg_replace('/phar:\/\//', '', $interceptions[$i]->getClass());
				     $nspieces = explode('/', $className);
				     array_pop($nspieces);
				     $namespace = implode('\\', $nspieces);

			  		 $pieces = explode('/', $className);
			  		 $className = array_pop($pieces);
			  		 $fqcn = $namespace . '\\' . preg_replace('/\.php/', '', $className);
				  }

				  // Parse methods annotated with #@AroundInvoke and #@AfterInvoke
		     	  if(($interceptions[$i]->getClass() == get_class($this) || isset($fqcn) && $fqcn == get_class($this))
		     	  			 && $interceptions[$i]->getMethod() == $method) {

	     			  $interceptorClass = new \AnnotatedClass($interceptions[$i]->getInterceptor());
	     		 	  foreach($interceptorClass->getMethods() as $interceptorMethod) {

	     		 	 	 	  if($interceptorMethod->hasAnnotation('AroundInvoke'))
	     		 	 	 	     array_push($prehooks, array('method' => $interceptorMethod, 'interceptor' => $interceptions[$i]));

	     		 	 	 	  if($interceptorMethod->hasAnnotation('AfterInvoke'))
	     		 	 	 	     array_push($posthooks, array('method' => $interceptorMethod, 'interceptor' => $interceptions[$i]));
	     		 	 	   }
   		  			 }
		     }

		     $methods = array('AroundInvoke' => $prehooks, 'AfterInvoke' => $posthooks);

		     if($cacher) $cacher->set($key, $methods);

		     return $methods;
  	  }

  	  /**
  	   * Magic PHP method executor used to intercept method calls.
  	   *
  	   * @param String $method The method being called
  	   * @param Array $args The arguments being passed
  	   * @return The result of the intercepted method invocation
  	   * @throws InterceptionException
  	   */
	  public function __call($method, $args) {

	         $class = new \ReflectionClass($this->object);

	         $sharedContext = null; // Stores a global InvocationContext that is shared among chained interceptors
	         $invoked = false;

	  		 $interceptors = $this->getInterceptorsByMethod($class->getName(), $method);
	  		 $aroundInvokes = $interceptors['AroundInvoke'];
	  		 $afterInvokes = $interceptors['AfterInvoke'];

	  		 // Execute #@AroundInvoke interceptor methods
	  		 for($i=0; $i<count($aroundInvokes); $i++) {

  		         if(!$sharedContext)
  		            $sharedContext = new \InvocationContext($this->object, $method, $args, $aroundInvokes[$i]['interceptor']->getInterceptor());

	  		     $sharedContext = $aroundInvokes[$i]['method']->invoke($aroundInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);

		         // Only execute the intercepted target call if the InvocationContext has had its proceed() method invoked.
		         if($sharedContext instanceof \InvocationContext && $sharedContext->proceed) {

				    $m = $class->getMethod($sharedContext->getMethod());

					// Invoke the intercepted call, capturing the return value
					$sharedContext->setReturn($args ?
					        $m->invokeArgs($this->object, $sharedContext->getParameters()) : $m->invoke($this->object));
		         }
		         else {

		            // Interceptors that return a non-null value get the return value returned to the caller.
		          	if($sharedContext !== null) return $sharedContext;
                }
	  		 }

	  		 // Execute #@AfterInvoke interceptor methods
	  		 for($i=0; $i<count($afterInvokes); $i++) {

	  		     // If no #@AroundInvoke interceptions occurred, invoke the intercepted call, capturing the return value
 	 	   	     if(!$sharedContext) {

     		 	 	$sharedContext = new \InvocationContext($this->object, $method, $args, $afterInvokes[$i]['interceptor']->getInterceptor());
     		 	 	$m = $class->getMethod($method);
     		 	 	$sharedContext->setReturn($args ? $m->invokeArgs($this->object, $args) : $m->invoke($this->object));
     		 	 }

     		 	 $sharedContext = $afterInvokes[$i]['method']->invoke($afterInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);
				 if($sharedContext instanceof InvocationContext && $sharedContext->proceed)
 		 	 	    return $sharedContext->getReturn();
	  		 }

	  		 if($sharedContext instanceof \InvocationContext)
	  		    return $sharedContext->getReturn();

	  		 // No interceptors present for this method, invoke as it was called.
		     $m = $class->getMethod($method);
		     return $args ? $m->invokeArgs($this->object, $args) : $m->invoke($this->object, $args);
	  }

	  /**
	   * Invokes class level interceptor #@AfterInvoke methods upon destruction.
	   *
	   * @return void
	   */
	  public function __destruct() {

	  		 $proxiedClass = get_class($this);
	  		 foreach(\AgilePHP::getInterceptions() as $interception) {

	  		 		  // Invoke class level interceptor #@AfterInvoke
		     		  if($interception->getClass() == $proxiedClass &&
		     		 	      !$interception->getMethod() && !$interception->getProperty()) {

	     		 	 	  $interceptorClass = new \AnnotatedClass($interception->getInterceptor());
     	 	 	 		  foreach($interceptorClass->getMethods() as $interceptorMethod) {

     	 	 	 				   if($interceptorMethod->hasAnnotation('AfterInvoke')) {

     		 	 	 		  	 	   $invocationCtx = new \InvocationContext($this->object, null, null, $interception->getInterceptor());
					              	   $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
     		 	 	 		  	   }
	     		 	 	  }
		     		  }
	 		}
	  }
}
?>