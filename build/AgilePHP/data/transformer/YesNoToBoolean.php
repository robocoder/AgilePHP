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
 * @package com.makeabyte.agilephp.data.transformer
 */

/**
 * Transforms a "Yes" or "No" string to a SQL bit type
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.transformer
 */
class YesNoToBoolean implements DataTransformer {

	  /**
	   * Transforms a "Yes" or "No" string to a "1" or "0"
	   * 
	   * @param string $data The "Yes" or "No" string
	   * @return int "1" if the string was "Yes", "0" otherwise
	   */
	  public static function transform($data) {

             if($data == 1) return 1;

	  		 return (strtolower($data) == 'yes') ? 1 : 0;
	  }
}
?>