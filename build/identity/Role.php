<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009 Make A Byte, inc
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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * AgilePHP :: Role
 * Role associated with an Identity
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 * @version 0.1a
 */
class Role {

	  private $name;
	  private $description;

	  public function __construct( $role = null ) {

	  		 if( $role ) $this->name = $role;
	  }

	  /**
	   * Sets the name of the role
	   * 
	   * @param $name The role name
	   * @return void
	   */
	  public function setName( $name ) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the role
	   * 
	   * @return The role name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the description of the role.
	   * 
	   * @param $description The role description
	   * @return void
	   */
	  public function setDescription( $description ) {

	  		 $this->description = $description;
	  }

	  /**
	   * Returns the role description
	   * 
	   * @return The role description
	   */
	  public function getDescription() {
	  	
	  		 return $this->description;
	  }
}
?>