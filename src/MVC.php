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
 * AgilePHP :: Model-View-Control (MVC)
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class MVC {

	  private static $instance = null;

	  private $scriptExtension = '.php';
	  private $defaultController = 'IndexController';
	  private $defaultAction = 'index';
	  private $defaultRenderer = 'PHTMLRenderer';
	  private $controller;
	  private $action;

	  private function __construct() {

	  		  require_once 'mvc/BaseController.php';
	  		  require_once 'mvc/BaseRenderer.php';
	  }

	  private function __clone() {}

	  /**
	   * Returns a singleton instance of MVC
	   * 
	   * @return Singleton instance of MVC
	   */
	  public static function getInstance() {

	  	     if( self::$instance == null )
	  	         self::$instance = new self;

	  	      return self::$instance;
	  }

	  /**
	   * Initalizes the MVC component with agilephp.xml configuration.
	   * 
	   * @param $config SimpleXMLElement containing the MVC configuration.
	   * @return void
	   * @throws AgilePHP_Exception If $config is not an instance of SimpleXMLElement
	   */
	  public function setConfig( $config ) {

	  		 if( !$config instanceof SimpleXMLElement )
	  		     throw new AgilePHP_Exception( 'MVC configuration must be an instance of SimpleXMLElement' );

	  		 if( $config->attributes()->controller ) {

	  		 	 Logger::getInstance( 'MVC::setConfig loading default controller \'' . $config->attributes()->controller . '\' defined in agilephp.xml' );
	  		     $this->defaultController = (string)$config->attributes()->controller;
	  		 }

	  		 if( $config->attributes()->action ) {

	  		 	 Logger::getInstance( 'MVC::setConfig loading default action \'' . $config->attributes()->action . '\' defined in agilephp.xml' );
	  		 	 $this->defaultAction = (string)$config->attributes()->action;
	  		 }

	  		 if( $config->attributes()->renderer ) {

	  		 	 Logger::getInstance( 'MVC::setConfig loading default renderer \'' . $config->attributes()->renderer . '\' defined in agilephp.xml' );
	  		 	 $this->defaultRenderer = (string)$config->attributes()->renderer;
	  		 }
	  }
  
	  /**
	   * Sets the name of the default controller which is used if one is not
	   * specified in the request URI. Default is 'IndexController'.
	   * 
	   * @param $name The name of the controller
	   * @return void
	   */
	  public function setDefaultController( $name ) {

	  	     $this->defaultController = $name;
	  }

	  /**
	   * Returns the name of a default controller if one is not specified
	   * in the request URI. Default is 'IndexController'.
	   * 
	   * @return The name of the default controller
	   */
	  public function getDefaultController() {

	  	     return $this->defaultController;
	  }

	  /**
	   * Sets the name of the default action method if one is not specified
	   * in the request URI. Default is 'index'. 
	   * 
	   * @param $name The name of the default action method
	   * @return void
	   */
	  public function setDefaultAction( $name ) {

	  	     $this->defaultAction = $name;
	  }

	  /**
	   * Returns the name of a default action method if one is not specified
	   * in the request URI. Default is 'index'.
	   * 
	   * @return The name of the default action method
	   */
	  public function getDefaultAction() {

	  	     return $this->defaultAction;
	  }

	  /**
	   * Sets the name of the default view renderer. Default is 'PHTMLRenderer'.
	   * 
	   * @param $renderer The name of a view renderer to use as the default
	   * @return void
	   */
	  public function setDefaultRenderer( $renderer ) {

	  	     $this->defaultRenderer = $renderer;
	  }

	  /**
	   * Returns the name of the default view renderer
	   * 
	   * @return The default view renderer
	   */
	  public function getDefaultRenderer() {

	  	     return $this->defaultRenderer;
	  }
	  
	  /**
	   * Returns the name of the controller currently in use.
	   * 
	   * @return The name of the controller in use by the MVC component.
	   */
	  public function getController() {
	  	
	  		 return $this->controller;
	  }

	  /**
	   * Returns the action currently being invoked.
	   * 
	   * @return The name of the action currently being invoked.
	   */
	  public function getAction() {

	  		 return $this->action;
	  }

	  /**
	   * Parses the current request URI to obtain the controller, action method, and arguments
	   * present for this request and then performs the invocation. If these parameters ARE NOT
	   * present, the default controller and default action method are used instead.
	   * 
	   * NOTE: The URI requirement to communicate with this MVC system is as follows
	   *       http://domain.com/ScriptName.php/ControllerName/ActionMethod/arg1/arg2/arg3/etc...
	   * 
	   * @return void
	   */
	  public function processRequest() {

	  	     preg_match( '/^(.+?\.php)(.*)/si', $_SERVER['REQUEST_URI'], $matches );

	  	     // $matches[1] is the request base (for example /httpdocs/index.php)
	  	     // $matches[2] is everything else after $matches[1]

	  	  	 $mvcPieces = explode( '/', $matches[2] );
		  	 array_shift( $mvcPieces ); // $matches[2] starts with forward slash which makes the first element empty

		  	 // Assign controller and action
	  	     $controller = (count($mvcPieces) > 0) ? $mvcPieces[0] : $this->getDefaultController(); 
	  	     $action = (count( $mvcPieces ) > 1) ? $mvcPieces[1] : $this->getDefaultAction();

	  	     // Remove controller and action from mvcPieces
	  	     array_shift( $mvcPieces );
	  	     array_shift( $mvcPieces );

	  	     // Security, Security, Security.... 
	  	     $controller = addslashes( strip_tags( $controller ) );
	  	     $action = addslashes( strip_tags( $action ) );

	  	     $this->controller = $controller;
	  	     $this->action = $action;

	  	     // Use reflection to invoke the requested controller/method/args
	  	     $defaultController = $this->getDefaultController();
	  	     $oController = $controller ? new $controller : new $defaultController;

	  	   //  try {
			  	     $class = new ReflectionClass( $oController );
			  	     $m = $class->getMethod( $action ? $action : $this->getDefaultAction() );
		
			  	     if( isset( $mvcPieces ) ) {
		
			  	     	 foreach( $mvcPieces as $key => $val )
				  	     	 $mvcPieces[$key] = addslashes( strip_tags( $val ) );
		
				  	     Logger::getInstance()->debug( 'MVC::processRequest Invoking controller \'' . $controller . 
				  	     			'\', action \'' . $action . '\', args \'' . implode( ',', $mvcPieces  ) . '\'.' );
				  	     
				  	     $m->invokeArgs( $oController, $mvcPieces );
			  	     }
			  	     else {

			  	     	 Logger::getInstance()->debug( 'MVC::processRequest Invoking controller \'' . $controller . 
				  	     			'\', action \'' . $action . '\'.' );
		
			  	     	 $m->invoke( $oController );
			  	     }
	  	//     }
	  	//     catch( Exception $e ) {

	  	//     		throw new AgilePHP_Exception( $e->getMessage(), $e->getCode() );
	  	//     }
	  }

	  /**
	   * Returns a new instance of the default view renderer
	   * 
	   * @return An instance of the default renderer
	   */
	  public function createDefaultRenderer() {

	  	     $path = AgilePHP::getFramework()->getFrameworkRoot() . '/mvc/' . $this->getDefaultRenderer() . '.php';

	  	     Logger::getInstance()->debug( 'MVC::createDefaultRenderer loading renderer: ' . $this->getDefaultRenderer() );

	  	     if( !file_exists( $path ) )
	  	     	 throw new AgilePHP_Exception( 'Default framework renderer could not be loaded from: ' . $path );

	  	     require_once $path;

	  	     $renderer = $this->getDefaultRenderer();
	  	     return new $renderer();
	  }

	  /**
	   * Returns a new instance of the specified view renderer
	   * 
	   * @return An instance of the specified renderer
	   */
	  public function createRenderer( $renderer ) {

	  	     $path = AgilePHP::getFramework()->getFrameworkRoot() . '/mvc/' . $renderer . '.php';

	  	     Logger::getInstance()->debug( 'MVC::createRenderer loading renderer: ' . $renderer );

	  		 if( !file_exists( $path ) )
	  	     	 throw new AgilePHP_Exception( 'Framework renderer could not be loaded from: ' . $path );

			 require_once $path; 	  		 
	  		 return new $renderer;
	  }

	  /**
	   * Returns a new instance of the specified renderer. The renderer is loaded from
	   * the web app 'classes' directory.
	   * 
	   * @param $renderer The name of the custom view renderer
	   * @param $classpath A relative child path under the webapp's 'classes' folder where the renderer is located.
	   * @return A new instance of the custom renderer
	   */
	  public function createCustomRenderer( $renderer, $classpath='' ) {

	  	     $path = AgilePHP::getFramework()->getWebRoot() . '/classes/' . $classpath . '/' . $renderer . '.php';

	  	     Logger::getInstance()->debug( 'MVC::createDefaultRenderer loading custom renderer: ' . $renderer );

	  	     if( !file_exists( $path ) )
	  	     	 throw new AgilePHP_Exception( 'Custom renderer could not be loaded from: ' . $path );

	  	     require_once $path;
	  	     return new $renderer;
	  }
}
?>