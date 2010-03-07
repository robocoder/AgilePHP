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
 * @version 0.1a
 */
class RMITest {

	  private $test1;
	  private $test2;
	  private $test3;

	  public function __construct( $test1, $test2 = null, $test3 = 'test' ) {

	  		 $this->test1 = $test1;
	  		 $this->test2 = $test2;
	  		 $this->test3 = $test3;
	  }

	  #@RemoteMethod
	  public function testme( $param1 = null, $param2 ) {

	  		 echo '$this->test1 = ' . $this->test1 . "\n";
	  		 echo '$this->test2 = ' . $this->test2 . "\n";
	  		 echo '$this->test3 = ' . $this->test3 . "\n";

	  		 echo '$param1 = ' . ($param1 ? $param1 : 'null') . "\n";
	  		 echo '$param2 = ' . $param2 . "\n";
	  		 
	  		 return 'testme done';
	  }

	  #@RemoteMethod
	  #@Restrict( role = 'foo' )
	  public function testme2( $param1 = null, $param2 ) {
	  	
	  		 echo 'this does something 2';
	  		 
	  		 return 'testme2 done';
	  }

	  #@RemoteMethod
	  public function testme3() {

	  		 echo 'this does something else';
	  		 
	  		 return 'testme3 done';
	  }

	  public function testme4() { }

	  #@RemoteMethod
	  public function setTest2( $val ) {

	  		 $this->test2 = $val;
	  		 echo 'set test2 to: ' . $val;
	  }
	  
	  #@RemoteMethod
	  public function getTest2() {

	  		 return $this->test2;
	  }

	  #@RemoteMethod
	  public function show() {

	  		 print_r( $this );
	  		 return 'RMITest::show';
	  }
}
?>