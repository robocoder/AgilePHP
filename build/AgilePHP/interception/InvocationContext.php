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
 * @package com.makeabyte.agilephp.interception
 */

/**
 * Provides environment and method execution state when an interception occurs. This
 * is passed into an interceptor so it knows about the target class, method, parameters,
 * and the #@Interceptor instance which caused the interception.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception
 */
class InvocationContext {

	  private $target;
	  private $method;
	  private $parameters = array();
	  private $interceptor;
	  private $callee;
	  private $field;
	  private $return;

	  public $proceed = false;

	  /**
	   * Initalizes the InvocationContext.
	   * 
	   * @param String $target The instance which was intercepted
	   * @param String $method The name of the method which was intercepted
	   * @param array $parameters The method parameters which were intercepted
	   * @param Object $interceptor The interceptor instance which caused the interception
	   * @param String $field The name of the field/property which was intercepted
	   * @return void
	   */
	  public function __construct($target, $method, $parameters, $interceptor, $field = null) {

	  		 $this->target = $target;
	  		 $this->method = $method;
	  		 $this->parameters = $parameters;
	  		 $this->interceptor = $interceptor;
	  		 $this->field = $field;

	  		 // PHP stack state that caused the interception
	  		 $backtrace = debug_backtrace();
	  		 $this->callee = (isset($backtrace[2]) ? $backtrace[2] : $backtrace[1]);
	  }

	  /**
	   * Returns the name of the intercepted method.
	   * 
	   * @return String The name of the intercepted method
	   */
	  public function getMethod() {

	  		 return $this->method;
	  }

	  /**
	   * Transforms the state of the method calls. This is used by interceptors
	   * to cause a different method to be executed than the one initially
	   * called.
	   * 
	   * @param String $method The name of the method to invoke
	   * @return void
	   */
	  public function setMethod($method) {

	  		 $this->method = $method;
	  }

	  /**
	   * Transforms the state of method parameters. This is used by interceptors
	   * to cause a different set of parameters to be passed into the method
	   * being invoked.
	   *  
	   * @param array $parameters An array of parameters to pass into the invoked method
	   * @return void
	   */
	  public function setParameters(array $parameters) {

	  		 $this->parameters = $parameters;
	  }

	  /**
	   * Returns the parameters being passed into the intercepted method.
	   * 
	   * @return An array containing each of the parameters being passed
	   * 		 to the intercepted method.
	   */
	  public function getParameters() {

	  		 return $this->parameters;
	  }

	  /**
	   * Returns the intercepted target instance.
	   * 
	   * @return The intercepted target instance
	   */
	  public function getTarget() {

	  		 return $this->target;
	  }

	  /**
	   * Sets the intercepted target instance
	   *
	   * @param Object $instance The intercepted target instance
	   * @return void
	   */
	  public function setTarget($instance) {

	  		 $this->target = $instance;
	  }

	  
	  /**
	   * Returns the instance of the interceptor annotation which caused the interception.
	   *  
	   * @return Interceptor The instance of the annotation which caused the interception 
	   */
	  public function getInterceptor() {

	  		 return $this->interceptor;
	  }

	  /**
	   * Returns the callee PHP stack state. This provides information about the PHP file
	   * and calls that caused the interception.
	   * 
	   * @return void
	   */
	  public function getCallee() {

	  		 return $this->callee;	  		 
	  }

	  /**
	   * Returns the name of the field if a property/field level annotation caused the interception.
	   * 
	   * @return String The name of the class field.
	   */
	  public function getField() {

	  		 return $this->field;
	  }

	  /**
	   * Sets the value which the intercepted method call returned
	   * 
	   * @param mixed The value which the intercepted method call returned
	   */
	  public function setReturn($return) {

	  		 $this->return = $return;
	  }

	  /**
	   * Returns the value which the intercepted method call returned
	   * 
	   * @return The value which the intercepted method call returned
	   */
	  public function getReturn() {

	  		 return $this->return;
	  }
	  
	  /**
	   * Causes the intercepted invocation to continue, using the method and parameters
	   * contained in the state of the InvocationContext instance. If this method is not
	   * invoked during an interception, the interceptor will not execute the intercepted
	   * call. To disregard an intercepted call (not invoke it), simply do not call this
	   * method during the interception.
	   * 
	   * @return The present state of the InvocationContext instance
	   */
	  public function proceed() {

	  		 $this->proceed = true;
	  		 return $this;
	  }
}
?>