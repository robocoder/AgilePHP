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

	  private $interceptedTarget;

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
		  		 $this->interceptedTarget = $interceptedClass->newInstanceArgs($args);
	  		 }
	  		 else
		  	 	$this->interceptedTarget = new $intercepted();

	  	 	 $class = new \ReflectionClass($this->interceptedTarget);

	  		 foreach(\AgilePHP::getInterceptions() as $interception) {

	  		 		 // Invoke class level interceptors
		     		 if($interception->getClass() == $proxiedClass &&
		     		 	 !$interception->getMethod() && !$interception->getProperty()) {

	     		 	 	 $interceptorClass = new \AnnotatedClass($interception->getInterceptor());
     	 	 	 		 foreach($interceptorClass->getMethods() as $interceptorMethod) {

     		 	 	 	 	      if($interceptorMethod->hasAnnotation('AroundInvoke')) {

		     		 	 	 	   	  $invocationCtx = new \InvocationContext($this->interceptedTarget, null, null, $interception->getInterceptor());
							          $ctx = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
							          if($ctx instanceof \InvocationContext && $ctx->proceed) {

							          	  $this->interceptedTarget = $ctx->getTarget();
								          if($ctx->getMethod()) $this->__call($ctx->getMethod(), $ctx->getParameters());
							          }
		     		 	 	 	  }
	     		 	 	 }
		     		 }

		     		 // Execute property/field level interceptors
		     		 if($interception->getClass() == $proxiedClass && $interception->getProperty()) {

	     		 	 	 $p = new \ReflectionProperty($this->interceptedTarget, $interception->getProperty());
	     		 	 	 if(!$p->isPublic())
	     		 	 	 	 throw new \InterceptionException('Property level interceptor requires public context at \'' . $proxiedClass .
	     		 	 	 	 		 '::' . $interception->getProperty() . '\'.');

 		 	 	 		$interceptorClass = new \AnnotatedClass($interception->getInterceptor());
 		 	 	 		foreach($interceptorClass->getMethods() as $interceptorMethod) {

     		 	 	 	 	     if($interceptorMethod->hasAnnotation('AroundInvoke')) {

		     		 	 	 	   	 $invocationCtx = new \InvocationContext($this->interceptedTarget, null, null, $interception->getInterceptor(), $interception->getProperty());
							         $return = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
							         $p->setValue($this->interceptedTarget, $return);
		     		 	 	 	 }
 		 	 	 				 if($interceptorMethod->hasAnnotation('AfterInvoke')) {

     		 	 	 		  	 	 $invocationCtx = new \InvocationContext($this->interceptedTarget, null, null, $interception->getInterceptor(), $interception->getProperty());
					              	 $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx );
     		 	 	 		  	 }
	     		 	 	 }
		     		 }
		     }
	  }

	  /**
	   * Custom method used to initalize static fields as soon as the class is loaded.
	   * This method gets invoked only if static fields are parsed from the intercepted target.
	   * 
	   * @return void
	   * @static
	   */
	  public static function __initstatic() {

	         $proxiedClass = get_called_class();
	  		 $intercepted = $proxiedClass . '_Intercepted';

	  		 foreach(\AgilePHP::getInterceptions() as $interception) {

	  		 		 // Invoke class level interceptors
		     		 if($interception->getClass() == $proxiedClass && !$interception->getMethod() && !$interception->getProperty()) {

	     		 	 	 $interceptorClass = new \AnnotatedClass($interception->getInterceptor());
     	 	 	 		 foreach($interceptorClass->getMethods() as $interceptorMethod) {

     		 	 	 	 	      if($interceptorMethod->hasAnnotation('AroundInvoke')) {

		     		 	 	 	   	  $invocationCtx = new \InvocationContext($intercepted, null, null, $interception->getInterceptor());
							          $ctx = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
							          if($ctx instanceof InvocationContext && $ctx->proceed) {

							          	  $intercepted = $ctx->getTarget();
								          if($ctx->getMethod()) $this->__call($ctx->getMethod(), $ctx->getParameters());
							          }
		     		 	 	 	  }
	     		 	 	 }
		     		 }

		     		 // Execute property/field level interceptors
		     		 if($interception->getClass() == $proxiedClass && $interception->getProperty()) {

	     		 	 	 $p = new \ReflectionProperty($intercepted, $interception->getProperty());
	     		 	 	 if(!$p->isPublic())
	     		 	 	 	 throw new \InterceptionException('Property level interceptor requires public context at \'' . $proxiedClass .
	     		 	 	 	 		 '::' . $interception->getProperty() . '\'.');

 		 	 	 		$interceptorClass = new \AnnotatedClass($interception->getInterceptor());
 		 	 	 		foreach($interceptorClass->getMethods() as $interceptorMethod) {

     		 	 	 	 	     if($interceptorMethod->hasAnnotation('AroundInvoke')) {

		     		 	 	 	   	 $invocationCtx = new \InvocationContext($intercepted, null, null, $interception->getInterceptor(), $interception->getProperty());
							         $return = $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
							         $p->setValue($intercepted, $return);
		     		 	 	 	 }
 		 	 	 				 if($interceptorMethod->hasAnnotation('AfterInvoke')) {

     		 	 	 		  	 	 $invocationCtx = new \InvocationContext($intercepted, null, null, $interception->getInterceptor(), $interception->getProperty());
					              	 $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx );
     		 	 	 		  	 }
	     		 	 	 }
		     		 }
		     }
	  }

	  /**
	   * Returns the instance of the intercepted class. This is the original class
	   * that was requested, renamed to '<class_name>_Intercepted'.
	   *
	   * @return The intercepted class instance
	   */
	  public function getInterceptedInstance() {

	  		 return $this->interceptedTarget;
	  }

  	  /**
  	   * Magic PHP method executor used to intercept static method calls.
  	   *
  	   * @param String $method The method being called
  	   * @param Array $args The arguments being passed
  	   * @return The result of the intercepted static method invocation
  	   * @throws InterceptionException
  	   * @static
  	   */
  	  public static function __callstatic($method, $args) {

  	         $className = get_called_class();
  	         $interceptedClass = $className . '_Intercepted';
  	         $class = new ReflectionClass($className);

  	         $sharedContext = null; // Stores an InvocationContext shared among chained interceptors
	         $invoked = false;

	  		 $interceptors = self::getInterceptorsByMethod($className, $method, $className);
	  		 $aroundInvokes = $interceptors['AroundInvoke'];
	  		 $afterInvokes = $interceptors['AfterInvoke'];

	  		 // Execute #@AroundInvoke interceptor methods
	  		 for($i=0; $i<count($aroundInvokes); $i++) {

  		         if(!$sharedContext)
  		            $sharedContext = new \InvocationContext($interceptedClass, $method, $args, $aroundInvokes[$i]['interceptor']->getInterceptor());

	  		     $sharedContext = $aroundInvokes[$i]['method']->invoke($aroundInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);

		         // Only execute the intercepted target call if the InvocationContext has had its proceed() method invoked.
		         if($sharedContext instanceof \InvocationContext && $sharedContext->proceed) {

				    $m = $class->getMethod($sharedContext->getMethod());

					// Invoke the intercepted call, capturing the return value
					$sharedContext->setReturn(call_user_func_array(array($interceptedClass, $method), $sharedContext->getParameters()));
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

     		 	 	$sharedContext = new \InvocationContext($interceptedClass, $method, $args, $afterInvokes[$i]['interceptor']->getInterceptor());
     		 	 	$m = $class->getMethod($method);
     		 	 	$sharedContext->setReturn(call_user_func_array(array($interceptedClass, $method), $args));
     		 	 }

     		 	 $sharedContext = $afterInvokes[$i]['method']->invoke($afterInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);
				 if($sharedContext instanceof InvocationContext && $sharedContext->proceed)
 		 	 	    return $sharedContext->getReturn();
	  		 }

	  		 if($sharedContext instanceof \InvocationContext)
	  		    return $sharedContext->getReturn();

	  		 // No interceptors present for this method, invoke as it was called.
	  		 return call_user_func_array(array($interceptedClass, $method), $args);
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

	         $class = new \ReflectionClass($this->interceptedTarget);

	         $sharedContext = null; // Stores a global InvocationContext that is shared among chained interceptors
	         $invoked = false;

	  		 $interceptors = $this->getInterceptorsByMethod($class->getName(), $method, get_class($this));
	  		 $aroundInvokes = $interceptors['AroundInvoke'];
	  		 $afterInvokes = $interceptors['AfterInvoke'];

	  		 // Execute #@AroundInvoke interceptor methods
	  		 for($i=0; $i<count($aroundInvokes); $i++) {

  		         if(!$sharedContext)
  		            $sharedContext = new \InvocationContext($this->interceptedTarget, $method, $args, $aroundInvokes[$i]['interceptor']->getInterceptor());

	  		     $sharedContext = $aroundInvokes[$i]['method']->invoke($aroundInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);

		         // Only execute the intercepted target call if the InvocationContext has had its proceed() method invoked.
		         if($sharedContext instanceof \InvocationContext && $sharedContext->proceed) {

				    $m = $class->getMethod($sharedContext->getMethod());

					// Invoke the intercepted call, capturing the return value
					$sharedContext->setReturn($args ?
					        $m->invokeArgs($this->interceptedTarget, $sharedContext->getParameters()) : $m->invoke($this->interceptedTarget));
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

     		 	 	$sharedContext = new \InvocationContext($this->interceptedTarget, $method, $args, $afterInvokes[$i]['interceptor']->getInterceptor());
     		 	 	$m = $class->getMethod($method);
     		 	 	$sharedContext->setReturn($args ? $m->invokeArgs($this->interceptedTarget, $args) : $m->invoke($this->interceptedTarget));
     		 	 }

     		 	 $sharedContext = $afterInvokes[$i]['method']->invoke($afterInvokes[$i]['interceptor']->getInterceptor(), $sharedContext);
				 if($sharedContext instanceof InvocationContext && $sharedContext->proceed)
 		 	 	    return $sharedContext->getReturn();
	  		 }

	  		 if($sharedContext instanceof \InvocationContext)
	  		    return $sharedContext->getReturn();

	  		 // No interceptors present for this method, invoke as it was called.
		     $m = $class->getMethod($method);
		     return $args ? $m->invokeArgs($this->interceptedTarget, $args) : $m->invoke($this->interceptedTarget, $args);
	  }

	  /**
  	   * Returns a list of #@AroundInvoke and #@AfterInvoke interceptors that need to be executed for the specified method.
  	   * This method is declared as static so that it can be shared by both __call and __callstatic, as PHP will generate
  	   * an E_WARNING if a non-static method is called statically, however, not the other way around (static method is called
  	   * from within an instance).
  	   *
  	   * @param string $class The name of the target/intercepted class
  	   * @param string $method The name of the method to retrieve interceptors for
  	   * @return array An associative array containing AroundInvoke and AfterInvoke interceptor methods to execute
  	   * @static
  	   */
  	  private static function getInterceptorsByMethod($class, $method, $target) {

  	          // Serve from cache if present
		      if($cacher = \AgilePHP::getCacher()) {

		         $cacheKey = 'AGILEPHP_INTERCEPTORPROXY_GETINTERCEPTORS_' . $class . '_' . $method;
	             if($cacher->exists($cacheKey)) return $cacher->get($cacheKey);
		      }

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
		     	  if(($interceptions[$i]->getClass() == $target || isset($fqcn) && $fqcn == $target)
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

		     $interceptors = array('AroundInvoke' => $prehooks, 'AfterInvoke' => $posthooks);
		     if($cacher) $cacher->set($cacheKey, $interceptors);
		     return $interceptors;
  	  }

	  /**
	   * Invokes class level interceptor #@AfterInvoke methods upon destruction.
	   *
	   * @return void
	   */
	  public function __destruct() {

	         try {
        	  		 $proxiedClass = get_class($this);
        	  		 foreach(\AgilePHP::getInterceptions() as $interception) {
        
        	  		 		  // Invoke class level interceptor #@AfterInvoke
        		     		  if($interception->getClass() == $proxiedClass &&
        		     		 	      !$interception->getMethod() && !$interception->getProperty()) {
        
        	     		 	 	  $interceptorClass = new \AnnotatedClass($interception->getInterceptor());
             	 	 	 		  foreach($interceptorClass->getMethods() as $interceptorMethod) {
        
             	 	 	 				   if($interceptorMethod->hasAnnotation('AfterInvoke')) {
        
             		 	 	 		  	 	   $invocationCtx = new \InvocationContext($this->interceptedTarget, null, null, $interception->getInterceptor());
        					              	   $interceptorMethod->invoke($interception->getInterceptor(), $invocationCtx);
             		 	 	 		  	   }
        	     		 	 	  }
        		     		  }
        	 		}
	         }
	         catch(\Exception $e) {

	               Log::error('InterceptorProxy::__destruct ' . $e->getMessage());
	               throw new \InterceptionException($e->getMessage(), $e->getCode());
	         }
	  }
}
?>