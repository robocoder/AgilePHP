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
 * @package com.makeabyte.agilephp.studio.model
 */

/**
 * Configuration model
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.model
 */
class Config {

	  private $name;
	  private $value;

	  public function __construct( $name = null, $value = null ) {

	  		 $this->name = $name;
	  		 $this->value = $value;
	  }

	  #@Id
	  public function setName( $name ) {
	  	
	  		 $this->name = $name;
	  }
	  
	  public function getName() {
	  	
	  		 return $this->name;
	  }
	  
	  public function setValue( $value ) {
	  	
	  		 $this->value = $value;
	  }
	  
	  public function getValue() {
	  	
	  		 return $this->value;
	  }
}
?>