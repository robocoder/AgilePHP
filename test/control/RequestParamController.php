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
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Tests the #@RequestParam interceptor (sets the annotated property with the
 * corresponding form field value).
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 * @version 0.1a
 */
class RequestParamController extends BaseController {

	  #@RequestParam
	  public $name;

	  #@RequestParam( name = 'comments', sanitize = false )
	  public $comments;

	  #@In( class = Logger::getInstance() )
	  public $logger;

	  public function __construct() {

	  		 parent::__construct();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  		 $this->getRenderer()->render( 'request-param-example' );
	  }

	  /**
	   * Displays the submitted form values set by the #@RequestParam interceptor
	   * 
	   * @return void
	   */
	  public function process() {

	  		 echo '<hr>';
	  		 echo 'Name: ' . $this->name . '<br>';
	  		 echo 'Comments: ' . $this->comments . '<br>';

	  		 $this->logger->debug( 'RequestParamController::process Name = ' . $this->name . ', comments: ' . $this->comments );
	  }
}
?>