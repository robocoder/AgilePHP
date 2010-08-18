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
 * Annotation used to designate a class as an AgilePHP interceptor.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.interception
  * <code>
 * #@Interceptor
 * class MyInterceptor { }
 * 
 * // This is a class level interceptor that gets invoked just before MyInterceptorImpl is created.
 * #@MyInterceptor
 * class MyInterceptorImpl {
 * 
 * 		 // This is a property level interceptor that gets invoked upon construction
 * 		 #@MyInterceptor
 * 		 private $foo;
 * 
 * 		 // This is a method level interceptor that gets invoked before the method is called.
 * 		 #@MyInterceptor
 * 		 public function doSomething() { }
 * }
 * </code>
 * 
 * <code>
 * #@Interceptor
 * class MyInterceptor {
 * 
 * 		 // This interceptor accepts one parameter named 'param'
 * 		 public $param;
 * }
 * 
 * class MyInterceptorImpl {
 * 
 * 		 #@MyInterceptor(param = 'this is a parameter string value')
 * 		 public function doSomething() { }
 * 
 * 		 #@MyInterceptor(param = { 'this', 'key2' => 'is', 3 => 'an', 'key4' => 'array parameter value' })
 * 		 public function doSomething2() { }
 * 
 * 		 #@MyInterceptor(param = new MyClass())
 * 		 public function doSomething3() { }
 * 
 * 		 #@MyInterceptor(param = MySingleton::getInstance())
 * 		 public function doSomething4() { }
 * }
 * </code>
 */
class Interceptor { }
?>