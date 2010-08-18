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
 * Represents a foreign key in the AgilePHP ORM component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class ForeignKey {

	  private $name;		// The name of the foreign key constraint
	  private $type;		// The type of relationship (one-to-one, one-to-many, many-to-many)
	  private $table;		// The referenced table name
	  private $column;		// The referenced column name
	  private $controller;  // The MVC controller responsible for rendering the referenced table
	  private $onUpdate;
	  private $onDelete;
	  private $select;		// Stores the name of the foreign table's column to display in a drop down
	  						// in place of the actual foreign key value.

	  private $fkTable ;    // Passed in from 'Column'; retains the name of the foreign key table
	  private $fkColumn;    // Passed in from 'Column'; retains the name of the foreign key column
	  private $cascade;

	  public function __construct(SimpleXMLElement $foreign = null, $tableName, $columnName) {

	  		 if($foreign) {

		  	     $this->name = (string)$foreign->attributes()->name;
		  		 $this->type = (string)$foreign->attributes()->type;
		  		 $this->table = (string)$foreign->attributes()->table;
		  		 $this->column = (string)$foreign->attributes()->column;
		  		 $this->controller = (string)$foreign->attributes()->controller;
		  		 $this->setOnUpdate(preg_replace('/_/', ' ', (string)$foreign->attributes()->onUpdate));
		  		 $this->setOnDelete(preg_replace('/_/', ' ', (string)$foreign->attributes()->onDelete));
		  		 $this->setSelect((string)$foreign->attributes()->select);
		  		 $this->cascade = (string)$foreign->attributes()->cascade;
	  		 }

	  		 $this->fkTable = $tableName;
		  	 $this->fkColumn = $columnName;
	  }

	  /**
	   * Sets the name of the foreign key. This is the actual name given to
	   * the foreign key according to the database server.
	   * 
	   * @param String $name The foreign key name
	   * @return void
	   */
	  public function setName($name) {
	  	
	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the foreign key
	   *  
	   * @return The foreign key name
	   */
	  public function getName() {
	  	
	  		 return $this->name;
	  }

	  /**
	   * Sets the foreign key value data type.
	   *  
	   * @param String $type The data type of the value being stored in the foreign key column
	   * @return void
	   */
	  public function setType($type) {

	  		 $this->type = $type;
	  }

	  /**
	   * Returns the data type being stored in the foreign key column.
	   * 
	   * @return The foreign key data type
	   */
	  public function getType() {
	  	
	  		 return $this->type;
	  }

	  /**
	   * Sets the name of the referenced table.
	   * 
	   * @param String $tableName The name of the referenced (parent) table.
	   * @return void
	   */
	  public function setReferencedTable($tableName) {
	  	
	  		 $this->table = $tableName;
	  }

	  /**
	   * Returns the physical name of the referenced table.
	   * 
	   * @return String The referenced (parent) table name
	   */
	  public function getReferencedTable() {

	  		 return $this->table;
	  }

	  /**
	   * Sets the name of the referenced (parent) column.
	   * 
	   * @param String $columnName The referenced column name
	   * @return void
	   */
	  public function setReferencedColumn($columnName) {

	  		 $this->column = $columnName;
	  }

	  /**
	   * Returns the name of the referenced (parent) column.
	   * 
	   * @return String The name of the referenced (parent) column.
	   */
	  public function getReferencedColumn() {

		  	 return $this->column;
	  }

	  /**
	   * Sets the name of the referenced MVC controller responsible
	   * for the management of the referenced domain model.
	   *  
	   * @param String $controller The controller responsible for the management of the
	   * 						   referenced domain model
	   * @return void
	   */
	  public function setReferencedController($controller) {

	  		 $this->controller = $controller;
	  }

	  /**
	   * Returns the name of the referenced MVC controller responsible
	   * for the management of the referenced domain model.
	   * 
	   * @return String The referenced controller name
	   */
	  public function getReferencedController() {

	  		 return $this->controller;
	  }

	  /**
	   * Sets the SQL 'OnUpdate' action for the foreign key. This action
	   * is performed automatically by the database server anytime the
	   * primary key in the foreign (parent) table is updated.
	   * 
	   * @param String $action The action to perform (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   * @return void
	   */
	  public function setOnUpdate($action) {

	  		 $this->onUpdate = $action;
	  }

	  /**
	   * Returns the configured SQL 'OnUpdate' action for the foreign key.
	   * 
	   * @return String The configured action (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   */
	  public function getOnUpdate() {

	  		 return $this->onUpdate;
	  }

	  /**
	   * Sets the SQL 'OnDelete' action which is invoked when the foreign (parent)
	   * key is updated.
	   * 
	   * @param String $action The action to perform when the foreign (parent) key is deleted. 
	   * 			   		  (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   * @return void
	   */
	  public function setOnDelete($action) {

	  		 $this->onDelete = $action; 
	  }

	  /**
	   * Returns the configured SQL 'OnDelete' action for the foreign key.
	   * 
	   * @return String The action to perform when the foreign (parent) key is deleted.
	   * 				(NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   */
	  public function getOnDelete() {

	  		 return $this->onDelete;
	  }

	  /**
	   * Sets the physical name of the foreign key table.
	   * 
	   * @param String $tableName The phsical name of the foreign key table.
	   * @return void
	   */
	  public function setFkTable($tableName) {

	  		 $this->fkTable = $tableName;
	  }

	  /**
	   * Returns the name of the table that stores this foreign key.
	   * 
	   * @return String The foreign key (parent) table
	   */
	  public function getFkTable() {

	  		 return $this->fkTable;
	  }

	  /**
	   * The name of the column which stores this foreign key
	   *  
	   * @param String $columnName The physical column name
	   * @return void
	   */
	  public function setFkColumn($columnName) {

	  		 $this->fkColumn = $columnName;
	  }

	  /**
	   * Returns the physical name of the column which stores the foreign key.
	   * 
	   * @return String The physical name of the column storing the foreign key
	   */
	  public function getFkColumn() {

	  		 return $this->fkColumn;
	  }

	  /**
	   * Sets the name of the foreign table's column which should be displayed
	   * in a drop-down in place of the actual foreign key value.
	   *  
	   * @param String $columnName The foreign column name
	   * @return void
	   */
	  public function setSelect($columnName) {

	  		 $this->select = $columnName;
	  }

	  /**
	   * Gets the name of the foreign table's column which should be displayed
	   * in a drop-down in place of the actual foreign key value.
	   * 
	   * @return Column name used to populate select combobox
	   */
	  public function getSelect() {

	  		 return $this->select;
	  }

	  /**
	   * Sets the action to use for the parent/child relationship.
	   * 
	   * @param string $action The action to take (none | all | save-update | delete)
	   * @return void
	   */
	  public function setCascade($value) {
	      
	         $this->cascade = $value;
	  }

	  /**
	   * Gets the cascade action to take for the parent/child relationship.
	   * 
	   * @return string One of the following actions to take - (none | all | save-update | delete)
	   */
	  public function getCascade() {

	         return $this->cascade;
	  }

	  /**
	   * Returns an instance of the referenced Table.
	   * 
	   * @return Table The referenced table instance
	   */
	  public function getReferencedTableInstance() {

	  		 return ORM::getTableByName($this->getReferencedTable());
	  }

	  /**
	   * Returns an instance of the referenced column
	   * 
	   * @return Column The referenced column instance
	   */
	  public function getReferencedColumnInstance() {

	  		 $table = ORM::getTableByName($this->getReferencedTable());

	  		 foreach($table->getColumns() as $column)
	  		 		  if($column->getName() == $this->getReferencedColumn())
	  		 		  	  return $column;

	  		 return null;
	  }

	  /**
	   * Returns the column instance where the foreign key resides
	   * 
	   * @return Column The foreign key column instance
	   */
	  public function getColumnInstance() {

	  		 $table = ORM::getTableByName($this->getFkTable());

	  		 foreach($table->getColumns() as $column)
	  		 		  if($column->getName() == $this->getFkColumn())
	  		 		  	  return $column;

	  		 return null;
	  }

	  /**
	   * Returns the foreign (parent) column instance which contains a
	   * select="true" configuration in orm.xml. If a column is
	   * not explicitly set, the referenced column instance is returned.
	   *  
	   * @return Column The column instance to use to display values in an HTML select
	   * 		 		element rather than the foreign key values.
	   */
	  public function getSelectedColumnInstance() {

	  		 foreach($this->getReferencedTableInstance()->getColumns() as $column)
	  		 		  if($column->getName() == $this->getSelect())
	  		 		  	  return $column;

	  		 return $this->getReferencedColumnInstance();
	  }
}
?>