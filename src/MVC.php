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
 * Model-View-Control (MVC) component
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.2a
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
	   * @static
	   */
	  public static function getInstance() {

	  	     if( self::$instance == null )
	  	         self::$instance = new self;

	  	      return self::$instance;
	  }

	  /**
	   * Initalizes the MVC component with agilephp.xml configuration.
	   * 
	   * @param SimpleXMLElement $config SimpleXMLElement containing the MVC configuration.
	   * @return void
	   */
	  public function setConfig( SimpleXMLElement $config ) {

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
	   * @param String $name The name of the controller
	   * @return void
	   */
	  public function setDefaultController( $name ) {

	  	     $this->defaultController = $name;
	  }

	  /**
	   * Returns the name of a default controller if one is not specified
	   * in the request URI. Default is 'IndexController'.
	   * 
	   * @return String The name of the default controller
	   */
	  public function getDefaultController() {

	  	     return $this->defaultController;
	  }

	  /**
	   * Sets the name of the default action method if one is not specified
	   * in the request URI. Default is 'index'. 
	   * 
	   * @param String $name The name of the default action method
	   * @return void
	   */
	  public function setDefaultAction( $name ) {

	  	     $this->defaultAction = $name;
	  }

	  /**
	   * Returns the name of a default action method if one is not specified
	   * in the request URI. Default is 'index'.
	   * 
	   * @return String The name of the default action method
	   */
	  public function getDefaultAction() {

	  	     return $this->defaultAction;
	  }

	  /**
	   * Sets the name of the default view renderer. Default is 'PHTMLRenderer'.
	   * 
	   * @param String $renderer The name of a view renderer to use as the default
	   * @return void
	   */
	  public function setDefaultRenderer( $renderer ) {

	  	     $this->defaultRenderer = $renderer;
	  }

	  /**
	   * Returns the name of the default view renderer
	   * 
	   * @return String The default view renderer
	   */
	  public function getDefaultRenderer() {

	  	     return $this->defaultRenderer;
	  }
	  
	  /**
	   * Returns the name of the controller currently in use.
	   * 
	   * @return String The name of the controller in use by the MVC component.
	   */
	  public function getController() {
	  	
	  		 return $this->controller;
	  }

	  /**
	   * Returns the action currently being invoked.
	   * 
	   * @return String The name of the action currently being invoked.
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

	  		 $path = (isset( $_SERVER['PHP_SELF'] )) ? $_SERVER['PHP_SELF'] : '/';

		  	 preg_match( '/^.*\.php(.*)/si', $path, $matches );

	  	     if( count( $matches ) ) {

		  	  	 $mvcPieces = explode( '/', $matches[count($matches)-1] );
			  	 array_shift( $mvcPieces );

			  	 // Assign controller and action
		  	     $controller = (count($mvcPieces) > 0 && $mvcPieces[0] != '') ? $mvcPieces[0] : $this->getDefaultController(); 
		  	     $action = (count( $mvcPieces ) > 1) ? $mvcPieces[1] : $this->getDefaultAction();

		  	     // Remove controller and action from mvcPieces
		  	     array_shift( $mvcPieces );
		  	     array_shift( $mvcPieces );

		  	     // Security, Security, Security.... 
		  	     $controller = addslashes( strip_tags( $controller ) );
		  	     $action = addslashes( strip_tags( $action ) );

		  	     $this->controller = $controller;
		  	     $this->action = $action;
	  	     }

		  	 $this->controller = isset( $controller ) ? $controller : $this->getDefaultController();
		  	 $this->action = isset( $action ) ? $action : $this->getDefaultAction();

		     // Make sure controllers are loaded from the web application control directory ONLY.
	  	     if( !in_array( $this->controller, get_declared_classes() ) )
	  	     	 $this->loadController( $controller );

	  	     $oController = new $this->controller;

	  	     try {
		  	     	if( isset( $mvcPieces ) ) {
	
		  	     		$request = Scope::getRequestScope();
	
		  	     		foreach( $mvcPieces as $key => $val )
					  	     	 $mvcPieces[$key] = $request->sanitize( $val );
	
					  	Logger::getInstance()->debug( 'MVC::processRequest Invoking controller \'' . $this->controller . 
					  	     			'\', action \'' . $this->action . '\', args \'' . implode( ',', $mvcPieces  ) . '\'.' );
		  	     		call_user_func_array( array( $oController, $action ), $mvcPieces ); 
		  	     	}
		  	     	else {
	
		  	     		Logger::getInstance()->debug( 'MVC::processRequest Invoking controller \'' . $this->controller . 
					  	     			'\', action \'' . $this->action . '\'.' );
	
		  	     		$oController->$action();
		  	     	}
	  	     }
	  	     catch( Exception $e ) {

	  	     		throw new AgilePHP_Exception( $e->getMessage(), $e->getCode() );
	  	     }
	  }

	  /**
	   * Returns a new instance of the default view renderer
	   * 
	   * @return Object An instance of the default renderer
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
	   * @return Object An instance of the specified renderer
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
	   * @return Object A new instance of the custom renderer
	   */
	  public function createCustomRenderer( $renderer, $classpath='' ) {

	  	     $path = AgilePHP::getFramework()->getWebRoot() . '/classes/' . $classpath . '/' . $renderer . '.php';

	  	     Logger::getInstance()->debug( 'MVC::createDefaultRenderer loading custom renderer: ' . $renderer );

	  	     if( !file_exists( $path ) )
	  	     	 throw new AgilePHP_Exception( 'Custom renderer could not be loaded from: ' . $path );

	  	     require_once $path;
	  	     return new $renderer;
	  }

	  /**
	   * Loads a controller class only if it exists in the application controller directory.
	   * 
	   * @param String $controller The name of the controller to load.
	   * @return void
	   * @throws AgilePHP_Exception if the requested controller could not be found.
	   */
	  private function loadController( $controller ) {

	  		  $f = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'control' .
	  		  		DIRECTORY_SEPARATOR . $controller . '.php';

	  		  if( file_exists( $f ) ) {

	  		  	  __autoload( $controller );
	  		  	  return;
	  		  }

	  		  // Perform deeper scan of control directory
	  		  $f = AgilePHP::getFramework()->getWebRoot() . DIRECTORY_SEPARATOR . 'control';
		  	  $it = new RecursiveDirectoryIterator( $f );
			  foreach( new RecursiveIteratorIterator( $it ) as $file ) {
	
			   	       if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {
	
				 		   if( array_pop( explode( DIRECTORY_SEPARATOR, $file ) ) == $controller . '.php' ) {

				 		   	   __autoload( $controller );
				 		       return;
				 		   }
				       }
			  }

	  		  throw new AgilePHP_Exception( 'The requested controller could not be found.' );
	  }
}
?>