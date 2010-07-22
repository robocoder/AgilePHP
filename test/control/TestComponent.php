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

      /**
	   * (non-PHPdoc)
	   * @see src/mvc/BaseController#index()
	   */
      public function index() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function add() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

      public function edit() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function read() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function search() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function sort() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function persist() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function merge() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function delete() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function setPrimaryKeys() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function setModelValues() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }

	  public function getCastedValue() {

      		 parent::delegate(new TestComponent\control\Table1Controller());
      }
}
?>