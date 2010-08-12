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
 * @package com.makeabyte.agilephp.scope
 */


/**
 * Session domain model object. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
class Session extends DomainModel {

	  private $id;
	  private $data;
	  private $created;

	  public function __construct() { }

	  /**
	   * Sets the session id
	   * 
	   * @param String $id The session id
	   * @return void
	   */
	  public function setId($id) {

	  		 $this->id = $id;
	  }

	  /**
	   * Returns the session id
	   * 
	   * @return String Session id
	   */
	  public function getId() {

	  		 return  $this->id;
	  }

	  /**
	   * Stores the serialized session
	   * 
	   * @param String $data Serialized session data
	   * @return void
	   */
	  public function setData($data) {

	  		 $this->data = $data;
	  }

	  /**
	   * Returns the serialized session data
	   * 
	   * @return String Serialized session data
	   */
	  public function getData() {

	  		 return $this->data;
	  }

	  /**
	   * Timestamp indicating when the session was created
	   * 
	   * @param Date $dateTime Timestamp indicating when the session was created
	   * @return void
	   */
	  public function setCreated($timestamp) {

	  		 $this->created = date('Y-m-d H:i:s', strtotime($timestamp));
	  }

	  /**
	   * Returns the timestamp when the session was created
	   * 
	   * @return Date Timestamp indicating when the session was created
	   */
	  public function getCreated() {

	  		 return $this->created;
	  }
}
?>