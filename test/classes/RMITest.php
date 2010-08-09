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
 * @package com.makeabyte.agilephp.test.classes
 */

/**
 * A simple class exposed to client side javascript via AgilePHP remoting component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */
class RMITest {

	  private $test1;
	  private $test2;
	  private $test3;

	  public function __construct($test1, $test2 = null, $test3 = 'test') {

	  		 $this->test1 = $test1;
	  		 $this->test2 = $test2;
	  		 $this->test3 = $test3;
	  }

	  #@RemoteMethod
	  public function testme($param1 = null, $param2) {

	  		 $o = new stdClass;
	  		 $o->test1 = $this->test1;
	  		 $o->test2 = $this->test2;
	  		 $o->test3 = $this->test3;
	  		 $o->param1 = ($param1 ? $param1 : 'null');
	  		 $o->param2 = $param2;

	  		 return $o;
	  }

	  #@RemoteMethod
	  #@Restrict(role = 'foo')
	  public function testme2($param1 = null, $param2) {

	  		 $o = new stdClass;
	  		 $o->testme2 = 'this does something 2 as long as the user is logged in and belongs to role "foo"';

	  		 return $o;
	  }

	  #@RemoteMethod
	  public function testme3() {

	  		 $o = new stdClass;
	  		 $o->testme2 = 'this does something else';

	  		 return $o;
	  }

	  public function testme4() { }

	  #@RemoteMethod
	  public function setTest2($val) {

	  		 $this->test2 = $val;

	  		 $o = new stdClass;
	  		 $o->setTest2 = 'set test2 to: ' . $val;

	  		 return $o;
	  }
	  
	  #@RemoteMethod
	  public function getTest2() {

	  		 $o = new stdClass;
	  		 $o->getTest2 = $this->test2;

	  		 return $o;
	  }

	  #@RemoteMethod
	  public function show() {

	  		 $o = new stdClass;
	  		 $o->show = print_r($this, true);

	  		 return $o;
	  }
}
?>