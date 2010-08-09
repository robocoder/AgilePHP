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
 * A test class used by the CacheController to test caching in AgilePHP.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */
class MockCacher {

	  private $markup;

	  public function __construct() {

	  		 $this->resetMarkup();
	  }

	  /**
	   * Outputs the value of the markup property with a 1 second cache expiry time. 
	   * 
	   */
	  #@Cache(minutes = 1)
	  public function expires() {

	  		 return $this->getMarkup();
	  }

	  /**
	   * Outputs the value of the markup property with a cache time of 'never expire'.
	   */
	  #@Cache
	  public function neverExpires() {

	  		 return $this->getMarkup();
	  }

	  /**
	   * Sets the markup property used as an output value from the cached method.
	   * 
	   * @param mixed $value The value to have the cached method output
	   * @return void
	   */
	  public function setMarkup($value) {

	  		 $this->markup = $value;
	  }

	  /**
	   * Returns the value of the markup property.
	   * 
	   * @return mixed The value of the markup property.
	   */
	  public function getMarkup() {

	  		 return $this->markup;
	  }

	  /**
	   * Resets the markup property back to default value.
	   * 
	   * @return void
	   */
	  public function resetMarkup() {

	  		 $this->markup = "This is some default output to get cached.\n";
	  }
}
?>