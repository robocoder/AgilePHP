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
 * @package com.makeabyte.agilephp.scope
 */


/**
 * AgilePHP :: Session
 * Session domain model object. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 * @version 0.1a
 */
class Session {

	  private $id;
	  private $data;
	  private $created;

	  public function __construct() { }

	  /**
	   * Sets the session id
	   * 
	   * @param $id The session id
	   * @return void
	   */
	  public function setId( $id ) {

	  		 $this->id = $id;
	  }

	  /**
	   * Returns the session id
	   * 
	   * @return Session id
	   */
	  public function getId() {

	  		 return  $this->id;
	  }

	  /**
	   * Stores the serialized session
	   * 
	   * @param $data Serialized session data
	   * @return void
	   */
	  public function setData( $data ) {

	  		 $this->data = $data;
	  }

	  /**
	   * Returns the serialized session data
	   * 
	   * @return void
	   */
	  public function getData() {

	  		 return $this->data;
	  }

	  /**
	   * Timestamp indicating when the session was created
	   * 
	   * @param $dateTime Timestamp indicating when the session was created
	   * @return void
	   */
	  public function setCreated( $timestamp ) {

	  		 $this->created = $timestamp;
	  }

	  /**
	   * Returns the timestamp when the session was created
	   * 
	   * @return Timestamp indicating when the session was created
	   */
	  public function getCreated() {

	  		 return $this->created;
	  }
}
?>