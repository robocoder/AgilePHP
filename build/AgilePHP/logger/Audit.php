<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp.logger
 */

/**
 * Logs a call state context both before and after a method invocation.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.logger
 * <code>
 * class MyClass {
 * 
 * 		 // Calls to this function are logged including class, method, parameters, and return value.
 * 		 #@Audit
 * 		 public function doSomething($param1, $param2) {
 * 
 * 				return 'something useful';
 * 		 }
 * }
 * </code>
 */
#@Interceptor
class Audit {

	  /**
	   * Sets the logging level
	   * 
	   * @var string $level Optional logging level (info|warn|error|debug). Defaults to 'debug'.
	   */
	  public $level = 'debug';

	  /**
	   * Flag indicating whether or not to display detailed information about the invocation
	   * 
	   * @var bool $verbose True to display the entire invocation context, false to display class name,
	   * 				    method name, and arguments. The return value will also be captured and logged.
	   * 				 	Defaults to false (not verbose).
	   */
	  public $verbose = false;

	  private $valid = array('info', 'warn', 'error', 'debug');

	  #@AroundInvoke
	  public function logInvocation(InvocationContext $ic) {

	  		 $level = (in_array($this->level, $this->valid)) ? $this->level : 'debug';
	  		 if($this->verbose) {

	  		 	 Log::$level('#@Audit::logInvocation');
	  		 	 Log::$level($ic);
	  		 	 return $ic->proceed();	 
	  		 }

	  		 $callee = $ic->getCallee();
	  		 $message = 'class = \'' . $callee['class'] . '\', method = \'' . $callee['function'] . '\', args = \'' . implode(',', $ic->getParameters()) . '\'';

	  		 Log::$level('#@Audit::logInvocation ' . $message); 
	  		 return $ic->proceed();
	  }

	  #@AfterInvoke
	  public function logReturn(InvocationContext $ic) {

	  		 $level = (in_array($this->level, $this->valid)) ? $this->level : 'debug';
	  		 if($this->verbose) {

	  		 	 Log::$level('#@Audit::logReturn');
	  		 	 Log::$level($ic);
	  		 	 return $ic->proceed();	 
	  		 }

	  		 $return = $ic->getReturn();
	  		 Log::$level('#@Audit::logReturn');
	  		 Log::$level((($return) ? $return : '(null)'));

	  		 return $ic->proceed();
	  }
}
?>