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

require 'mvc/BaseController.php';
require 'mvc/BaseRenderer.php';

/**
 * Model-View-Control (MVC) component
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
final class MVC {

	  private static $defaultController = 'IndexController';
	  private static $defaultAction = 'index';
	  private static $defaultRenderer = 'PHTMLRenderer';
	  private static $controller;
	  private static $action;
	  private static $parameters;
	  private static $sanitize = true;
	  private static $cacheables;

	  private function __construct() { }
	  private function __clone() {}

	  /**
	   * Initalizes the MVC component with agilephp.xml configuration.
	   *
	   * @param SimpleXMLElement $config SimpleXMLElement containing the MVC configuration.
	   * @return void
	   * @static
	   */
	  public static function init($controller, $action, $renderer, $sanitize, $cacheables) {

	  		 if($controller) self::$defaultController = $controller;
	  		 if($action) self::$defaultAction = $action;
	  		 if($renderer) self::$defaultRenderer = $renderer;
	  		 if($sanitize) self::$sanitize = $sanitize;
	  		 if($cacheables) self::$cacheables = $cacheables;
	  }

	  /**
	   * Sets the name of the default controller which is used if one is not
	   * specified in the request URI. Default is 'IndexController'.
	   *
	   * @param String $name The name of the controller
	   * @return void
	   * @static
	   */
	  public static function setDefaultController($name) {

	  	     self::$defaultController = $name;
	  }

	  /**
	   * Returns the name of a default controller if one is not specified
	   * in the request URI. Default is 'IndexController'.
	   *
	   * @return String The name of the default controller
	   * @static
	   */
	  public static function getDefaultController() {

	  	     return self::$defaultController;
	  }

	  /**
	   * Sets the name of the default action method if one is not specified
	   * in the request URI. Default is 'index'.
	   *
	   * @param String $name The name of the default action method
	   * @return void
	   * @static
	   */
	  public static function setDefaultAction($name) {

	  	     self::$defaultAction = $name;
	  }

	  /**
	   * Returns the name of a default action method if one is not specified
	   * in the request URI. Default is 'index'.
	   *
	   * @return String The name of the default action method
	   * @static
	   */
	  public static function getDefaultAction() {

	  	     return self::$defaultAction;
	  }

	  /**
	   * Sets the name of the default view renderer. Default is 'PHTMLRenderer'.
	   *
	   * @param String $renderer The name of a view renderer to use as the default
	   * @return void
	   * @static
	   */
	  public static function setDefaultRenderer($renderer) {

	  	     self::$defaultRenderer = $renderer;
	  }

	  /**
	   * Returns the name of the default view renderer
	   *
	   * @return String The default view renderer
	   * @static
	   */
	  public static function getDefaultRenderer() {

	  	     return self::$defaultRenderer;
	  }

	  /**
	   * Returns the name of the controller currently in use.
	   *
	   * @return String The name of the controller in use by the MVC component.
	   * @static
	   */
	  public static function getController() {

	  		 return self::$controller;
	  }

	  /**
	   * Returns the action currently being invoked.
	   *
	   * @return String The name of the action currently being invoked.
	   * @static
	   */
	  public static function getAction() {

	  		 return self::$action;
	  }

	  /**
	   * Returns the action parameters specified in the request
	   *
	   * @return Array Parameters passed to the invoked action
	   * @static
	   */
	  public static function getParameters() {

	  		 return self::$parameters;
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
	   * @static
	   */
	  public static function dispatch($controller = null, $action = null) {

	  		 $path = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '/';

	  		 if(self::$cacheables && ($cacher = AgilePHP::getCacher())) {

	  		    $cacheKey = 'AGILEPHP_MVC_' . $path;
	  		    if($cacher->exists($cacheKey)) {

		           $data = $cacher->get($cacheKey);
		           if($data['contentType']) header('content-type: ' . $data['contentType']);
	  		       echo $data['html'];
	  		       return;
	  		    }
	  		 }

	  		 if(!$controller) {

    	  		// Capture everything after the first occurrence of '.php'
    		  	preg_match('/^.*?\.php(.*)/si', $path, $matches);

    	  	    if(isset($matches[0])) {

    		  	   $parameters = explode('/', $matches[count($matches)-1]);

    			   // Assign controller and action
    		  	   $controller = isset($parameters[1]) ? $parameters[1] : self::$defaultController;
    		  	   $action = isset($parameters[2]) ? $parameters[2] : self::$defaultAction;

    		  	   // Remove empty element, controller and action values
    		  	   array_splice($parameters, 0, 3);

    		  	   // Security, Security, Security....
    		  	   $controller = addslashes(strip_tags($controller));
    		  	   $action = addslashes(strip_tags($action));
    	  	    }
	  		}

	  	    if(!$controller) $controller = self::$defaultController;
	  	    if(!$action) $action = self::$defaultAction;
	  	    if(!isset($parameters)) $parameters = array();

	  	    self::$controller = $controller;
            self::$action = $action;
            self::$parameters = $parameters;

	  	    if(!class_exists($controller, false)) {

	  	        $webroot = AgilePHP::getWebRoot();

	  	        // PHAR support
	  	     	$phar = $webroot . DIRECTORY_SEPARATOR . 'control' .
	  		  				DIRECTORY_SEPARATOR . $controller . '.phar';

	  		  	if(file_exists($phar)) {

	  		  	   require $phar;
	  		  	   $oController = new $controller;
	  		  	}
	  		  	else // web application controller
	  	     	   $oController = self::loadController($webroot, $controller);
	  	     }
	  	     else // controller already loaded
	  	        $oController = new $controller;

	  	     // Sanitize action arguments unless configured otherwise
             if(self::$sanitize)
     		    foreach($parameters as $key => $val)
		  	 	   $parameters[$key] = addslashes(strip_tags($val));

	  	     // Make sure requested action method exists
		     if(!method_exists($controller, $action))
		  	    throw new FrameworkException('The specified action \'' . $action . '\' does not exist.', 102);

		  	 // Cache the output if caching is enabled
		     if(isset($cacher) && self::$cacheables)
		        if(self::cacheDispatch($oController, $cacher, $cacheKey)) return;

		     // Execute the controller action - caching is not enabled
		     call_user_func_array(array($oController, $action), $parameters);
	  }

	  /**
	   * Returns a new instance of the default view renderer
	   *
	   * @return Object An instance of the default renderer
	   * @static
	   */
	  public static function createDefaultRenderer() {

	  	     $path = AgilePHP::getFrameworkRoot() . '/mvc/' . self::$defaultRenderer . '.php';

	  	     if(!file_exists($path))
	  	     	throw new FrameworkException('Default framework renderer could not be loaded from: ' . $path, 103);

	  	     require_once $path;
	  	     return new self::$defaultRenderer;
	  }

	  /**
	   * Returns a new instance of the specified view renderer
	   *
	   * @return Object An instance of the specified renderer
	   * @static
	   */
	  public static function createRenderer($renderer) {

	  	     $path = AgilePHP::getFrameworkRoot() . '/mvc/' . $renderer . '.php';

	  	     Log::debug('MVC::createRenderer loading renderer: ' . $renderer);

	  		 if(!file_exists($path))
	  	     	throw new FrameworkException('Framework renderer could not be loaded from: ' . $path, 104);

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
	   * @static
	   */
	  public static function createCustomRenderer($renderer, $classpath='') {

	  	     $path = AgilePHP::getWebRoot() . '/classes/' . $classpath . '/' . $renderer . '.php';

	  	     Log::debug('MVC::createDefaultRenderer loading custom renderer: ' . $renderer);

	  	     if(!file_exists($path))
	  	     	 throw new FrameworkException('Custom renderer could not be loaded from: ' . $path, 105);

	  	     require_once $path;
	  	     return new $renderer;
	  }

	  /**
	   * Dispatches the request to the appropriate controller/action using output buffering
	   * to capture and cache the response.
	   * 
	   * @param BaseController $oController The controller instance to dispatch the request to
	   * @param CacheProvider $cacher The cache provider instance responsible for handling the cached data
	   * @param string $cacheKey The cache key used to store and retrieve the requested data
	   * @return void
	   */
	  private static function cacheDispatch(BaseController $oController, $cacher, $cacheKey) {

	          foreach(self::$cacheables as $cacheable) {

		            if($cacheable->attributes()->controller == self::$controller &&
		               ($cacheable->attributes()->action == self::$action || $cacheable->attributes()->action == '*')) {

		                   // Cache according to parameter values if configured
		                   if($parameters = $cacheable->attributes()->parameters) {

		                      $xmlParams = explode('/', $parameters);
		                      if(count($xmlParams) != count(self::$parameters)) return false;

		                      for($i=0; $i<count(self::$parameters); $i++) {

    		                      // Regex comparison/support
    		                      if($xmlParams[$i][0] == '^') {

    		                         if(!preg_match('/' . $xmlParams[$i] . '/', self::$parameters[$i]))
    		                            $return = true;
    		                         else {

    		                            $return = false;
    		                            break;
    		                         }

    		                         continue;
    		                      }
    		                      // String literal comparison
    		                      else {

    		                          if(self::$parameters[$i] != $xmlParams[$i])
    		                             $return = true;
    		                          else {
    		                              
    		                             $return = false;
    		                             break;
    		                          }
    		                          // $return = (self::$parameters[$i] == $xmlParams[$i]);
    		                      }
		                      }

		                      if($return) return false;
		                   }

		                   // Use content-type header if configured
		                   if($contentType = (string)$cacheable->attributes()->contentType) {

		                      if($contentType == 'HTTP_ACCEPT') {

                    	  		  $clientMimes = array();
                    			  foreach(explode(',', $_SERVER['HTTP_ACCEPT']) as $mimeType) {
                    
                    					  $item = explode(';', $mimeType);
                    					  $clientMimes[$item[0]] = floatval(array_key_exists(1, $item) ? substr($item[1], 2) : 1);
                    			  }
                    			  arsort($clientMimes);
                    			  $contentType = array_pop($clientMimes);
		                      }

		                      header('content-type: ' . $contentType);
		                   }

		                   $ttl = (int)$cacheable->attributes()->ttl;

		                   ob_start();
              	     	   call_user_func_array(array($oController, self::$action), self::$parameters);
              	     	   $data = array('html' => ob_get_flush(), 'contentType' => (isset($contentType) ? $contentType : null));
              	     	   $cacher->set($cacheKey, $data, $ttl);
              	     	   return true;
		               }
		        }
	  }

	  /**
	   * Loads the requested controller / class ONLY if it exists in the web application controller directory.
	   *
	   * @param String $controller The name of the controller to load.
	   * @return void
	   * @throws FrameworkException if the requested controller could not be found.
	   * @static
	   */
	  private static function loadController($webroot, $controller) {

	          // PHP namespace support
	          $controller = str_replace('\\\\', '\\', $controller);
	          $namespace = explode('\\', $controller);
	  		  $className = array_pop($namespace);
	  		  $namespace = implode(DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;

	  		  // Load the controller from the application control directory
	  		  if(file_exists($webroot . DIRECTORY_SEPARATOR . 'control' . DIRECTORY_SEPARATOR .
	  		              $namespace . $className . '.php'))
	  		     return new $controller;

	  		  // Load the controller from component control directory
	  		  if(strpos($controller, '\\') !== false) {

	  		     $path = $webroot . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR
  		                 . $namespace . $className . '.php';

  		         if(file_exists($path))
	  		        return new $controller;
	  		  }

	  		  // Perform deep scan of control directory
		  	  $it = new RecursiveDirectoryIterator($webroot . DIRECTORY_SEPARATOR . 'control');
			  foreach(new RecursiveIteratorIterator($it) as $file) {

			   	      if(substr($file, -1) != '.' && substr($file, -2) != '..') {

			   	       	 $pieces = explode(DIRECTORY_SEPARATOR, $file);
			   	      	 $item = array_pop($pieces);

			   	      	 if($item == $controller . '.php')
				 		    return new $controller;
				      }
			  }

	  		  throw new FrameworkException('The requested controller \'' . $controller . '\' could not be found.', 106);
	  }
}
?>