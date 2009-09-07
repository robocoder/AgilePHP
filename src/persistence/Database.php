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
 * @package com.makeabyte.agilephp.persistence
 */

/**
 * AgilePHP :: Database
 * Represents a database in the AgilePHP persistence component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence
 * @version 0.1a
 */
class Database {

	  private $id;
	  private $name;
	  private $type;
	  private $hostname;
	  private $username;
	  private $password;

	  private $tables = array();

	  public function __construct( SimpleXMLElement $database = null ) {

	  		 if( $database !== null ) {
		  		 
	  		 	 $this->id = (string)$database->attributes()->id;
		  		 $this->name = (string)$database->attributes()->name;
		  		 $this->type = (string)$database->attributes()->type;
		  		 $this->hostname = (string)$database->attributes()->hostname;
		  		 $this->username = (string)$database->attributes()->username;
		  		 $this->password = (string)$database->attributes()->password;
	
		  		 foreach( $database->table as $table )
		  		     	  array_push( $this->tables, new Table( $table ) );
	  		 }
	  }

	  /**
	   * Sets the database id. This is used internally by AgilePHP to distinguish
	   * between multiple data sources configured in persistence.xml
	   * 
	   * @param $id The database identifier. This can be any legal XML value.
	   * @return void
	   */
	  public function setId( $id ) {

	  		 $this->id = $id;
	  }

	  /**
	   * Returns the unique database identifier.
	   * 
	   * @return void
	   */
	  public function getId() {

	  		 return $this->id;
	  }

	  /**
	   * Sets the name of the database
	   * 
	   * @param $name The database name
	   * @return void
	   */
	  public function setName( $name ) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the database
	   * 
	   * @return The database name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the type of database server
	   * 
	   * @param $type The database server type (sqlite|mysql|pgsql|firebird|informix|oracle|dblib|ibm)
	   * @return void
	   */
	  public function setType( $type ) {

	  		 $this->type = $type;
	  }

	  /**
	   * Returns the database server type.
	   * 
	   * @return The type of database (sqlite|mysql|pgsql|firebird|informix|oracle|dblib|ibm)
	   */
	  public function getType() {

	  		 return $this->type;
	  }

	  /**
	   * Sets the hostname of the database server
	   * 
	   * @param $hostname The hostname of the database server (or file path
	   * 				  for sqlite databases).
	   * @return void
	   */
	  public function setHostname( $hostname ) {

	  		 $this->hostname = $hostname;
	  }

	  /**
	   * Returns the hostname of the database server.
	   * 
	   * @return The hostname of the database server (or file path for sqlite databases).
	   */
	  public function getHostname() {

	  		 return $this->hostname;
	  }

	  /**
	   * Sets the username required to access the database
	   * 
	   * @param $username The username to connect to the database with
	   * @return void
	   */
	  public function setUsername( $username ) {

	  		 $this->username = $username;
	  }

	  /**
	   * Returns the username which is used to connect to the database
	   * 
	   * @return The username thats used to connect to the database
	   */
	  public function getUsername() {

	  		 return $this->username;
	  }

	  /**
	   * Sets the password which is used to connect to the database
	   * 
	   * @param $password The password used to authenticate access to the database
	   * @return void
	   */
	  public function setPassword( $password ) {

	  		 $this->password = $password;
	  }

	  /**
	   * Returns the password thats used to connect to the database
	   * 
	   * @return The password used to authenticate access to the database
	   */
	  public function getPassword() {

	  		 return $this->password;
	  }

	  /**
	   * Sets the array of Table instances which represent a table in the physical
	   * database.
	   * 
	   * @param array $tables An array of Table instances which represent a table in the
	   * 					  physical database.
	   * @return void
	   */
	  public function setTables( array $tables ) {

	  		 $this->tables = $tables;
	  }

	  /**
	   * Pushes a new Table instance onto the stack
	   * 
	   * @param Table $table The table instance to push onto the stack
	   * @return void
	   */
	  public function addTable( Table $table ) {

	  		 array_push( $this->tables, $table );
	  }

	  /**
	   * Returns boolean indicator based on the presence of any Table instances
	   * configured for the database.
	   * 
	   * @return True if the database has any one or more tables, false otherwise
	   */
	  public function hasTables() {

	  		 return count( $this->tables ) ? true : false;
	  }

	  /**
	   * Returns an array of Table instances which represent a table in the physical
	   * database.
	   * 
	   * @return Array of Table instances
	   */
	  public function getTables() {

	  		 return $this->tables;
	  }
}
?>