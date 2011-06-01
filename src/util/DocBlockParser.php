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
 * Utility class / helper class to assist in parsing PHP-doc comments
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.util
 */
class DocBlockParser {

	/**
	 * Extracts the data type from the PHP-doc comments block for the specified property
	 * 
	 * @param ReflectionProperty $property A PHP ReflectionProperty instance representing
	 *        the property to extract the data type from.
	 * @return string The extracted PHP data type
     */
	public static function getPropertyType(ReflectionProperty $property) {
	  	preg_match('/@var\\s*([A-Za-z_][A-Za-z0-9_]*)<?.*\\s/', $property->getDocComment(), $matches);
	  	return (isset($matches[1])) ? trim($matches[1]) : 'undefined';  		  	  
    }

	/**
     * Extracts the data type from the PHP-doc comments block for the specified method
     * 
     * @param ReflectionMethod $method A PHP ReflectionMethod instance representing
     *        the method which has the parameter to extract the data type from
     * @param ReflectionParameter $param A PHP ReflectionMethod instance representing
     *        the parameter to extract the data type from.
     * @return string The extracted PHP data type 
	 */
	public static function getParameterType(ReflectionMethod $method, ReflectionParameter $param) {
		preg_match('/@param\\s*(.*?\\[?\\]?)\\s*\$' . $param->name . '/i', $method->getDocComment(), $matches);
	  	return (isset($matches[1])) ? trim($matches[1]) : 'undefined';	  		  	  
	}

	/**
     * Extracts the return data type from the PHP-doc comments block for the specified method
     * 
     * @param ReflectionMethod $method A PHP ReflectionMethod instance representing the method
     *        to extract the parameter data type from
     * @return string The extracted PHP data type 
	 */
	public function getReturnType(ReflectionMethod $method) {
		preg_match('/@return\\s*(.*?)\\s/i', $method->getDocComment(), $matches);
		if(isset($matches[1])) {

			$value = trim($matches[1]);
			if($value == 'void') return null;
			return $value;
		}
	  	return null;
	}

	/**
	 * Identifys custom user space object types
	 *
	 * @param String $type The PHP data type returned from one of the methods in this class
	 * @return bool 
	 */
	public static function isUserSpaceObject($type) {

		$lowerType = strtolower($type);
		$phpDataTypes = array('string', 'int', 'integer', 'float',
				'double', 'bool', 'boolean', 'array', 'object', 'resource', 'null',
				'void');
		if($lowerType == 'undefined' || in_array($lowerType, $phpDataTypes)) return false;

		return class_exists($type); 
	}
}
?>