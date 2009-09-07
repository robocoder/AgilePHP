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
 * AgilePHP :: InvocationContext
 * Provides environment and method execution details t interceptors.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.interception
 * @version 0.1a
 */
class InvocationContext {

	  private $target;
	  private $method;
	  private $parameters = array();
	  private $interceptor;

	  public $proceed = false;

	  /**
	   * Initalizes the InvocationContext.
	   * 
	   * @param $target The instance which was intercepted
	   * @param $method The name of the method which was intercepted
	   * @param $parameters The method parameters which were intercepted
	   * @param $interceptor The interceptor instance which caused the interception
	   * @return void
	   */
	  public function __construct( &$target, $method, $parameters, &$interceptor ) {

	  		 $this->target = $target;
	  		 $this->method = $method;
	  		 $this->parameters = $parameters;
	  		 $this->interceptor = $interceptor;
	  }

	  /**
	   * Returns the name of the method which was intercepted.
	   * 
	   * @return The name of the intercepted method
	   */
	  public function getMethod() {

	  		 return $this->method;
	  }

	  /**
	   * Changes the name of the method. This is used by the interceptor
	   * to cause a different method to be executed than initially requested.
	   * 
	   * @param $method The name of the method to invoke
	   * @return void
	   */
	  public function setMethod( $method ) {

	  		 $this->method = $method;
	  }

	  /**
	   * Changes the method parameters. This is used by the interceptor to
	   * cause a different set of parameters to be passed into the method
	   * which gets invoked.
	   *  
	   * @param array $parameters An array of parameters to pass into the invoked method
	   * @return void
	   */
	  public function setParameters( array $parameters ) {

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
	   * Returns the target instance which was intercepted.
	   * 
	   * @return The intercepted target instance
	   */
	  public function getTarget() {

	  		 return $this->target;
	  }

	  /**
	   * Returns the instance of the interceptor annotation which caused the interception.
	   *  
	   * @return Interceptor annotation instance which caused the interception 
	   */
	  public function getInterceptor() {

	  		 return $this->interceptor;
	  }

	  /**
	   * Causes the intercepted invocation to proceed, using the method and parameters
	   * which are present in the current InvocationContext.
	   * 
	   * @return The present state of InvocationContext
	   */
	  public function proceed() {

	  		 $this->proceed = true;
	  		 return $this;
	  }
}
?>