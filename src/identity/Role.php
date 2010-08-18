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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Role associated with an AgilePHP Identity
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class Role extends DomainModel {

      private $id;
	  private $name;
	  private $description;

	  /**
	   * Creates a new instance of Role.
	   *
	   * @param String $name An optional user friendly name of the role.
	   * @return void
	   */
	  public function __construct($id = null, $name = null, $description = null) {

	         $this->id = $id;
	  		 $this->name = $name;
	  		 $this->description = $description;
	  }

	  /**
	   * Sets the role id/primary key
	   *
	   * @param integer $id The role id
	   * @return void
	   */
	  #@Id
	  public function setId($id) {

	         $this->id = $id;
	  }

	  /**
	   * Gets the role id
	   *
	   * @return integer The role id
	   */
	  public function getId() {

	         return $this->id;
	  }

	  /**
	   * Sets the name of the role
	   *
	   * @param String $name The role name
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the role
	   *
	   * @return String The role name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the description of the role.
	   *
	   * @param String $description The role description
	   * @return void
	   */
	  public function setDescription($description) {

	  		 $this->description = $description;
	  }

	  /**
	   * Returns the role description
	   *
	   * @return String The role description
	   */
	  public function getDescription() {

	  		 return $this->description;
	  }
}
?>