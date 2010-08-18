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
 * Transforms YAML string data into a populated domain model.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.transformer
 */
class YamlToModel implements DataTransformer {

	  /**
	   * Transforms the specified data into a populated domain model.
	   * 
	   * @param string $data The string data which represents the domain model
	   * 					 and state to create.
	   * @return Object The domain model specified in the string $data
	   * @throws FrameworkException if the specified data could not be parsed
	   */
	  public static function transform($data) {

	  		 $o = yaml_parse($data);
	  		 if($o == null) throw new FrameworkException('Malformed YAML data');

	  		 return $o;
	  }
}
?>