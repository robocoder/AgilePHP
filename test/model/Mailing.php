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
 * @package com.makeabyte.agilephp.test.model
 */

/**
 * The inventory domain model object responsible for maintaining ActiveRecord
 * state for a specified mailing list item. Note the use of the #@Id interceptor
 * which performs a SQL lookup in the database and populates the ActiveRecord
 * state when the mutator method is called.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.model
 * @version 0.1a
 */
class Mailing {
	
	  private $id;
	  private $name;
	  private $email;
	  private $enabled;
	  
	  public function Mailing() { }

	  #@Id
	  public function setId( $id ) {
	  	
	  	     $this->id = $id;
	  }
	  
	  public function getId() {
	  	
	  	     return $this->id;
	  }
	  
	  public function setName( $name ) {
	  	
	  	     $this->name = $name;
	  }
	  
	  public function getName() {
	  	
	  	     return $this->name;
	  }
	  
	  public function setEmail( $email ) {
	  	
	  	     $this->email = $email;
	  }
	  
	  public function getEmail() {
	  	
	  	     return $this->email;
	  }
	  
	  public function setEnabled( $bool ) {
	  	
	  	     $this->enabled = $bool;
	  }
	  
	  public function getEnabled() {

	  	     return $this->enabled;
	  }
	  
	  public function isEnabled() {

	  	     return $this->enabled == true ? true : false;
	  }
}
?>