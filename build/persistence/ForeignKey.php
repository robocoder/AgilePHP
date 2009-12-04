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
 * AgilePHP :: ForeignKey
 * Represents a foreign key in the AgilePHP persistence component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence
 * @version 0.1a
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

	  public function __construct( SimpleXMLElement $foreign, $tableName, $columnName ) {

	  	     $this->name = (string)$foreign->attributes()->name;
	  		 $this->type = (string)$foreign->attributes()->type;
	  		 $this->table = (string)$foreign->attributes()->table;
	  		 $this->column = (string)$foreign->attributes()->column;
	  		 $this->controller = (string)$foreign->attributes()->controller;
	  		 $this->setOnUpdate( (string)$foreign->attributes()->onUpdate );
	  		 $this->setOnDelete( (string)$foreign->attributes()->onDelete );
	  		 $this->setSelect( (string)$foreign->attributes()->select );

	  		 $this->fkTable = $tableName;
	  		 $this->fkColumn = $columnName;
	  }

	  /**
	   * Sets the name of the foreign key. This is the actual name given to
	   * the foreign key according to the database server.
	   * 
	   * @param $name The foreign key name
	   * @return void
	   */
	  public function setName( $name ) {
	  	
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
	   * @param $type The data type of the value being stored in the foreign key column
	   * @return void
	   */
	  public function setType( $type ) {

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
	   * @param $tableName The name of the referenced (parent) table.
	   * @return void
	   */
	  public function setReferencedTable( $tableName ) {
	  	
	  		 $this->table = $table;
	  }

	  /**
	   * Returns the physical name of the referenced table.
	   * 
	   * @return The referenced (parent) table name
	   */
	  public function getReferencedTable() {

	  		 return $this->table;
	  }

	  /**
	   * Sets the name of the referenced (parent) column.
	   * 
	   * @param $columnName The referenced column name
	   * @return void
	   */
	  public function setReferencedColumn( $columnName ) {

	  		 $this->column = $column;
	  }

	  /**
	   * Returns the name of the referenced (parent) column.
	   * 
	   * @return The name of the referenced (parent) column.
	   */
	  public function getReferencedColumn( ) {

		  	 return $this->column;
	  }

	  /**
	   * Sets the name of the referenced MVC controller responsible
	   * for the management of the referenced domain model.
	   *  
	   * @param $controller The controller responsible for the management of the
	   * 					referenced domain model
	   * @return void
	   */
	  public function setReferencedController( $controller ) {

	  		 $this->controller = $controller;
	  }

	  /**
	   * Returns the name of the referenced MVC controller responsible
	   * for the management of the referenced domain model.
	   * 
	   * @return The referenced controller name
	   */
	  public function getReferencedController() {

	  		 return $this->controller;
	  }

	  /**
	   * Sets the SQL 'OnUpdate' action for the foreign key. This action
	   * is performed automatically by the database server anytime the
	   * primary key in the foreign (parent) table is updated.
	   * 
	   * @param $action The action to perform (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   * @return void
	   */
	  public function setOnUpdate( $action ) {

	  		 $this->onUpdate = str_replace( '_', ' ', $action );
	  }

	  /**
	   * Returns the configured SQL 'OnUpdate' action for the foreign key.
	   * 
	   * @return The configured action (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   */
	  public function getOnUpdate() {

	  		 return $this->onUpdate;
	  }

	  /**
	   * Sets the SQL 'OnDelete' action which is invoked when the foreign (parent)
	   * key is updated.
	   * 
	   * @param $action The action to perform when the foreign (parent) key is deleted. 
	   * 			    (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   * @return void
	   */
	  public function setOnDelete( $action ) {

	  		 $this->onDelete = str_replace( '_', ' ', $action ); 
	  }

	  /**
	   * Returns the configured SQL 'OnDelete' action for the foreign key.
	   * 
	   * @return The action to perform when the foreign (parent) key is deleted.
	   * 		 (NO_ACTION|RESTRICT|CASCADE|SET_NULL|SET_DEFAULT)
	   */
	  public function getOnDelete() {

	  		 return $this->onDelete;
	  }

	  /**
	   * Sets the physical name of the foreign key table.
	   * 
	   * @param $tableName The phsical name of the foreign key table.
	   * @return void
	   */
	  public function setFkTable( $tableName ) {

	  		 $this->fkTable = $tableName;
	  }

	  /**
	   * Returns the name of the table that stores this foreign key.
	   * 
	   * @return The foreign key (parent) table
	   */
	  public function getFkTable() {

	  		 return $this->fkTable;
	  }

	  /**
	   * The name of the column which stores this foreign key
	   *  
	   * @param $columnName The physical column name
	   * @return void
	   */
	  public function setFkColumn( $columnName ) {

	  		 $this->fkColumn = $columnName;
	  }

	  /**
	   * Returns the physical name of the column which stores the foreign key.
	   * 
	   * @return The physical name of the column storing the foreign key
	   */
	  public function getFkColumn() {

	  		 return $this->fkColumn;
	  }

	  /**
	   * Sets the name of the foreign table's column which should be displayed
	   * in a drop-down in place of the actual foreign key value.
	   *  
	   * @param $columnName The foreign column name
	   * @return void
	   */
	  public function setSelect( $columnName ) {

	  		 $this->select = $columnName;
	  }

	  /**
	   * Gets the name of the foreign table's column which should be displayed
	   * in a drop-down in place of the actual foreign key value.
	   * 
	   * @return void
	   */
	  public function getSelect() {

	  		 return $this->select;
	  }

	  /* Operations */

	  /**
	   * Returns an instance of the referenced Table.
	   * 
	   * @return The referenced table instance
	   */
	  public function getReferencedTableInstance() {

	  		 $pm = new PersistenceManager();
	  		 return $pm->getTableByName( $this->getReferencedTable() );
	  }

	  /**
	   * Returns an instance of the referenced column
	   * 
	   * @return The referenced column instance
	   */
	  public function getReferencedColumnInstance() {

	  		 $pm = new PersistenceManager();
	  		 $table = $pm->getTableByName( $this->getReferencedTable() );

	  		 foreach( $table->getColumns() as $column )
	  		 		  if( $column->getName() == $this->getReferencedColumn() )
	  		 		  	  return $column;

	  		 return null;
	  }

	  /**
	   * Returns the column instance where the foreign key resides
	   * 
	   * @return The foreign key column instance
	   */
	  public function getColumnInstance() {

	  		 $pm = new PersistenceManager();
	  		 $table = $pm->getTableByName( $this->getFkTable() );

	  		 foreach( $table->getColumns() as $column )
	  		 		  if( $column->getName() == $this->getFkColumn() )
	  		 		  	  return $column;

	  		 return null;
	  }

	  /**
	   * Returns the foreign (parent) column instance which contains a
	   * select="true" configuration in persistence.xml. If a column is
	   * not explicitly set, the referenced column instance is returned.
	   *  
	   * @return The column instance to use to display values in an HTML select
	   * 		 element rather than the foreign key values.
	   */
	  public function getSelectedColumnInstance() {

	  		 foreach( $this->getReferencedTableInstance()->getColumns() as $column )
	  		 		  if( $column->getName() == $this->getSelect() )
	  		 		  	  return $column;

	  		 return $this->getReferencedColumnInstance();
	  }
}
?>