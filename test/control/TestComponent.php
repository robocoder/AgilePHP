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

             $navigation = '<p>
             				  <a href="' . AgilePHP::getRequestBase() . '/TestComponent/table1">Table 1</a>
             				  <a href="' . AgilePHP::getRequestBase() . '/TestComponent/table2">Table 2</a>
             				</p>';

         	 $renderer = new TestComponent\PHTMLRenderer();
	     	 $renderer->set('title', 'TestComponent :: Home');
	     	 $renderer->set('content', '<b>Welcome to the TestComponent home page!</b>' . $navigation);
	     	 $renderer->render('index');
      }

      /**
       * Shows TestPhar table1 as configured in component.xml <orm>
       * 
       * @return void
       */
      public function table1() {

             parent::delegate(new TestComponent\control\Table1Controller());
      }

      /**
       * Shows TestPhar table2 as configured in component.xml <orm>.
       * 
       * @return void
       */
      public function table2() {

             parent::delegate(new TestComponent\control\Table2Controller());
      }

      /**
       * Shows the component state using print_r($this)
       * 
       * @return void
       */
      public function debug() {

             print_r($this);
      }
}
?>