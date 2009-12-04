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
 * @package com.makeabyte.agilephp
 */

/**
 * AgilePHP :: Core Framework Class
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 * @static
 */
class AgilePHP {

	  private static $instance;
	  private $displayPhpErrors = true;
	  private $mvc;							// Model-View-Control component singleton instance
	  private $webroot;						// The full system path to the web application
	  private $frameworkRoot;				// The full system path to the location of the AgilePHP framework
	  private $documentRoot;				// The relative path to the web app from the server's document root.
	  private $requestBase;					// The base request URL (used to communicate with MVC component)
	  private $debugMode = false;			// Whether or not this component is running in debug mode
	  private $xml;							// AgilePHP configuration - agilephp.xml
	  private $appName;						// Name of the AgilePHP application
	  private $interceptions;				// An array of interceptions which have occurred during __autoload
	  private $startTime;					// Used with startClock and stopClock methods

	  /**
	   * Initalize the AgilePHP framework. Sets the following defaults \n
	   * $webroot = current working directory (of the script that instantiated the framework)
	   * $requestBase = name of the script that instantiated the framework
	   * $frameworkRoot = $webroot/AgilePHP
	   * $appName = The HTTP HOST header value
	   * 
	   * @return void
	   */
	  private function __construct() {

	  	      $this->webroot = getcwd();
	  	      $this->requestBase = $_SERVER['SCRIPT_NAME'];
	  	      $this->frameworkRoot = $this->webroot . '/AgilePHP';
	  	      $this->appName = $_SERVER['HTTP_HOST'];

	  	      // Parse and set documentRoot
	  	      $pieces = explode( '.php', $_SERVER['SCRIPT_NAME'] );
	  	      array_pop( $pieces );
	  	      $docRootPieces = array();
	  	      $newPieces = explode( '/', implode( '/', $pieces ) );
	  	      for( $i=0; $i<(count( $newPieces ) - 1); $i++ )
	  	      	   $docRootPieces[$i] = $newPieces[$i];
	  	      $this->documentRoot = implode( '/', $docRootPieces );

	  	      $this->parseXml();
	  }

	  private function __clone() { }

	  /**
	   * Factory method which is used to instantiate a singleton
	   * instance of the AgilePHP framework.
	   * 
	   * @return An instance of the AgilePHP Framework
	   */
	  public static function getFramework() {

	  	     if( self::$instance == null )
	  	         self::$instance = new self;

	  	     return self::$instance;
	  }
	  
	  /**
	   * Accessor method for Model-View-Control component. The MVC component this method returns is
	   * created as follows:
	   * 
	   * 1) If an agilephp.xml file is present in the web app root and contains a valid 'mvc'
	   * 	configuration, the MVC component is created using the specified values when AgilePHP
	   * 	framework is instantiated by a call to 'getFramework'.
	   * 
	   * 2) A call to 'initMVC' has already loaded the MVC component so this method will return the
	   * 	already created component.
	   * 
	   * 3) No agilephp.xml file is present. This method will obtain a singleton instance, store it
	   * 	internally, and then return the stored instance.  
	   */
	  public function getMVC() {

	  		 if( $this->mvc == null )
	  		     $this->mvc = MVC::getInstance();

	  		 return $this->mvc;
	  }

	  /**
	   * Sets the fully qualified path to the base directory of
	   * the web application.
	   * 
	   * @param $path The fully qualified path to the web application
	   */
	  public function setWebRoot( $path ) {

	  	     $this->webroot = $path;
	  }

	  /**
	   * Returns the fully qualified path to the base directory of
	   * the web application.
	   * 
	   * @return The fully qualified path to the web application
	   */
	  public function getWebRoot() {

	  	     return $this->webroot;
	  }

	  /**
	   * Sets the full system path to the location of the AgilePHP framework. The given path is
	   * appended to the current php.ini include_path configuration.
	   * 
	   * @param $path The full system path to the location where AgilePHP framework resides.
   	   * @return void 
	   */
	  public function setFrameworkRoot( $path ) {

	  	     $this->frameworkRoot = $path;

	  	     if( strpos( ':' . $path, $path ) === false )
	  	     	 ini_set( 'include_path', ini_get( 'include_path' ) . PATH_SEPARATOR . $path );
	  }

	  /**
	   * Gets the full system path to the location where AgilePHP resides.
	   * 
	   * @return The full system path to the location of the AgilePHP framework.
	   */
	  public function getFrameworkRoot() {

	  		 return $this->frameworkRoot;
	  }

	  /**
	   * Sets the relative path to the web application from the server's document root.
	   * 
	   * @param $path The document root path
	   * @return void
	   */
	  public function setDocumentRoot( $path ) {

	  		 $this->documentRoot = $path;
	  }

	  /**
	   * Returns the relative path to the web application from the server's document root.
	   * 
	   * @return The web applications relative path from the server's document root
	   */
	  public function getDocumentRoot() {

	  		 return $this->documentRoot;
	  }

	  /**
	   * Returns the base action url used to communicate with the
	   * AgilePHP MVC component. Defaults to the name of the script
	   * which initalizes the framework.
	   *
	   * @return The base action url used to communicate with the AgilePHP MVC component.
	   */
	  public function getRequestBase() {

	  		 return $this->requestBase;
	  }

	  /**
	   * Sets the base action url used to communicate with the AgilePHP MVC component.
	   * 
	   * @param $url The base url to be used to communicate with the AgilePHP MVC component.
	   */
	  public function setRequestBase( $url ) {

	  		 $this->requestBase = $url;
	  }

	  /**
	   * Sets the name of the AgilePHP web application.
	   * 
	   * @param $name The name of the AgilePHP application
	   * @return void
	   */
	  public function setAppName( $name ) {
	  	
	  		 $this->appName = $name;
	  }

	  /**
	   * Returns the name of the AgilePHP web application.
	   * 
	   * @return AgilePHP web application name
	   */
	  public function getAppName() {

	  		 return $this->appName;
	  }

	  /**
	   * By default PHP hides errors on production servers. Setting this to true enables PHP
	   * 'display_errors', sets 'error_reporting' to 'E_ALL'.
	   * 
	   * @param $bool True to turn on error reporting on (E_ALL)
	   * @return void
	   */
	  public function setDisplayPhpErrors( $bool ) {

	  	     $this->displayPhpErrors = ($bool == true);

	  	     if( $bool === false ) {

	  	      	 ini_set( 'display_errors', '1' );
	  	      	 error_reporting( E_ALL );
	  	     }
	  }

	  /**
	   * Initalizes a default Model-View-Control component without reading
	   * agilephp.xml configuration.
	   * 
	   * @return void
	   */
	  public function initMVC() {

	  	     $this->mvc = MVC::getInstance();
	  	     $this->mvc->processRequest();
	  }

	  /**
	   * Enables or disables AgilePHP framework debug mode.
	   * 
	   * @param $boolean True for debug mode, false for production mode. Default is production.
	   */
	  public function setDebugMode( $boolean ) {

	  		 $this->debugMode = ($boolean === true) ? true : false;
	  }

	  /**
	   * Whether or not AgilePHP framework is running in debug mode.
	   * 
	   * @return void
	   */
	  public function isInDebugMode() {

	  		 return $this->debugMode;
	  }

	  /**
	   * Returns the agilephp.xml configuration as a SimpleXMLElement.
	   * 
	   * @return The SimpleXMLElement containing the agilephp.xml configuration
	   */
	  public function getXmlConfiguration() {

		     return $this->xml;
	  }

	  /**
	   * Calls PHP date_default_timezone_set function to set the current timezone.
	   * 
	   * @param $timezone The timezone to use as default. (ie. America/New_York)
	   * @return void
	   */
	  public function setDefaultTimezone( $timezone ) {

	  		 date_default_timezone_set( $timezone );
	  }

	  /**
	   * Adds an Interception to the interceptions stack
	   * 
	   * @param Interception $interception The interception instance to add to the stack
	   * @return void
	   */
	  public function addInterception( Interception $interception ) {

	  		 Logger::getInstance()->debug( 'AgilePHP::addInterception Adding interception for class \'' . $interception->getClass() . '\'.' );

	  		 array_push( $this->interceptions, $interception );
	  }

	  /**
	   * Returns an array of Interceptions which have been loaded into the framework
	   * 
	   * @return Array of Interception instances
	   */
	  public function getInterceptions() {

	  		 return $this->interceptions;
	  }

	  /**
	   * Enables the use of interceptors.
	   *  
	   * @return void
	   */
	  public function enableInterceptors() {

	  		 $this->interceptions = array();
	  }

	  /**
	   * Disables interceptors.
	   * 
	   * @return void
	   */
	  public function disableInterceptors() {

	  		 $this->interceptions = null;
	  }

	  /**
	   * Parses AgilePHP configuration file (agilephp.xml) and initalizes the
	   * framework according to the specified configuration.
	   * 
	   * @return void
	   */
	  private function parseXml() {

	  	  	  $agilephp_xml = $this->getWebRoot() . '/agilephp.xml';
	  		  if( !file_exists( $agilephp_xml ) )
	  		  	  return;

	  	      $this->xml = simplexml_load_file( $agilephp_xml );
	  	      
	  		  $dom = new DOMDocument();
 			  $dom->Load( $agilephp_xml );			 
			  if( !$dom->validate() ) {

			 	  throw new AgilePHP_Exception( "agilephp.xml Document Object Model validation failed. You can validate your configurations with the DTD at AgilePHP/core/agilephp.dtd." );
			 	  return;
			  }

	  	      if( $this->xml->mvc ) {

	  	      	  require_once 'MVC.php';

	  	      	  $mvc = MVC::getInstance();
	  	      	  $mvc->setconfig( $this->xml->mvc );
	  	      }

	  		  if( $this->xml->logger ) {

	  	      	  require_once 'Logger.php';
	  	      	  $logger = Logger::getInstance();
	  	      	  $logger->setConfig( $this->xml->logger );

	  	      	  if( $this->xml->logger->attributes()->level == 'debug' )
	  	      	  	  $this->setDebugMode( true );
	  	      }

	  	      /*
	  	       *  Speed things up a bit, i think this is just a nice to have
	  	       *  
	  		  if( $this->xml->scope ) {

	  	      	  require_once 'Scope.php';

	  	      	  $scopeType = (string)$this->xml->scope->attributes()->type;

	  	      	  switch( $scopeType ) {

	  	      	  		  case 'application':
	  	      	  		  	   $appName = (string)$this->xml->scope->attributes()->application;
	  	      	  		  	   if( !$appName ) throw new AgilePHP_Exception( 'Attribute \'application\' required for ApplicationScope. This value should contain the name of your application.' );
	  	      	  		  	   $key = (string)$this->xml->scope->attributes()->key;
	  	      	  		  	   $value = (string)$this->xml->scope->attributes()->value;
	  	      	  		  	   Scope::getInstance()->getApplicationScope()->set( $key, $value );
	  	      	  		  	   break;

	  	      	  		  case 'session':
	  	      	  		  		$key = (string)$this->xml->scope->attributes()->key;
	  	      	  		  	    $value = (string)$this->xml->scope->attributes()->value;
	  	      	  		  		Scope::getInstance()->getSessionScope()->set( $key, $value );
	  	      	  		  		break;

	  	      	  		  case 'request':
	  	      	  		  		$request = Scope::getInstance()->getRequestScope();
	  	      	  		  		$request->set( (string)$this->xml->scope->attributes()->key, (string)$this->xml->scope->attributes()->value );
	  	      	  		  		break;

	  	      	  		  default:
	  	      	  		  	throw new AgilePHP_Exception( 'Unsupported ScopeType configured in agilephp.xml at key="' . 
	  	      	  		  					$this->xml->scope->attributes()->key . '" value="' .
	  	      	  		  					$this->xml->scope->attributes()->value . '"' );
	  	      	  }
	  	      }
	  	      */

	  	      if( $this->xml->interceptors )
	  	      	  $this->interceptions = array();
	  }

	  /**
	   * Starts a timer. Useful for measuring how long a particular
	   * operation takes.
	   * 
	   * @return void
	   */
	  public function startClock() {

		  	 $mtime = microtime();
		     $mtime = explode( ' ', $mtime );
		     $mtime = $mtime[1] + $mtime[0];
		     $this->startTime = $mtime;
	  }

	  /**
	   * Stops the timer and returns the elapsed time between startClock()
	   * and stopClock().
	   * 
	   * @return Time elapsed time between startClock() and endClock()
	   */
	  public function stopClock() {

	  		 $mtime = microtime();
     		 $mtime = explode( ' ', $mtime );
   		     $mtime = $mtime[1] + $mtime[0];
   			 $endtime = $mtime;
   			 $difference = ($endtime - $this->startTime);

   			 return $difference; 
	  }

	  public function __destruct() { }
}

/**
 * Base AgilePHP exception class
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_Exception extends Exception {

	  public function __construct( $message, $code = 0 ) {

	  	     parent::__construct( $message, $code );
	  }
}

/**
 * Exceptions thrown by the Persistence/ORM framework
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_PersistenceException extends AgilePHP_Exception { }

/**
 * Thrown when a user is not logged in and requests secure content
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_NotLoggedInException extends AgilePHP_Exception { }

/**
 * Thrown when a user attempts to access content which they do not have
 * permission to view.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_AccessDeniedException extends AgilePHP_Exception { }

/**
 * Exceptions thrown by the Annotation package
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_AnnotationException extends AgilePHP_Exception { }

/**
 * Exceptions thrown by the Interception package
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_InterceptionException extends AgilePHP_Exception { }

/**
 * Handles all remoting exceptions. Output is returned in JSON format
 * with an application/json HTTP header.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class AgilePHP_RemotingException extends AgilePHP_Exception { 

	  /**
	   * Deliver remoting exceptions in JSON format and halt execution.
	   * 
	   * @param $message The exception message
	   * @return void
	   */
	  public function __construct( $message ) {

			 $this->message = $message;

	  		 $renderer = MVC::getInstance()->createRenderer( 'AJAXRenderer' );
	  		 $renderer->render( $this );
	  		 exit;
	  }
}

/**
 * Responsible for 'lazy loading' classes. If agilephp.xml contains
 * a valid <interceptors/> configuration, the loaded class is handed
 * off to __autoload_interceptions.
 * 
 * @param $class The class being lazy loaded
 * @return void
 */
function __autoload( $class ) {

		 if( is_array( AgilePHP::getFramework()->getInterceptions() ) ) {

		 	 __autoload_interceptions( $class );

		 	 if( !class_exists( $class ) )
  		     	 __autoload_class( $class );

  		     return;
		 }

  		 __autoload_class( $class );
}

/**
 * If the class being loaded is annotated with any interceptors, an InterceptorProxy
 * class is created for the requested class. The InterceptorProxy is a template used
 * to create the dynamic proxy for the class being intercepted. 
 * 
 * Note: Anootations are being gotten as arrays here instead of Annoated* objects
 * 		 because in order to create the dynamic proxy with the name of the requested
 * 	 	 class, we need to make sure that the class has not been loaded since there
 * 		 is no easy/elegant way to unload a PHP class once it has been loaded.
 * 
 * @param $class The name of the class being loaded by __autoload
 * @return void
 */

require_once 'Annotation.php';
require_once 'Interception.php';
require_once 'Interception.php';
require_once 'annotation/annotations/AroundInvoke.php';

function __autoload_interceptions( $class ) {

  	     $classAnnotations = &Annotation::getClassAsArray( $class );
  	     if( count( $classAnnotations ) ) {

		     foreach( $classAnnotations as $annotation ) {

		  	   		  $annote = new AnnotatedClass( $annotation );
			   	   	  if( $annote->hasAnnotation( 'Interceptor' ) ) {

			   	   	  	  $interceptor = $annote->getName();
			   	   	  	  $interception = new Interception( $class, null, null, $annotation );
			   	   	  	  AgilePHP::getFramework()->addInterception( $interception );
			   	   	  }
			 }
  	     }

		 $annotatedMethods = &Annotation::getMethodsAsArray( $class );	  	     
	 	 if( count( $annotatedMethods ) ) {

			 foreach( $annotatedMethods as $methodName => $methodAnnotation ) {

			     foreach( $methodAnnotation as $annotation ) {

			  	   		  $annote = new AnnotatedClass( $annotation );
				   	   	  if( $annote->hasAnnotation( 'Interceptor' ) ) {

				   	   	  	  $interceptor = $annote->getName();
				   	   	  	  $interception = new Interception( $class, $methodName, null, $annotation );
				   	   	  	  AgilePHP::getFramework()->addInterception( $interception );
				   	   	  }
				 }
			 }
  	     }

  	     $annotatedProperties = &Annotation::getPropertiesAsArray( $class );	  	     
	 	 if( count( $annotatedProperties ) ) {

			 foreach( $annotatedProperties as $fieldName => $fieldAnnotation ) {

			     foreach( $fieldAnnotation as $annotation ) {

			  	   		  $annote = new AnnotatedClass( $annotation );
				   	   	  if( $annote->hasAnnotation( 'Interceptor' ) ) {

				   	   	  	  $interceptor = $annote->getName();
				   	   	  	  $interception = new Interception( $class, null, $fieldName, $annotation );
				   	   	  	  AgilePHP::getFramework()->addInterception( $interception );
				   	   	  }
				 }
			 }
  	     }
}

/**
 * Lazy loads standard framework and web application classes that do not
 * contain interceptor annotations.
 *  
 * @param $class The name of the class being loaded by __autoload
 * @return void
 */
function __autoload_class( $class ) {

		 // Load framework classes
		 $path = AgilePHP::getFramework()->getFrameworkRoot() . '/' . $class . '.php';
	     if( file_exists( $path ) ) {

	     	 require_once $path;
	     	 return;
	     }
		 $it = new RecursiveDirectoryIterator( AgilePHP::getFramework()->getFrameworkRoot() );
		 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..'  &&
		   	      	  substr( $file, -4 ) != 'view' ) {

			 		  if( array_pop( explode( '/', $file ) ) == $class . '.php' ) {

		     	 		  require_once $file;
			 		      return;
			 		  }
			      }
		 }

		 // Load web application classes
		 $path = AgilePHP::getFramework()->getWebRoot() . '/components/' . $class . '.php';
	     if( file_exists( $path ) ) {

		     require_once $path;
	     	 return;
	     }
  	     $path = AgilePHP::getFramework()->getWebRoot() . '/control/' . $class . '.php';
  	     if( file_exists( $path ) ) {

		     require_once $path;
  	         return;
  	     }
  	     $path = AgilePHP::getFramework()->getWebRoot() . '/model/' . $class . '.php';
	     if( file_exists( $path ) ) {

		      require_once $path;
  	          return;
	     }
	     $path = AgilePHP::getFramework()->getWebRoot() . '/classes/' .	$class . '.php';
  	     if( file_exists( $path ) ) {

		     require_once $path;
  	         return;
  	     }
	  	 $it = new RecursiveDirectoryIterator( AgilePHP::getFramework()->getWebRoot() );
		 foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..'  &&
		   	      	  substr( $file, -4 ) != 'view' ) {

			 		  if( array_pop( explode( '/', $file ) ) == $class . '.php' ) {

		     	 		  require_once $file;
			 		      return;
			 		  }
			      }
		 }
}
?>