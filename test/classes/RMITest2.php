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
 * Another simple class exposed to client side javascript via AgilePHP remoting component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */
class RMITest2 {

	  public function __construct() { }

	  #@RemoteMethod
	  public function testme( $param1 = null, $param2 ) {

	  		 $o = new stdClass;
	  		 $o->param1 = $param1;
	  		 $o->param2 = $param2;

	  		 return $o;
	  }

	  #@RemoteMethod
	  public function testme2( $param1 = null, $param2 ) {
	  	
	  		 $o = new stdClass;
	  		 $o->testme2 = 'this does something 2';

	  		 return $o;
	  }
	  
	  #@RemoteMethod
	  public function testme3() {

	  		 $o = new stdClass;
	  		 $o->testme3 = 'this does something else';

	  		 return $o;
	  }
	  
	  public function testme4() { }
}
?>