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
 * @package com.makeabyte.agilephp
 */

/**
 * Base implementation for validators
 *  
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @abstract
 */
abstract class Validator {

	  protected $data;

	  /**
	   * Creates a new instance of Validator
	   * 
	   * @param mixed $data The data to validate
	   * @return void
	   */
	  public function __construct($data) { 

	  		 $this->data = $data;
	  }

	  /**
	   * Validates the data passed into the constructor.
	   * 
	   * @return boolean True if the data is valid, false otherwise. 
	   */
	  abstract public function validate();
}
?>