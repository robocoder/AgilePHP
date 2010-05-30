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
 * Responsible for performing AJAX calls. Can render to either JSON or XML.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
class AJAXController extends BaseController {

	  private $renderer;

	  public function __construct() {

	  		 $this->createRenderer( 'AJAXRenderer' );

	  		 // CSFR token seems to cause some interference with the javascript code - needs to be ironed out!
	  		 //Scope::getRequestScope()->createToken();
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
	  public function index() {

	  		 $stdClass = new stdClass();
	  		 $stdClass->result = 'Some text from the AJAXController';
	  		 $this->getRenderer()->render( $stdClass );
	  }

	  public function xml() {

	  		 $this->getRenderer()->setOutput( 'xml' );
	  		 $this->getRenderer()->render( $this->getMockData() );
	  }

	  public function testUpdater() {

	  		 echo '<div style="color: #FF0000;">Some text from the AJAXController</div>';
	  }

	  public function formSubmit() {

	  		 $stdClass = new stdClass();
	  		 $stdClass->result = implode( ',', Scope::getRequestScope()->getParameters() );

	  		 $this->getRenderer()->render( $stdClass );
	  }

	  /**
	   * Server-side logic which demonstrates jQuery integration with AgilePHP
	   * using AJAXRenderer to output JSON.
	   * 
	   * @return void
	   */
	  public function jqueryExample() {

	  		 $pm = new PersistenceManager();
	  		 $models = $pm->find( new User(), true );

	  		 $this->getRenderer()->renderNoHeader( $models );
	  }
	  
	  private function getMockData() {

	  		  $o = new stdClass;
	  		  $o->prop1 = 'test1';
	  		  $o->prop2 = 'test2';

	  		  return $o;
	  }
}
?>