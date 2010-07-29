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
 * Annotation which causes methods within an
 * interceptor (denoted by a class level #@Interceptor annotation)
 * to be invoked when an interceptoion occurs.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception
  * <code>
 * #@Interceptor
 * class MyInterceptor {
 * 
 * #@AroundInvoke
 * public function aMethodICanNameAnything(InvocationTarget $ic) {
 * 
 * 		  // Inspect the invocation context for the call stack and state
 * 		  // of the application and perform some kind of aspect logic.
 * 		  //
 * 		  // If you call $ic->proceed(), the interceptor will return execution
 * 		  // back to "regular scheduled programming". If $ic->proceed() is not
 * 		  // called, the application will come to a close with the logic defined
 * 		  // in this interceptor.
 * }
 * 
 * #@AroundInvoke
 * public function anotationCausesMeToBeInvoked(InvocationContext $ic) {
 * 
 * 		  // The #@AroundInvoke annotation causes methods within the interceptor
 * 		  // to be invoked. You can decorate as many methods with #@AroundInvoke
 * 		  // inside the class as you want.
 * }
 * }
 * </code>
 */
class AroundInvoke { }
?>