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
 * @package com.makeabyte.agilephp.test.component.TestComponent.model
 */

/**
 * Table2 model in the TestComponent model namespace
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.component.TestComponent.model
 */
namespace TestComponent\model;

class Table2 {

  public function __construct() { }

  private $id;
  private $name;
  private $description;

  #@Id
  public function setId( $value ) {

     $this->id = $value;
  }

  public function getId() {

     return $this->id;
  }

  public function setName( $value ) {

     $this->name = $value;
  }

  public function getName() {

     return $this->name;
  }
  
  public function setDescription( $description ) {
  	
  		$this->description = $description;
  }
  
  public function getDescription() {
  	
  		return $this->description;
  }

}
?>