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
 * @package com.makeabyte.agilephp.interception
 */

/**
 * Responsible for performing reflection on intercepted classes. Standard
 * PHP reflection used on an intercepted class will really be reflecting
 * InterceptorProxy and not the intercepted class. This reflector will perform
 * requested operations on the intercepted class, not the proxy.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.interception
 * @version 0.1a
 */
class InterceptedClass extends ReflectionClass {

	  public function __construct( $class ) {

	  		 $c = is_object( $class ) ? $class->getInterceptedInstance() : preg_replace( '/_Intercepted/', '', $class );
	  		 parent::__construct( $c );
	  }

	  public function getName() {

	  		 return preg_replace( '/_Intercepted/', '', parent::getName() );
	  }
}
?>