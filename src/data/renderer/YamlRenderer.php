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
 * @package com.makeabyte.agilephp.data.renderer
 */

/**
 * Transforms data to YAML
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.renderer
 */
class YamlRenderer implements DataRenderer {

	  /**
	   * Transforms the specified PHP data to YAML.
	   * 
	   * @param mixed $data Data to transform to YAML
	   * @param int $encoding YAML_ANY_ENCODING, YAML_UTF8_ENCODING, YAML_UTF16LE_ENCODING, YAML_UTF16BE_ENCODING. Defaults to YAML_ANY_ENCODING.
	   * @param int $linebreak YAML_ANY_BREAK, YAML_CR_BREAK, YAML_LN_BREAK, YAML_CRLN_BREAK. Defaults to YAML_ANY_BREAK
	   * @return string The YAML formatted data.
	   */
	  public static function render($data, $encoding = null, $linebreak = null) {

	  		 return yaml_emit($data, $encoding, $linebreak);
	  }
}
?>