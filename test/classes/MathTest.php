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
 * @package com.makeabyte.agilephp.test.classes
 */

/**
 * A simple calculator object that gets exposed via web services.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.classes
 */
class MathTest {

	  /** @var int */
	  public $a;

	  /** @var int */
	  public $b;

	  /**
	   * Mutator for $a property.
	   * 
	   * @param int $a
	   * @return void
	   */
	  public function setA($a) {
	  	
	  		 $this->a = $a;
	  }
	  
	  /**
	   * Accessor for $a property
	   * 
	   * @return int
	   */
	  public function getA() {
	  		
	  		 return $this->a;
	  }
	  
	  /**
	   * Mutator for $b property
	   * 
	   * @param int $b
	   * @return void
	   */
	  public function setB($b) {
	  		 
	  		 $this->b = $b;
	  }
	  
	  /**
	   * Accessor for $b property
	   * 
	   * @return int
	   */
	  public function getB() {
	  	
	  		 return $this->b;
	  }

	  /**
	   * Adds two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to add to the base
	   * @return int The sum
	   */
	  public function add($a, $b) {

	  		 return $a + $b;
	  }

	  /**
	   * Subtracts two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to subtract from the base
	   * @return int The difference
	   */
	  public function subtract($a, $b) {
	  	
	  		 return $a - $b;
	  }

	  /**
	   * Multiplies two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The number to multiply to the base
	   * @return int The product
	   */
	  public function multiply($a, $b) {
	  	
	  		 return $a * $b;
	  }

	  /**
	   * Divides two numbers.
	   * 
	   * @param int $a Base integer number
	   * @param int $b The divisor of the base
	   * @return int The quotient
	   */
	  public function divide($a, $b) {
	  	
	  		 return $a / $b;
	  }
}
?>