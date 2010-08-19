<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *(at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.orm
 */

/**
 * Represents a procedure parameter in the AgilePHP orm component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class ProcedureParam {

	  private $name;
	  private $property;
	  private $mode;
	  private $references;

	  public function __construct(SimpleXMLElement $parameter = null) {

	  		 if($parameter) {

	  		 	 $this->name =(string)$parameter->attributes()->name;
		  		 $this->property =(string)$parameter->attributes()->property;
		  		 $this->mode =(string)$parameter->attributes()->mode;
		  		 $this->references =(string)$parameter->attributes()->references;
	  		 }
	  }

	  /**
	   * Sets the name of the parameter
	   * 
	   * @param string $name The parameter name
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Gets the name of the parameter
	   * 
	   * @return string The parameter name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the parameter property mapping
	   * 
	   * @param string $property The name of the property which the parameter maps
	   * @return void
	   */
	  public function setProperty($property) {

	  		 $this->property = $property;
	  }

	  /**
	   * Gets the parameter property mapping
	   * 
	   * @return string The name of the property which the parameter maps
	   */
	  public function getProperty() {

	  		 return $this->property;
	  }

	  /**
	   * Sets the parameter type
	   * 
	   * @param string $mode The parameter type(IN|OUT|INOUT)
	   */
	  public function setMode($mode) {

	  		 $this->mode = $mode;
	  }

	  /**
	   * Gets the parameter type
	   * 
	   * @return string The parameter type(IN|OUT|INOUT)
	   */
	  public function getMode() {

	  		 return $this->mode;
	  }

	  /**
	   * Sets the name of a referenced procedure to execute passing in the value
	   * coming from the database. 
	   * 
	   * @param string $procedure The referenced procedure name
	   * @return void
	   */
	  public function setReference($procedure) {

	         $this->references = $procedure;
	  }

	  /**
	   * Returns the referenced procedure
	   * 
	   * @return string The referenced procedure name
	   */
	  public function getReference() {

	         return $this->references;
	  }
	  
	  /**
	   * Helper method which provides the name of the parameter property name
	   * as it exists inside its model. If a property attribute has been set,
	   * the property is returned, otherwise the name is returned.
	   * 
	   * @return string The name of the parameter's model property
	   */
	  public function getModelPropertyName() {

	  		 return($this->property) ? $this->property : $this->name;
	  }
}