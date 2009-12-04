<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * AgilePHP :: InterceptorProxy
 * Dynamic proxy template for intercepted classes
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.interception
 * @version 0.1a
 */
class InterceptorProxy {

	  private static $instance;
	  private $object;

	  /**
	   * Initalizes the intercepted class by creating a new instance, passing in
	   * any constructor arguments as required. Class level interceptors are invoked
	   * here, as well as dependancy injections via #@In.
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 $proxiedClass = get_class( $this );
	  		 $intercepted = get_class( $this ) . '_Intercepted';

	  		 // Create the intercepted class using constructor arguments if we have any
	  		 if( $args = func_get_args() ) {

	  		 	 $interceptedClass = new ReflectionClass( $intercepted );
		  		 $this->object = $interceptedClass->newInstanceArgs( $args );
	  		 }
	  		 else
		  	 	$this->object = new $intercepted();

		  	 // Invoke class level interceptors upon construction
	  		 foreach( AgilePHP::getFramework()->getInterceptions() as $interception ) {

		     		 if( $interception->getClass() == $proxiedClass &&
		     		 	 !$interception->getMethod() && !$interception->getProperty() ) {

	     		 	 	 $interceptorClass = new AnnotatedClass( $interception->getInterceptor() );
	     		 	 	 foreach( $interceptorClass->getMethods() as $interceptorMethod ) {

	     		 	 	 		  if( $interceptorMethod->hasAnnotation( 'AroundInvoke' ) ) {

	     		 	 	 	 	   	  $invocationCtx = new InvocationContext( $this->object, null, null, $interception->getInterceptor() );
						              $ctx = $interceptorMethod->invoke( $interception->getInterceptor(), $invocationCtx );

						              // Only execute the interceptor if the InvocationContext has had its proceed() method invoked.
						              if( $ctx instanceof InvocationContext && $ctx->proceed ) {

										  $m = $class->getMethod( $ctx->getMethod() );
										  if( $m !== null )
										  	  $ctx->getParameters() ? $m->invokeArgs( $this->object, $ctx->getParameters() ) : $m->invoke( $this->object );
						              }
	     		 	 	 		  }
		     		 	 }
		     		 }

		     		 // Perform property/field injections which are annotated with @In interceptor.
		     		 //
		     		 // NOTE: Interceptor classes do not work with dependancy injection (@In).
		     		 // Workaround is to set public property/fields on interceptor when defining the annotation.
		     		 // For example: #@MyInterceptor( newObject = new obj(), singleton = obj::getInstance() )
		     		 if( $interception->getClass() == $proxiedClass && $interception->getProperty() ) {

		     		 	 $property = $interception->getProperty();
		     		 	 if( $interception->getInterceptor() instanceof In ) {

		     		 	 	 $p = new ReflectionProperty( $this->object, $interception->getProperty() );

		     		 	 	 if( !$p->isPublic() )
		     		 	 	 	 throw new AgilePHP_InterceptionException( '@In interceptor requires public context at \'' . $proxiedClass .
		     		 	 	 	 		 '::' . $interception->getProperty() . '\'.' );

		     		 	 	 $p->setValue( $this->object, $interception->getInterceptor()->class );
		     		 	 }
		     		 }
		     }
	  }

	  /**
	   * Returns a singleton instance of the intercepted class
	   * 
	   * @return Singleton instance of the intercepted class
	   */
	  public static function getInstance() {

	  		 if( self::$instance == null ) {

	  		 	 $intercepted = get_class( self ) . '_Intercepted';
	  		 	 self::$instance = new $intercepted;
	  		 }

	  		 return self::$instance;
	  }

	  /**
	   * Returns the instance of the intercepted class.
	   * 
	   * @return The intercepted class instance
	   */
	  public function getInterceptedInstance() {

	  		 return $this->object;
	  }

	  /**
	   * Magic PHP property accessor
	   * 
	   * @param $property The property/field name being gotten
	   * @return The property/field value
	   * @throws AgilePHP_InterceptionException
	   */
	  public function __get( $property ) {

	  	     try {
		  		   $rp = new ReflectionProperty( $this->object, $property );
	  	  		   return $rp->getValue( $this->object );
	  	     }
	  	     catch( ReflectionException $re ) {

	  	     		throw new AgilePHP_InterceptionException( $re->getMessage(), $re->getCode() );
	  	     }
  	  }

  	  /**
  	   * Magic PHP property mutator
  	   * 
  	   * @param $property The property/field name being set
  	   * @param $value The value to set
  	   * @return void
  	   * @throws AgilePHP_InterceptionException
  	   */
  	  public function __set( $property, $value ) {

  	  		 try {
		  		   $rp = new ReflectionProperty( $this->object, $property );
	  	  		   return $rp->setValue( $this->object, $value );
	  	     }
	  	     catch( ReflectionException $re ) {

	  	     		throw new AgilePHP_InterceptionException( $re->getMessage(), $re->getCode() );
	  	     }
      }

      /**
       * Magic PHP isset. Checks a property to see if its set
       * 
       * @param $property The property/field being tested
       * @return True if the property/field is set, false otherwise
       * @throws AgilePHP_InterceptionException
       */
  	  public function __isset( $property ) {

  	  		 return $this->__get( $property ) ? true : false;
  	  }

  	  /**
  	   * Magic PHP unset function.
  	   * 
  	   * @param $property The property/field being unset
  	   * @return void
  	   * @throws AgilePHP_InterceptionException
  	   */
  	  public function __unset( $property ) {

  	 		 $this->__set( $property, null );
  	  }

  	  /**
  	   * Magic PHP method executor.
  	   * 
  	   * @param $method The method being called
  	   * @param $args The arguments being passed
  	   * @return The result of the method invocation
  	   * @throws AgilePHP_InterceptionException
  	   */
	  public function __call( $method, $args ) {

	  		 $class = new ReflectionClass( $this->object );

	  		 // Invoke interceptor if AgilePHP contains an Interception for this method call
	  		 $interceptions = AgilePHP::getFramework()->getInterceptions();
	  		 if( isset( $interceptions ) ) {

			     foreach( AgilePHP::getFramework()->getInterceptions() as $interception ) {

			     		  if( $interception->getClass() == get_class( $this ) ) {

			     		 	  if( $interception->getMethod() == $method ) {

			     		 	 	  $interceptorClass = new AnnotatedClass( $interception->getInterceptor() );
			     		 	 	  foreach( $interceptorClass->getMethods() as $interceptorMethod ) {

			     		 	 	 	 	   if( $interceptorMethod->hasAnnotation( 'AroundInvoke' ) ) {

			     		 	 	 	 	   	   $invocationCtx = new InvocationContext( $this->object, $method, $args, $interception->getInterceptor() );
								               $ctx = $interceptorMethod->invoke( $interception->getInterceptor(), $invocationCtx );

								              // Only execute the intercepted __call if the InvocationContext has had its proceed() method invoked.
								              if( $ctx instanceof InvocationContext && $ctx->proceed ) {

												  $m = $class->getMethod( $ctx->getMethod() );
												  if( $m !== null )
												  	  return $args ? $m->invokeArgs( $this->object, $ctx->getParameters() ) : $m->invoke( $this->object );
								              }
			     		 	 	 		   }
			     		 	 	  }
			     		 	  }
			     		  }
			      }
	  		 }

	  		 // No interceptors, invoke the intercepted method as it was called.
	  		 if( !isset( $invocationCtx ) ) {

			     $m = $class->getMethod( $method );
			     return $args ? $m->invokeArgs( $this->object, $args ) : $m->invoke( $this->object, $args );
	  		 }
	  }
}
?>