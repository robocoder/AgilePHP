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
 * A class used by the test package to test interceptions in AgilePHP.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */

#@TestInterceptor2( param1 = "test", param2 = { key1 = "test2", "test3", key2 = "test4" }, param3 = new Role() )
class MockInterceptionTarget {

	  #@Logger
	  public $logger;

	  private $property1;

	  public function __construct() { }

	  /**
	   * Property1 mutator
	   * 
	   * @param $value The value
	   * @return void
	   */
	  #@TestInterceptor
	  #@TestInterceptor2( param1 = Crypto::getInstance(), param2 = { key1 = "test2", "test3", key2 = "test4" }, param3 = new Role() )
	  public function setProperty1( $value ) {

	  		 Log::debug( 'MockInterceptionTarget::setProperty1 with value \'' . $value . '\'.' );
	  		 $this->property1 = $value;
	  }

	  /**
	   * Property1 accessor
	   * 
	   * @return Property1 value
	   */
	  public function getProperty1() {

	  		 return $this->property1;
	  }

	  /**
	   * Restricted method. Only users with a role of
	   * 'admin' can invoke this method.
	   * 
	   * @return The string 'restrictedMethod'
	   * @throws AccessDeniedException
	   */
	  #@Restrict( role = 'admin' )
	  public function restrictedMethod() {

	  		 Log::debug( 'MockInterceptionTarget::restrictedMethod invoked' );
	  }

	  /**
	   * Secure method. Only logged in users can invoke
	   * this method.
	   * 
	   * @return The string 'secureMethod'
	   */
	  #@LoggedIn
	  public function secureMethod() {

	  		 Log::debug( 'MockInterceptionTarget::secureMethod invoked' );
	  }

	  /**
	   * Returns the injected #@Logger instance from LogFactory
	   */
	  public function getLogger() {

	  		 return $this->logger;
	  }
}
?>