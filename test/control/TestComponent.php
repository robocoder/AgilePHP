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
 * @package com.makeabyte.agilephp.test.component
 */

// Import custom PHTMLRenderer from TestComponent. Since we
// are using namespaces, this PHTMLRenderer does not
// clash with AgilePHP.mvc.PHTMLRenderer
AgilePHP::import( 'TestComponent.classes.PHTMLRenderer' );

/**
 * A test component for AgilePHP Framework
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte inc,
 * @package com.makeabyte.agilephp.test.component
 */
class TestComponent extends Component {

      public function __construct() {

	     	 parent::__construct();
      }

      /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
      public function index() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'index', func_get_args() );

      		 /*
	     	 $renderer = new TestComponent\PHTMLRenderer();
	     	 $renderer->set( 'title', 'TestComponent :: Home' );
	     	 $renderer->set( 'content', 'Welcome to the TestComponent home page!' );
	     	 $renderer->render( 'index' );
	     	 */
      }

	  public function add() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'add', func_get_args() );
      }

      public function edit() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'edit', func_get_args() );
      }
      
	  public function read() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'read', func_get_args() );
      }
      
	  public function search() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'search', func_get_args() );
      }
      
	  public function sort() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'sort', func_get_args() );
      }
      
	  public function persist() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'persist', func_get_args() );
      }
      
	  public function merge() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'merge', func_get_args() );
      }
      
	  public function delete() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'delete', func_get_args() );
      }
      
	  public function setPrimaryKeys() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'setPrimaryKeys', func_get_args() );
      }
      
	  public function setModelValues() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'setModelValues', func_get_args() );
      }

	  public function getCastedValue() {

      		 parent::dispatch( new TestComponent\control\Table1Controller(), 'getCastedValue', func_get_args() );
      }
}
?>