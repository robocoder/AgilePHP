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
 * Provides client side JavaScript remoting to PHP objects. Handles 
 * marshalling/unmarshalling of JSON objects between the client and server. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 * @abstract
 */
abstract class Remoting extends BaseController {

	  	 private $class;

	  	 /**
	  	  * Initalizes the Remoting instance when the extension class is created.
	  	  * 
	  	  * @param String $class
	  	  * @return void
	  	  */
	  	 protected function __construct( $class = null ) {

	  		       $this->class = $class;
	  		       $this->createRenderer( 'AJAXRenderer' );

	  	 		   ini_set('error_prepend_string', '<phperror>');
				   ini_set('error_append_string', '</phperror>');
				   set_error_handler( 'Remoting::ErrorHandler' );
				   ob_start( array( $this, 'captureErrors' ) );
	  	 }

	  	 /**
	  	  * Sets the name of the class to remote
	  	  * 
	  	  * @param String $class The class name to remote
	  	  * @return void
	  	  */
	  	 protected function setClass( $class ) {

	  	 		   $this->class = $class;
	  	 }

	  	 /**
	  	  * Returns the name of the class being remoted
	  	  * 
	  	  * @return The name of the class being remoted
	  	  */
	  	 protected function getClass() {

	  	 		   return $this->class;
	  	 }

	  	 /**
	   	  * Returns the current session id. If a session is not active a new
	   	  * session is created and the id is returned.
	   	  * 
	   	  * @return String Session id for the current request
	  	  */
	  	public function getSessionId() {

	  		   $sessionId = Scope::getInstance()->getSessionScope()->getSessionId();

	  		   Logger::getInstance()->debug( 'Remoting::getSessionId Returning session id \'' . $sessionId . '\'.' );

	  		   return $sessionId;
	  	}

	  	/**
	   	 * Invokes the specified class/method/args using a stateful instance stored in SessionScope. If
	   	 * a stateful instance does not exist, a new instance is created and stored in the SessionScope
	   	 * for future calls.
	   	 * 
	   	 * @return mixed Returns the result of the invocation
	   	 * @throws AgilePHP_RemotingException
	   	 */
	  	public function invokeStateful() {

	  		   $request = Scope::getInstance()->getRequestScope();
	  		   $session = Scope::getInstance()->getSessionScope();

	  		   $class = $request->getSanitized( 'class' );
	    	   $method = $request->getSanitized( 'method' );
	    	   $stub = $this->decode( $request->getSanitized( 'stub' ) );
	    	   $args = $this->decode( $request->getSanitized( 'parameters' ) );

	  		   if( !$classes = $session->get( 'REMOTING_classes' ) ) {

	  		 	   $session->set( 'REMOTING_classes', array() );
	  		 	   $classes = array();
	  		   }

	  		   try {
		  	         $clazz = new ReflectionClass( $class );

		  	         // Restore the instance from an existing session
	  		   	  	 if( array_key_exists( $class, $classes ) ) {

	  		 	   		 $instance = $classes[$class];
	  		 	   		 Logger::getInstance()->debug( 'Remoting::invokeStateful Loading instance of \'' . $class . '\' from session state.' );
	  		   		 }

	  		   		 // Create a new instance using client stub constructor values 
		  	         else if( $stub ) {

		  	         	 $instance = $clazz->newInstanceArgs( (array)$stub );
		  	         	 Logger::getInstance()->debug( 'Remoting::invokeStateful Creating new instance of \'' . $class . '\' from client stub.' );
		  	         }
		  	         
		  	         // Create a new instance without constructor parameters
		  	         else {

		  	         	$instance = $clazz->newInstance();
		  	         	Logger::getInstance()->debug( 'Remoting::invokeStateful Creating new instance of \'' . $class . '\'.' );
		  	         }

		  		     $m = $clazz->getMethod( $method );
		  		     $result = $args ? $m->invokeArgs( $instance, (array)$args ) : $m->invoke( $instance );
		  		     $classes[$class] = $instance;
		  		     $session->set( 'REMOTING_classes', $classes );

		  		     $this->getRenderer()->render( $result );
	  		   }
	  		   catch( Exception $e ) {

	  		 		  throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		   }
	    }

	  	/**
	  	 * Destroys the session used for stateful remoting
	 	 *  
	   	 * @param String $sessionId The id of the session to destroy
	     * @return void
	     */
	    public function destroySession( $sessionId ) {

	  		   $session = Scope::getInstance()->getSessionScope()->setSessionId( $sessionId );
	  		   $session->destroy(); 
	    }

	    /**
	     * Invokes a non-persistent/stateful instance of the requested class/method
	     * passing in arguments if any were defined. This non-stateful approach is
	     * how most RPC web services work.
	     * 
	   	 * @return mixed Returns the result of the invocation
	   	 * @throws AgilePHP_RemotingException
	     */
	    public function invoke() {

	    	   $request = Scope::getInstance()->getRequestScope();

	    	   $class = $request->getSanitized( 'class' );
	    	   $method = $request->getSanitized( 'method' );
	    	   $stub = $this->decode( $request->getSanitized( 'stub' ) );
	    	   $args = $this->decode( $request->getSanitized( 'parameters' ) );
	    	   
	  		   Logger::getInstance()->debug( 'Remoting::invoke Invoking class \'' . $class . '\', method \'' . $method .
	  		 	  	'\', stub \'' . print_r( $stub, true ) . '\', args \'' . print_r( $args, true ) . '\'.' );

	  		   try {
		  	         $clazz = new ReflectionClass( $class );
		  	         $instance = $stub ? $clazz->newInstanceArgs( (array)$stub ) : $clazz->newInstance();
		  		     $m = $clazz->getMethod( $method );

		  		     $this->getRenderer()->render( $args ? $m->invokeArgs( $instance, (array)$args ) : $m->invoke( $instance ) );
	  		   }
	  		   catch( Exception $e ) {

	  		 		  throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		   }
	    }

	    /**
	     * Performs RMI invocation on an intercepted class (non-stateful).
	     * 
	   	 * @return mixed Returns the result of the invocation
	   	 * @throws AgilePHP_RemotingException
	     */
	    public function invokeIntercepted() {

	    	   $request = Scope::getInstance()->getRequestScope();

	    	   $class = $request->getSanitized( 'class' );
	    	   $method = $request->getSanitized( 'method' );
	    	   $stub = $this->decode( $request->getSanitized( 'stub' ) );
	    	   $args = $this->decode( $request->getSanitized( 'parameters' ) );

	  		   Logger::getInstance()->debug( 'Remoting::invokeIntercepted Invoking class \'' . $class . '\', method \'' . $method .
	  		 	  	'\', stub \'' . print_r( $stub, true ) . '\', args \'' . print_r( $args, true ) . '\'.' );

	  		   try {
		  	         $clazz = new ReflectionClass( $class );
		  	         $instance = $stub ? $clazz->newInstanceArgs( (array)$stub ) : $clazz->newInstance();
		  		     $m = $clazz->getMethod( '__call' );
		  		     $callArgs = array( $method, (array) $args );
		  		     $this->getRenderer()->render( $args ? $m->invokeArgs( $instance, $callArgs ) : $m->invokeArgs( $instance, $method ) );
	  		   }
	  		   catch( Exception $e ) {

	  		 		  throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		   }
	    }
	    
		/**
	   	 * Invokes the specified intercepted class/method/args using a stateful instance stored in SessionScope. If
	   	 * a stateful instance does not exist, a new instance is created and stored in the SessionScope
	   	 * for future calls.
	   	 * 
	   	 * @return mixed Returns the result of the invocation
	   	 * @throws AgilePHP_RemotingException
	   	 */
	  	public function invokeInterceptedStateful() {

	  		   $request = Scope::getInstance()->getRequestScope();
	  		   $session = Scope::getInstance()->getSessionScope();

	  		   $class = $request->getSanitized( 'class' );
	    	   $method = $request->getSanitized( 'method' );
	    	   $stub = $this->decode( $request->getSanitized( 'stub' ) );
	    	   $args = $this->decode( $request->getSanitized( 'parameters' ) );

	  		   if( !$classes = $session->get( 'REMOTING_classes' ) ) {

	  		 	   $session->set( 'REMOTING_classes', array() );
	  		 	   $classes = array();
	  		   }

	  		   try {
		  	         $clazz = new ReflectionClass( $class );

		  	         // Restore the instance from an existing session
	  		   	  	 if( array_key_exists( $class, $classes ) ) {

	  		 	   		 $instance = $classes[$class];
	  		 	   		 Logger::getInstance()->debug( 'Remoting::invokeStateful Loading instance of \'' . $class . '\' from session state.' );
	  		   		 }

	  		   		 // Create a new instance using client stub constructor values 
		  	         else if( $stub ) {

		  	         	 $instance = $clazz->newInstanceArgs( (array)$stub );
		  	         	 Logger::getInstance()->debug( 'Remoting::invokeStateful Creating new instance of \'' . $class . '\' from client stub.' );
		  	         }
		  	         
		  	         // Create a new instance without constructor parameters
		  	         else {

		  	         	$instance = $clazz->newInstance();
		  	         	Logger::getInstance()->debug( 'Remoting::invokeStateful Creating new instance of \'' . $class . '\'.' );
		  	         }


		  		     $m = $clazz->getMethod( '__call' );
		  		     $callArgs = array( $method, (array) $args );
		  		     $result = $args ? $m->invokeArgs( $instance, $callArgs ) : $m->invokeArgs( $instance, $method );
		  		     $classes[$class] = $instance;
		  		     $session->set( 'REMOTING_classes', $classes );

		  		     $this->getRenderer()->render( $result );
	  		   }
	  		   catch( Exception $e ) {

	  		 		  throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		   }
	    }

		/**
		 * Creates a dynamic javascript proxy stub/interface used for remoting PHP classes. This method
		 * handles both standard and intercepted classes.
		 * 
		 * @param bool $stateful True to configure the remoting stub to invoke stateful server side calls. The
	     * 					 	 remoted object is kept in the SessionScope.
	  	 * @return void
	  	 * @throws AgilePHP_RemotingException
		 */
		protected function createStub( $stateful = false ) {

		 		  try {
		  		 	     $clazz = new ReflectionClass( $this->class );
		  		 		 if( $clazz->getMethod( 'getInterceptedInstance' ) )
			  		 	     return $this->createInterceptedStub( $stateful );
		 		  }
		 		  catch( Exception $e ) { }

		 		  // Standard PHP class requested
		  		  return $this->createStandardStub( $stateful );
		}

		/**
	     * Creates a dynamic javascript proxy stub/interface used for remoting AgilePHP intercepted classes
	     * 
	     * @param bool $stateful True to configure the remoting stub to invoke stateful server side calls. The
	     * 					 	 remoted object is kept in the SessionScope.
  	     * @return void
  	     * @throws AgilePHP_RemotingException
	     */
	    protected function createInterceptedStub( $stateful = false ) {

	  		   try {
	  		 		  $clazz = new ReflectionClass( $this->class );
	  		 		  $interceptedClazz = new ReflectionClass( $this->class . '_Intercepted' );

	  		 		  // Create javascript object w/ matching constructor parameters
	  		 		  $constructor = $interceptedClazz->getConstructor();
	  		 		  if( $constructor ) {

	  		 			  $js = 'function ' . $this->class . '( ';
	  		 			  $params = $constructor->getParameters();
	  		 			  for( $i=0; $i<count( $params ); $i++ ) {
	  		 				
	  		 				   $js .= $params[$i]->getName();
	  		 				   $js .= ( $i+1 < count( $params ) ) ? ', ' : '';
	  		 			  }
	  		 			  $js .= " ) {\n";
	  		 			  for( $i=0; $i<count( $params ); $i++ )
	  		 				   $js .= 'this.' . $params[$i]->getName() . ' = ' . $params[$i]->getName() . ";\n";

	  		 			  $js .= "}\n";
	  		 		  }
	  		 		  else
	  		 			  $js = 'function ' . $this->class . "() { }\n";

	  		 		  // create methods
	  		 		  $methods = Annotation::getMethodsAsArray( $this->class );
	  		 		  foreach( $methods as $name => $annotations ) {

	  		 		  			foreach( $annotations as $annotation ) {
	  		 		  	   	
		  		 		  	   		    if( $annotation instanceof RemoteMethod ) {

			  		 				   	    // create function
				  		 				    $js .= $this->class . '.prototype.' . $name . ' = function( ';

				  		 				    $rMethod = new ReflectionMethod( $clazz->getName() . '_Intercepted', $name );
				  		 				    $params = $rMethod->getParameters();
				  		 				    for( $j=0; $j<count( $params ); $j++ ) {
	
				  		 				 	  	 $js .= $params[$j]->getName();
				  		 				 	 	 $js .= ( ($j+1) < count( $params ) ) ? ', ' : '';
				  		 				    }
	
				  		 				    $js .= " ) {\n";
	
				  		 				    // function body
				  		 				    $js .= "return AgilePHP.Remoting.getStub( '" . $this->class . "' )." . 
				  		 				   			($stateful ? 'invokeInterceptedStateful' : 'invokeIntercepted') . 
				  		 				   			"( '" . $name . 
				  		 				   			"', arguments" . ($constructor ? ', this' : '' ) . " );\n";
	
				  		 				    // function closure
			  		 				 	    $js .= "}\n";
			  		 				    }
	  		 		  	       }
	  		 		  }

	  		 		  $js .= "\nnew AgilePHP.Remoting.Stub( '" . $this->class . "' );\n";

	  		 		  echo $js;
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		 }
	 }

	 /**
	  * Creates a dynamic javascript proxy stub/interface used for remoting standard PHP classes
	  * 
	  * @param bool $stateful True to configure the remoting stub to invoke stateful server side calls. The
	     * 				 	  remoted object is kept in the SessionScope.
  	  * @return void
  	  * @throws AgilePHP_RemotingException
	  */
	 protected function createStandardStub( $stateful = false ) { 
	 	
	  		   try {
	  		 		  $clazz = new AnnotatedClass( $this->class );

	  		 		  // Create javascript object w/ matching constructor parameters
	  		 		  $constructor = $clazz->getConstructor();
	  		 		  if( $constructor ) {

	  		 			  $js = 'function ' . $this->class . '( ';
	  		 			  $params = $constructor->getParameters();
	  		 			  for( $i=0; $i<count( $params ); $i++ ) {
	  		 				
	  		 				   $js .= $params[$i]->getName();
	  		 				   $js .= ( $i+1 < count( $params ) ) ? ', ' : '';
	  		 			  }
	  		 			  $js .= " ) {\n";
	  		 			  for( $i=0; $i<count( $params ); $i++ )
	  		 				   $js .= 'this.' . $params[$i]->getName() . ' = ' . $params[$i]->getName() . ";\n";

	  		 			  $js .= "}\n";
	  		 		  }
	  		 		  else
	  		 			  $js = 'function ' . $this->class . "() { }\n";


	  		 		  // create methods
	  		 		  $methods = $clazz->getMethods();
	  		 		  for( $i=0; $i<count( $methods ); $i++ ) {

  		 				   if( $methods[$i]->isAnnotated() && $methods[$i]->hasAnnotation( 'RemoteMethod' ) ) {

  		 				   	   // create function
	  		 				   $js .= $this->class . '.prototype.' . $methods[$i]->getName() . ' = function( ';

	  		 				   $params = $methods[$i]->getParameters();
	  		 				   for( $j=0; $j<count( $params ); $j++ ) {

	  		 				 	 	$js .= $params[$j]->getName();
	  		 				 	 	$js .= ( ($j+1) < count( $params ) ) ? ', ' : '';
	  		 				   }

	  		 				   $js .= " ) {\n";

	  		 				   // function body
	  		 				   $js .= "return AgilePHP.Remoting.getStub( '" . $this->class . "' )." . 
	  		 				   			($stateful ? 'invokeStateful' : 'invoke') . 
	  		 				   			"( '" . $methods[$i]->getName() . 
	  		 				   			"', arguments" . ($constructor ? ', this' : '' ) . " );\n";

	  		 				   // function closure
  		 				 	   $js .= "}\n";
  		 				   }
	  		 		  }

	  		 		  $js .= "\nnew AgilePHP.Remoting.Stub( '" . $this->class . "' );\n";

	  		 		  echo $js;
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AgilePHP_RemotingException( $e->getMessage(), $e->getCode() );
	  		 }
	  }

	  /**
	   * Returns the raw JavaScript contents of the AgilePHP.js file and pre-configures the library
	   * with a default AgilePHP.debug and AgilePHP.Remoting.controller value.
	   * 
	   * @param bool $debug True to enable client side AgilePHP debugging.
	   * @return void
	   */
	  public function getBaseJS( $debug = false ) {

	  		 $js = file_get_contents( AgilePHP::getFramework()->getFrameworkRoot() . '/AgilePHP.js' );

	  		 if( $debug ) $js .= "\nAgilePHP.setDebug( true );";

	  		 $js .= "\nAgilePHP.setRequestBase( '" . AgilePHP::getFramework()->getRequestBase() . "' );";
	  		 $js .= "\nAgilePHP.Remoting.setController( '" . MVC::getInstance()->getController() . "' );";

	  		 header( 'content-type: application/json' );
	  		 print $js;
	  }

	  /**
	   * Decodes POST variables
	   * 
	   * @param String $data The client side data to decode
	   * @return Object The JSON decoded object
	   * @throws AgilePHP_RemotingException if the received data does not unmarshall into an object
	   */
	  private function decode( $data ) {

	  		  if( !$data ) return;

	  		  $o = json_decode( stripslashes( htmlspecialchars_decode( stripslashes( urldecode( $data ) ) ) ) );
	  		  if( !is_object( $o ) )
	  		  	  throw new AgilePHP_RemotingException( 'Malformed request' );

	  		  return $o;
	  }

	  /**
	   * Parses each PHP output buffer for php errors and converts them into an AgilePHP_RemotingException
	   * 
	   * @param string $buffer PHP output buffer
	   * @return void
	   * throws AgilePHP_RemotingException
	   */
	  public function captureErrors( $buffer ) {

			 $output = $buffer;
			 $matches = array();
			 $errors = '';

			 /*
			 if( preg_match( '|<phperror>.*</phperror>|s', $output, &$matches ) ) {

				 foreach( $matches as $error )
					$errors .= strip_tags( $error );
			 }
			 */

			 if( ereg('(error</b>:)(.+)(<br)', $buffer, $regs ) ) {

			 	 $err = preg_replace("/<.*?>/","",$regs[2]);
		         $buffer = json_encode( array( '_class' => 'AgilePHP_RemotingException', 'message' => $err, 'trace' => debug_backtrace() ) );
		     }
		     return $buffer;

		     //if( $errors ) throw new AgilePHP_RemotingException( $errors );
			 //return $output;
	  }

	  /**
	   * Custom PHP error handling function which throws an AgilePHP_RemotingException instead of echoing.
	   * 
	   * @param Integer $errno Error number
	   * @param String $errmsg Error message
	   * @param String $errfile The name of the file that caused the error
	   * @param Integer $errline The line number that caused the error
	   * @return false
	   * @throws AgilePHP_Exception
	   */
 	  public static function ErrorHandler( $errno, $errmsg, $errfile, $errline ) {

 	  		 $entry = PHP_EOL . 'Number: ' . $errno . PHP_EOL . 'Message: ' . $errmsg . 
 	  		 		  PHP_EOL . 'File: ' . $errfile . PHP_EOL . 'Line: ' . $errline;

 	  		 throw new AgilePHP_RemotingException( $errmsg, $errno, $errfile, $errline );
	  }

	  /**
	   * Flush PHP output buffer and restore error handler
	   */
	  public function __destruct() {

	  		 ob_end_flush();
	  		 restore_error_handler();
	  }
}
?>