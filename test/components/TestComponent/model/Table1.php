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
namespace TestComponent\model;

/**
 * Table1 model in the TestComponent model namespace
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.component.TestComponent.model
 */
class Table1 extends \DomainModel implements \ActiveRecord {
  
      private $id;
      private $field1;
      private $field2;     
      private $Table2;

      public function __construct() { }
      
      #@Id
      public function setId( $value ) {
    
         $this->id = $value;
      }
    
      public function getId() {
    
         return $this->id;
      }
    
      public function setField1( $value ) {
    
         $this->field1 = $value;
      }
    
      public function getField1() {
    
         return $this->field1;
      }
    
      public function setField2( $value ) {
    
         $this->field2 = $value;
      }
    
      public function getField2() {
    
         return $this->field2;
      }
    
      public function setTable2( $value ) {
    
         $this->Table2 = $value;
      }
    
      public function getTable2() {
    
         return $this->Table2;
      }
}
?>