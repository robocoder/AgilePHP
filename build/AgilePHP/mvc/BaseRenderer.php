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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base rendering implementation
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @abstract
 */
abstract class BaseRenderer {

		 private $store = array();

		 /**
		  * Helper method to allow controllers to set variables
		  * which are dumped to a view during rendering.
		  * 
		  * @param String $key The variable name
		  * @param mixed $value The variable value
		  * @return void
		  */
	  	 public function set($key, $value) {

	  	      	$this->store[$key] = $value;
      	 }

      	 /**
      	  * Returns the value for the specified key.
      	  * 
      	  * @param String $key The key to retrieve the value from
      	  * @return mixed The value stored in the $key index
      	  */
      	 public function get($key) {

      	 		return $this->store[$key];
      	 }

      	 /**
      	  * Returns the store which contains variable names with their associated
      	  * values set by one or more controllers.
      	  *  
      	  * @return void
      	  */
      	 public function getStore() {

      	 	    return $this->store;
      	 }

         abstract public function render($view);
}
?>