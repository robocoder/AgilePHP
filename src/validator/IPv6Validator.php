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
 * @package com.makeabyte.agilephp.validator
 */

/**
 * Validates IPv4 addresses.
 *  
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.validator
 */
class IPv6Validator extends Validator {

	  /**
	   * Validates the specified data by ensuring it is a valid IP address.
	   * 
	   * @return bool True if the specified data is a valid IP address, false otherwise.
	   */
	  public function validate() {

	  		 return filter_var($this->data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	  }
}
?>