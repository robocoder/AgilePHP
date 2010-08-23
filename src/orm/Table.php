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
 * Represents a data table in the AgilePHP orm component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class Table {

	  private $name;
	  private $model;
	  private $display;
	  private $validate = true;
	  private $description;
	  private $isIdentity = false;
	  private $isSession = false;
	  private $persist;
	  private $merge;
	  private $delete;
	  private $get;
	  private $find;	  

	  private $columns = array();

	  /**
	   * Creates a new Table instance.
	   * 
	   * @param SimpleXMLElement $table The SimpleXMLElement instance representing the
	   * 								physical database table.
	   * @return void
	   */
	  public function __construct(SimpleXMLElement $table = null) {

	  		 if($table) {

		  		 $this->name = (string)$table->attributes()->name;
		  		 $this->model = (string)$table->attributes()->model;
		  		 $this->display = (string)$table->attributes()->display;
		  		 $this->validate = ($table->attributes()->validate == 'false') ? false : true;
		  		 $this->description = (string)$table->attributes()->description;
		  		 $this->isIdentity = ($table->attributes()->isIdentity == 'true') ? true : false;
		  		 $this->isSession = ($table->attributes()->isSession == 'true') ? true : false;
		  		 $this->persist = (string)$table->persist;
	  		     $this->merge = (string)$table->merge;
	  		     $this->delete = (string)$table->delete;
	  		     $this->get = (string)$table->get;
	  		     $this->find = (string)$table->find;

		  		 foreach($table->column as $column)
	  		 		  array_push($this->columns, new Column($column, $this->name));
	  		 }
	  }

	  /**
	   * Sets the table name
	   * 
	   * @param String $name The name of the database table
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the database table
	   * 
	   * @return String The database table name
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the name of the domain model responsible for the table
	   * 
	   * @param String $model The name of the domain model
	   * @return void
	   */
	  public function setModel($model) {

	  		 $this->model = $model;
	  }

	  /**
	   * Returns the name of the domain model responsible for the table
	   * 
	   * @return String The domain model name
	   */
	  public function getModel() {

	  		 return $this->model;
	  }

	  /**
	   * Text field used to configure a 'display name'. If a value is specified
	   * this is the name that is used in visual elements such as forms and result
	   * lists when referencing the table.
	   * 
	   * @param String $display The friendly display name for the table
	   * @return void
	   */
	  public function setDisplay($display) {
	  	
	  		 $this->display = $display;
	  }

	  /**
	   * Returns the friendly display name for the table.
	   * 
	   * @return String The friendly display name
	   */
	  public function getDisplay() {

	  		 return $this->display;
	  }

	  /**
	   * Boolean field used to toggle automatic data validation for the table
	   * 
	   * @param bool $boolean True to enable validation, false to disable. Default is True.
	   * @return void
	   */
	  public function setValidate($boolean) {

	  		 $this->validate = $boolean ? true : false;
	  }

	  /**
	   * Returns the boolean field used to toggle automatic data validation.
	   *  
	   * @return bool True if the table gets validated automatically, false otherwise.
	   */
	  public function getValidate() {

	  		 return $this->validate === true ? true : false;
	  }

	  /**
	   * Sets the description field for the table. This field is used by visual elements
	   * which reference the tables description.
	   * 
	   * @param String $description A short description of the table
	   * @return void
	   */
	  public function setDescription($description) {

	  		 $this->description = $description;
	  }

	  /**
	   * Returns the table description
	   * 
	   * @return String The short description about the table
	   */
	  public function getDescription() {

	  		 return $this->description;
	  }

	  /**
	   * Sets the boolean field used to identify an AgilePHP Identity table.
	   *  
	   * @param bool $boolean True if the table is responsible for AgilePHP Identity orm.
	   * @return void
	   */
	  public function setIsIdentity($boolean) {

	  		 $this->isIdentity = $boolean ? true : false;
	  }

	  /**
	   * Returns true if the table is configured as an AgilePHP Identity table, false otherwise.
	   * 
	   * @return bool True if this is an AgilePHP Identity table, false otherwise 
	   */
	  public function isIdentity() {

	  		 return $this->isIdentity === true ? true : false;
	  }

	  /**
	   * Sets the boolean field used to identity an AgilePHP Session table.
	   * 
	   * @param bool $boolean True if the table is responsible for AgilePHP Session orm.
	   * @return void
	   */
	  public function setIsSession($boolean) {

	  		 $this->isSession = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean indicator based on whether or not this is a table
	   * responsible for AgilePHP session table.
	   * 
	   * @return bool True if this is an AgilePHP Session table, false otherwise
	   */
	  public function isSession() {

	  		 return $this->isSession === true ? true : false;
	  }

	  /**
	   * Sets the array of columns for the table
	   * 
	   * @param array $columns An array of Column instances which belong to this table
	   * @return void
	   */
	  public function setColumns(array $columns) {

	  		 $this->columns = $columns;
	  }

	  /**
	   * Pushes a new column to the table's column stack.
	   * 
	   * @param Column $column The Column instance to push onto the stack
	   * @return void
	   */
	  public function addColumn(Column $column) {

	  		 array_push($this->columns, $column);
	  }

	  /**
	   * Returns a Column instance corresponding to the specified name.
	   * 
	   * @param string $name The column name
	   * @return void
	   */
	  public function getColumn($name) {

	  		 foreach($this->columns as $c)
	  		 	if($c->getName() == $name)
	  		 		return $c;
	  }

	  /**
	   * Returns boolean response based on the presence of one or more Column
	   * instances.
	   * 
	   * @return bool True if the table contains columns, false otherwise
	   */
	  public function hasColumns() {

	  		 return count($this->columns) ? true : false;
	  }

	  /**
	   * Returns an array of Column instances which are configured for the table.
	   * 
	   * @return array Array of column instances
	   */
	  public function getColumns() {

	  		 return $this->columns;
	  }

	  /**
	   * Sets a prepared statement used to override ORM generated INSERT
	   * 
	   * @param string $statement A valid SQL INSERT prepared statement
	   * @return void
	   */
	  public function setPersist($statement) {
	      
	         $this->persist = $statement;
	  }

	  /**
	   * Returns orm.xml <persist> configuration for the table
	   * 
	   * @return string <persist> configuration value
	   */
	  public function getPersist() {
	      
	         return $this->persist;
	  }

	  /**
	   * Sets the prepared statement used to override ORM generated UPDATE
	   * 
	   * @param string $statement A valid SQL UPDATE prepared statement
	   * @return void
	   */
	  public function setMerge($statement) {
	      
	         $this->merge = $statement;
	  }
	  
	  /**
	   * Returns the orm.xml <merge> configuration for the table
	   * 
	   * @return string <merge> configuration value
	   */
	  public function getMerge() {
	      
	         return $this->merge;
	  }

	  /**
	   * Sets a prepared statement used to override ORM generated DELETE
	   * 
	   * @param string $statement A valid SQL DELETE prepared statement
	   * @return void
	   */
	  public function setDelete($statement) {
	      
	         $this->delete = $statement;
	  }
	  
	  /**
	   * Returns the orm.xml <delete> configuration for the table
	   * 
	   * @return string <delete> configuration value
	   */
	  public function getDelete() {

	         return $this->delete;
	  }

	  /**
	   * Sets a prepared statement used to override ORM generated SELECT (intended to populate a single ActiveRecord model).
	   * 
	   * @param string $statement A valid SQL SELECT prepared statement intended to pull a single record (should contain WHERE id=?)
	   * @return void
	   */
	  public function setGet($statement) {
	      
	         $this->get = $statement;
	  }

	  /**
	   * Returns the orm.xml <get> configuration for the table
	   * 
	   * @return string <get> configuration value
	   */
	  public function getGet() {

	         return $this->get;
	  }

	  /**
	   * Sets a prepared statement used to override ORM generated SELECT (intended to populate a list view)
	   * 
	   * @param string $statement A valid SQL SELECT prepared statement
	   * @return void
	   */
	  public function setFind($statement) {
	      
	         $this->find = $statement;
	  }

	  /**
	   * Returns the orm.xml <find> configuration for the table
	   * 
	   * @return string <find> configuration value
	   */
	  public function getFind() {
	      
	         return $this->find;
	  }
	  
	  /**
	   * Returns a list of primary key columns in the current table.
	   * 
	   * @return array An array of 'Column' objects which have been configured as primary keys
	   * 		 	   in the current table.
	   */
	  public function getPrimaryKeyColumns() {

	  		 $columns = array();
	  		 foreach($this->getColumns() as $column)
	  		 	      if($column->isPrimaryKey())
	  		 	      	  array_push($columns, $column);

	  		 return $columns;
	  }

	  /**
	   * Returns a list of foreign key columns in the current table.
	   * 
	   * @return array An array of 'Column' objects which contain foreign keys in the current table.
	   */
	  public function getForeignKeyColumns() {

	  		 $columns = array();
	  		 foreach($this->getColumns() as $column)
	  		 	      if($column->isForeignKey())
	  		 	      	  array_push($columns, $column);

	  		 return $columns;
	  }

	  /**
	   * Returns a list of all foreign keys which share the same 'name' attribute value
	   * 
	   * @param String $name The name of the foreign key
	   * @return array An array of columns with the specified 'name' attribute
	   */
	  public function getForeignKeyColumnsByKey($name) {

	  	     $keys = array();
	  	     foreach($this->getColumns() as $column)

	  	     		  if($column->isForeignKey())
	  	     		  	  if($column->getForeignKey()->getName() == $name)
	  	     		  	  	  array_push($keys, $column->getForeignKey());

	  		 return $keys;
	  }

	  /**
	   * Looks up a 'Column' name by its orm.xml 'property' attribute.
	   * 
	   * @param $property The name of a domain model property to return its corresponding column name
	   * @return mixed Column name or null if the column name could not be found
	   */
	  public function getColumnNameByProperty($property) {

			 foreach($this->getColumns() as $column)
			   		  if($column->getProperty() == $property)
			   			  return $column->getName();

			 Log::warn('Table::getColumnByProperty Could not find a property name corresponding to \'' . $property . '\'. Attempting to return column name instead.');

			 foreach($this->getColumns() as $column)
			   		  if($column->getName() == $property)
			   			  return $column->getName();

			 Log::warn('Table::getColumnByProperty Warning about could not find a matching column name corresponding to \'' . $property . '\'. Returning null.');

			 return null;
	  }

	  /**
	   * Returns the 'display' attribute value as configured in orm.xml for
	   * the specified 'property' value.
	   * 
	   * @param String $property The name of the domain model property to retrieve the display name for
	   * @return The columns 'display' name or null if a display name has not been configured.
	   */
	  public function getDisplayNameByProperty($property) {

	  		 foreach($this->getColumns() as $column)
	  		 		  if($column->getModelPropertyName() == $property)
	  		 		  	  return $column->getDisplay() ? $column->getDisplay() : ucfirst($column->getName()); 

			 Log::debug('Table::getDisplayNameByProperty returning null value for property \'' . $property . '\'.');

	  		 return null;
	  }

	  /**
	   * Returns boolean response based on the 'visible' attribute in orm.xml for the
	   * column which contains the specified $property.
	   * 
	   * @param String $property The domain object model's property name
	   * @return bool True if the column is visible or false if the column is NOT visible
	   */
	  public function isVisible($property) {

	  		 foreach($this->getColumns() as $column)
	  		 		  if($column->getModelPropertyName() == $property)
	  		 		  	  return $column->isVisible() == true;

	  		 return true;
	  }

	  /**
	   * Returns boolean response based on the presence of a column which is
	   * configured in orm.xml as a blob data type.
	   * 
	   * @return bool True if the table contains any blob columns or false if no
	   * 		 	  blob columns exist.
	   */
	  public function hasBlobColumn() {

	  		 foreach($this->getColumns() as $column)
	  		 		  if($column->getType() == 'blob')
	  		 		  	  return true;

	  		 return false;
	  }
	  
	  /**
	   * Returns boolean response based on the presence of a column which is
	   * configured in orm.xml as a foreign key.
	   * 
	   * @return bool True if the table contains any foriegn key columns or false if no
	   * 		 	  foreign keys exist.
	   */
	  public function hasForeignKey() {

	  		 foreach($this->getColumns() as $column)
	  		 		  if($column->isForeignKey())
	  		 		  	  return true;

	  		 return false;
	  }

	  /**
	   * Returns boolean response based on whether or not there are any other
	   * tables which have foreign keys referencing this table.
	   * 
	   * @return bool True if there are any other tables in the database which
	   * 		 	  have foreign keys which reference this table.
	   */
	  public function hasForeignKeyReferences() {

	  		 $orm = ORMFactory::getDialect();
	  		 foreach($orm->getDatabase()->getTables() as $table)
	  		 	foreach($table->getColumns() as $column)
	  		 	   if($column->isForeignKey())
	  		 	  	  if($column->getForeignKey()->getReferencedTable() == $this->getName())
	  		 		     return true;

	  		 return false;
	  }

	  /**
	   * Returns an instance of the model as configured in orm.xml for this 'Table'.
	   * 
	   * @return Object An instance of the model responsible for the table's orm.
	   */
	  public function getModelInstance() {

	  		 $modelName = $this->getModel();
	  		 return new $modelName();
	  }

	  /**
	   * Returns a name suitable for display to end users. If a friendly name has been configured
	   * for this table (by providing a display attribute value in orm.xml for the Column),
	   * then this value is returned, otherwise the table name attribute value is returned instead.
	   * 
	   * @return String The name of the table which gets displayed to end users
	   */
	  public function getViewDisplayName() {

	  		 return $this->getDisplay() ? $this->getDisplay() : $this->getName();
	  }
}
?>