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
 * @package com.makeabyte.agilephp.orm
 */

/**
 * Represents a database in the AgilePHP ORM component.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class Database {

	  private $name;
	  private $type;
	  private $hostname;
	  private $port;
	  private $username;
	  private $password;
	  private $driver;

	  private $tables = array();
	  private $procedures = array();

	  /**
	   * Creates a new Database instance with optional database assignment.
	   *
	   * @param SimpleXMLElement $database A database instance represented in
	   * 						 a SimpleXMLElement structure.
	   */
	  public function __construct(SimpleXMLElement $database = null) {

	  		 if($database !== null) {

		  		$this->name = (string)$database->attributes()->name;
		  		$this->type = (string)$database->attributes()->type;
		  		$this->hostname = (string)$database->attributes()->hostname;
		  		$this->port = (string)$database->attributes()->port;
		  		$this->username = (string)$database->attributes()->username;
		  		$this->password = (string)$database->attributes()->password;
		  		$this->driver = (string)$database->attributes()->driver;

		  		foreach($database->table as $table)
		  		     array_push($this->tables, new Table($table));

		  		foreach($database->procedure as $procedure)
		  		     array_push($this->procedures, new Procedure($procedure));
	  		 }
	  }

	  /**
	   * Sets the name of the database
	   *
	   * @param String $name The database name
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the database
	   *
	   * @return String The database name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the type of database server
	   *
	   * @param String $type The database server type (sqlite|mysql|pgsql|mssql)
	   * @return void
	   */
	  public function setType($type) {

	  		 $this->type = $type;
	  }

	  /**
	   * Returns the database server type.
	   *
	   * @return String The type of database server type (sqlite|mysql|pgsql|mssql)
	   */
	  public function getType() {

	  		 return $this->type;
	  }

	  /**
	   * Sets the hostname of the database server
	   *
	   * @param String $hostname The hostname of the database server (or file path for sqlite databases).
	   * @return void
	   */
	  public function setHostname($hostname) {

	  		 $this->hostname = $hostname;
	  }

	  /**
	   * Returns the hostname of the database server.
	   *
	   * @return String The hostname of the database server (or file path for sqlite databases).
	   */
	  public function getHostname() {

	  		 return $this->hostname;
	  }

	  /**
	   * Sets the database server's TCP/IP listen port.
	   *
	   * @param int $port The TCP/IP port
	   * @return void
	   */
	  public function setPort($port) {

	         $this->port = $port;
	  }

	  /**
	   * Gets the database server's TCP/IP listen port.
	   *
	   * @return int The TCP/IP port
	   */
	  public function getPort() {

	         return $this->port;
	  }

	  /**
	   * Sets the username required to access the database
	   *
	   * @param String $username The username to connect to the database with
	   * @return void
	   */
	  public function setUsername($username) {

	  		 $this->username = $username;
	  }

	  /**
	   * Returns the username which is used to connect to the database
	   *
	   * @return String The username thats used to connect to the database
	   */
	  public function getUsername() {

	  		 return $this->username;
	  }

	  /**
	   * Sets the password which is used to connect to the database
	   *
	   * @param String $password The password used to authenticate access to the database
	   * @return void
	   */
	  public function setPassword($password) {

	  		 $this->password = $password;
	  }

	  /**
	   * Returns the password thats used to connect to the database
	   *
	   * @return String The password used to authenticate access to the database
	   */
	  public function getPassword() {

	  		 return $this->password;
	  }

	  /**
	   * Sets the driver string used in ODBC connections.
	   *
	   * @param String $driver The driver name.
	   * @return void
	   */
	  public function setDriver($driver) {

	  		 $this->driver = $driver;
	  }

	  /**
	   * Returns the driver used in ODBC connections.
	   *
	   * @return String The driver name.
	   */
	  public function getDriver() {

	  		 return $this->driver;
	  }

	  /**
	   * Sets the array of Table instances which represent a table in the physical
	   * database.
	   *
	   * @param array $tables An array of Table instances which represent a table in the
	   * 					  physical database.
	   * @return void
	   */
	  public function setTables(array $tables) {

	  		 $this->tables = $tables;
	  }

	  /**
	   * Pushes a new Table instance onto the stack
	   *
	   * @param Table $table The table instance to push onto the stack
	   * @return void
	   */
	  public function addTable(Table $table) {

	  		 array_push($this->tables, $table);
	  }

	  /**
	   * Returns boolean indicator based on the presence of any Table instances
	   * configured for the database.
	   *
	   * @return True if the database has any one or more tables, false otherwise
	   */
	  public function hasTables() {

	  		 return isset($this->tables[0]) ? true : false;
	  }

	  /**
	   * Returns an array of Table instances which represent a table in the physical
	   * database.
	   *
	   * @return Array of Table instances each representing a table in the physical database.
	   */
	  public function getTables() {

	  		 return $this->tables;
	  }

	  /**
	   * Returns an array of Proceudre instances which represent a stored procedure in the
	   * physical database.
	   *
	   * @return array A list of Procedure instances each representing a stored procedure in the physical database.
	   */
	  public function getProcedures() {

	  		 return $this->procedures;
	  }

	  /**
	   * Adds a new stored procedure
	   *
	   * @param Procedure The Procedure instance to push onto the stack
	   * @return void
	   */
	  public function addProcedure(Procedure $procedure) {

	         array_push($this->procedures, $procedure);
	  }

	  /**
	   * Sets the stack of Procedures
	   *
	   * @param array<Procedure> A list of Procedure instances
	   * @return void
	   */
	  public function setProcedures(array $procedures) {

	         $this->procedures = $procedures;
	  }
}
?>