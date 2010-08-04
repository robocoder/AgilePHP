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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides common rendering implementations and defines an abstract
 * "index" method.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class BaseController {

	     protected $renderer = null;

	     /**
	      * Creates a new instance of default renderer
	      *
	      * @return void
	      */
	     public function __construct() {

	  	        $this->renderer = MVC::createDefaultRenderer();
	     }

	     /**
	      * Returns the controllers view renderer.
	      *
	      * @return void
	      */
	     protected function getRenderer() {

	     		   return $this->renderer;
	     }

	     /**
	      * Shorthand / alias for getRenderer()->set
	      *
	      * @return void
	      */
	     public function set($key, $value) {

	            $this->renderer->set($key, $value);
	     }

	     /**
	      * Shorthand / alias for getRenderer()->render
	      *
	      * @return void
	      */
	     public function render($view) {

	            $this->renderer->render($view);
	     }

	     /**
		  * Creates an instance of the specified renderer the controller will use to render views.
		  * This renderer is loaded from the AgilePHP framework.
		  *
		  * @param String $renderer The name of a renderer the controller will use to render views
		  * @return void
	      */
	     protected function createRenderer($renderer) {

	     	       $this->renderer = MVC::createRenderer($renderer);
	     }

	     /**
		  * Creates an instance of the specified custom renderer the controller will use to render views.
		  * This renderer is loaded from the application 'classes' directory.
		  *
		  * @param String $renderer The name of a custom renderer the controller will use to render views.
		  * 						Use this method to load renderers outside of the framework mvc package.
		  * @return void
	      */
	     protected function createCustomRenderer($renderer) {

	     	       $this->renderer = MVC::createCustomRenderer($renderer);
	     }

	     /**
	      * Returns the raw JavaScript contents of the AgilePHP.js file and pre-configures the library
	      * with a default AgilePHP.debug, AgilePHP.MVC.controller, and AgilePHP.MVC.action value.
	      *
	      * @param bool $debug True to enable client side AgilePHP debugging.
	      * @return void
	      */
	     public function getBaseJS($debug = false) {

	  		    $js = file_get_contents(AgilePHP::getFrameworkRoot() . '/AgilePHP.js');

	  		    if($debug) $js .= "\nAgilePHP.setDebug(true);";

	  		    $js .= "\nAgilePHP.setRequestBase('" . AgilePHP::getRequestBase() . "');";
	  		    $js .= "\nAgilePHP.MVC.setController('" . MVC::getController() . "');";
	  		    $js .= "\nAgilePHP.MVC.setAction('" . MVC::getAction() . "');";

	  		    header('content-type: application/json');
	  		    print $js;
	     }

	     /**
	      * Default controller action method.
	      *
	      * @return void
	      */
	     abstract public function index();
}