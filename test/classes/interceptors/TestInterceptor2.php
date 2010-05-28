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
 * @package com.makeabyte.agilephp.test.classes.interceptors
 */

/**
 * Simple interceptor demostrating how to create and use an
 * interceptor in AgilePHP that accepts simple and complex data type
 * arguments.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes.interceptors
 */

#@Interceptor
class TestInterceptor2 {

	  public $param1;
	  public $param2 = array();
	  public $param3;

	  #@AroundInvoke
	  public function audit( InvocationContext $ic ) {

	  		 $class = new ReflectionClass( $ic->getTarget() );
	  		 $message = 'TestInterceptor2::audit @AroundInvoke This is what the InvocationContext interceptor state looks like: ' . print_r( $ic->getInterceptor(), true );
 	  		 Logger::debug( $message );
	  }

	  /**
	   * Returns all TestInterceptor2 fields/properties. These are set
	   * in the annotation declaration. Since this method does not contain
	   * an #@AroundInvoke annotation, it is never called during the interception.
	   * You would need to invoke this method yourself if you wanted to use it.
	   * This shows that really interceptors are still PHP classes at the end
	   * of the day, with just a little bit of magical seasoning :)
	   *  
	   * @return TestInterceptor2 fields/properties
	   */
	  public function getParams() {

	  		 echo "param1 = " . $this->param1 . 
	  		 	  ", param2 = " . implode( ",", $this->param1 ) .
	  		 	  ", param3 = " . $this->param3;
	  }
}
?>