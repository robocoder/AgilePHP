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
 * Represents a database table column in the AgilePHP ORM component.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
class Column {

	  private $name;
	  private $type;
	  private $length;
	  private $description;
	  private $property;
	  private $display;
	  private $default;
	  private $visible = true;
	  private $sortable = true;
	  private $required = false;
	  private $index = false;
	  private $primaryKey = false;
	  private $autoIncrement = false;
	  private $sanitize = true;
	  private $lazy = false;
	  private $validator;
	  private $renderer;
	  private $transformer;
	  private $sequence;

	  private $foreignKey;

	  public function __construct(SimpleXMLElement $column = null, $tableName) {

	  		 if($column) {

		  		 $this->name = (string)$column->attributes()->name;
		  		 $this->type = (string)$column->attributes()->type;
		  		 $this->length = (integer)$column->attributes()->length;
		  		 $this->description = (string)$column->attributes()->description;
		  		 $this->property = (string)$column->attributes()->property;
		  		 $this->default = (string)$column->attributes()->default;
		  		 $this->display = (string)$column->attributes()->display;
		  		 $this->visible = ($column->attributes()->visible == 'false') ? false : true;
		  		 $this->sortable = ($column->attributes()->sortable == 'false') ? false : true;
		  		 $this->required = ($column->attributes()->required == 'true') ? true : false;
		  		 $this->index = ($column->attributes()->index == 'true') ? true : false;
		  		 $this->primaryKey = ($column->attributes()->primaryKey == 'true') ? true : false;
		  		 $this->autoIncrement = ($column->attributes()->autoIncrement == 'true') ? true : false;
		  		 $this->sanitize = ($column->attributes()->sanitize == 'false') ? false : true;
		  		 $this->lazy = ($column->attributes()->lazy == 'true') ? true : false;
		  		 $this->validator = (string)$column->attributes()->validator;
		  		 $this->renderer = (string)$column->attributes()->renderer;
		  		 $this->transformer = (string)$column->attributes()->transformer;
		  		 $this->sequence = (string)$column->attributes()->sequence;

		  		 if($column->foreignKey)
		  		 	 $this->foreignKey = new ForeignKey($column->foreignKey, $tableName, $this->name);
	  		 }
	  }

	  /**
	   * Sets the name of the column in the physical database
	   *
	   * @param String $name The column name in the physical database
	   * @return void
	   */
	  public function setName($name) {

	  		 $this->name = $name;
	  }

	  /**
	   * Returns the name of the column in the physical database
	   *
	   * @return The name of the column in the physical database
	   */
	  public function getName() {

	  		 return $this->name;
	  }

	  /**
	   * Sets the data type which describes the type of data which is
	   * to be stored in the column/field.
	   *
	   * @param String $type The data type being stored (varchar|int|text|etc...)
	   * @return void
	   */
	  public function setType($type) {

	  	     $this->type = $type;
	  }

	  /**
	   * Returns the data type which describes the type of data which is
	   * being stored in the column/field.
	   *
	   * @return String The data type
	   */
	  public function getType() {

	  		 return $this->type;
	  }

	  /**
	   * Sets the length of the data being stored in the column
	   *
	   * @param $length The maximum length of the data being persisted
	   * @return void
	   */
	  public function setLength($length) {

	  		 $this->length = (integer)$length;
	  }

	  /**
	   * Returns the length of the data being stored in the column
	   *
	   * @return String The max length of the data being persisted
	   */
	  public function getLength() {

	  		 return $this->length;
	  }

	  /**
	   * Sets the friendly description of the column. This is used by visual
	   * elements to describe the column to the end user or website owner.
	   *
	   * @param String $description The friendly description explaining the purpose of the column, etc.
	   * @return void
	   */
	  public function setDescription($description) {

	  		 $this->description = $description;
	  }

	  /**
	   * Returns the friendly description of the column.
	   *
	   * @return The description of the column
	   */
	  public function getDescription() {

	  		 return $this->description;
	  }

	  /**
	   * Sets the name of the property in the domain model which the column maps
	   *
	   * @param String $property The property name in the domain model that stores the column data
	   * @return void
	   */
	  public function setProperty($property) {

	  		 $this->property = $property;
	  }

	  /**
	   * Returns the name of the property in the domain model which the column maps
	   *
	   * @return String The property/field name
	   */
	  public function getProperty() {

	  		 return $this->property;
	  }

	  /**
	   * Sets the friendly display name of the column. This is used by visual
	   * elements (such as forms) to show a user friendly name for the column.
	   *
	   * @param String $text The friendly name to display
	   * @return void
	   */
	  public function setDisplay($text) {

	  		 $this->display = $text;
	  }

	  /**
	   * Returns the friendly display name for the column
	   *
	   * @return String The user friendly display name for use with visual elements
	   */
	  public function getDisplay() {

	  		 return $this->display;
	  }

	  /**
	   * Sets the default value of the column data
	   *
	   * @param mixed $value The default value if nothing has been assigned
	   * @return void
	   */
	  public function setDefault($value) {

	  		 $this->default = $value;
	  }

	  /**
	   * Returns the default value assigned to the column data when a value
	   * has not been specified.
	   *
	   * @return void
	   */
	  public function getDefault() {

	  		 return $this->default;
	  }

	  /**
	   * Sets a boolean flag used to toggle the visibility of the column. This
	   * is used to hide a particular field (such as a primary key) from a form
	   * when its rendered.
	   *
	   * @param bool $boolean The boolean flag indicating whether or not the column
	   * 				 	  should be rendered. True to render, false to hide.
	   * @return void
	   */
	  public function setVisible($boolean) {

	  		 $this->visible = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean indicator based on the visibility of the column.
	   *
	   * @return True if the column is visible, false otherwise
	   */
	  public function isVisible() {

	  		 return $this->visible === true ? true : false;
	  }

	  /**
	   * Enables/disables sortable column headers,
	   *
	   * @param $boolean True to enable sortable column header, false to render
	   * 				 column header as plain text.
	   * @return void
	   */
	  public function setSortable($boolean) {

	  		 return $this->sortable === true ? true : false;
	  }

	  /**
	   * Returns boolean flag indicating whether or not the column
	   * is sortable.
	   *
	   * @return bool True if the column is sortable, false otherwise
	   */
	  public function isSortable() {

	  		 return $this->sortable == true ? true : false;
	  }

	  /**
	   * Boolean flag indicating whether or not the column data is required
	   *
	   * @param $boolean True if this column requires a value, false otherwise
	   * @return void
	   */
	  public function setRequired($boolean) {

	  		 $this->required = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean flag indicating whether or not the column data is required
	   *
	   * @return bool True if a value is required, false if a value is not required
	   */
	  public function isRequired() {

	  		 return $this->required;
	  }

	  /**
	   * Boolean flag indicating whether or not the column data is an index
	   *
	   * @param $boolean True if this column is an index, false otherwise
	   * @return void
	   */
	  public function setIndex($boolean) {

	  		 $this->index = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean flag indicating whether or not the column is an index
	   *
	   * @return bool True if the column is indexed, false otherwise
	   */
	  public function isIndex() {

	  		 return $this->index;
	  }

	  /**
	   * Marks this column as a primary key
	   *
	   * @param bool $boolean True to mark this column as a primary key, false otherwise
	   * @return void
	   */
	  public function setPrimaryKey($boolean) {

	  		 $this->primaryKey = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean flag indicating whether or not the column is a primary key
	   *
	   * @return bool True if the column is a primary key, false otherwise
	   */
	  public function isPrimaryKey() {

	  		 return ($this->primaryKey === true) ? true : false;
	  }

	  /**
	   * Marks the column as an AUTO_INCREMENT column.
	   *
	   * @param bool $boolean True if the column contains AUTO_INCREMENT values, false otherwise
	   * @return void
	   */
	  public function setAutoIncrement($boolean) {

	  		 $this->autoIncrement = $boolean ? true : false;
	  }

	  /**
	   * Returns boolean flag indicating whether or not this column is an
	   * AUTO_INCREMENT field.
	   *
	   * @return bool True if the column is an AUTO_INCREMENT field, false otherwise
	   */
	  public function isAutoIncrement() {

	  		 return $this->autoIncrement === true ? true : false;
	  }

	  /**
	   * Marks the column as a foreign key column.
	   *
	   * @param ForeignKey $foreignKey A ForeignKey instance representing the relationship for this column
	   * @return void
	   */
	  public function setForeignKey(ForeignKey $foreignKey) {

	  		 $this->foreignKey = $foreignKey;
	  }

	  /**
	   * Returns boolean indicator based on whether or not the column is a foreign key field.
	   *
	   * @return bool True if the column is a foreign key field, false otherwise
	   */
	  public function isForeignKey() {

	  		 return $this->foreignKey ? true : false;
	  }

	  /**
	   * Returns boolean indicator based on whether or not the column is a foreign key field.
	   *
	   * @return bool True if the column has a foreign key field, false otherwise
	   */
	  public function hasForeignKey() {

	  		 return $this->isForeignKey() ? true : false;
	  }

	  /**
	   * Returns boolean indicator based on whether or not the column is a foreign key field.
	   *
	   * @return ForeignKey The ForeignKey instance representing the relationship for this column
	   */
	  public function getForeignKey() {

	  		 return $this->foreignKey;
	  }

	  /**
	   * Returns boolean flag indicating whether or not the column is of the data type 'bit'. This
	   * is a *special* data type which is used to render HTML checkboxes by the presentation tier.
	   *
	   * @return void
	   */
	  public function isBit() {

 		  	 return $this->getType() == 'bit';
	  }

	  /**
	   * Boolean flag indicating whether or not to sanitize input using the RequestScope component.
	   *
	   * @param boolean $bool
	   * @return voi
	   */
	  public function setSanitize($bool) {

	  		 $this->sanitize = $bool;
	  }

	  /**
	   * Sets the validator responsible for validating the data.
	   *
	   * @param string $validator The class name of the Validator responsible for verifying the integrity of the data
	   * @return void
	   */
	  public function setValidator($validator) {

	  		 $this->validator = $validator;
	  }

	  /**
	   * Returns the class name of the Validator responsible for validating the data.
	   *
	   * @return string $validator The validator responsible for verifying the integrity of the data
	   */
	  public function getValidator() {

	  		 return $this->validator;
	  }

	  /**
	   * Sets the renderer responsible for formatting data for presentation
	   *
	   * @param string $renderer The DataRenderer responsible for formatting the column data for presentation
	   * @return void
	   */
	  public function setRenderer($renderer) {

	  		 $this->renderer = $renderer;
	  }

	  /**
	   * Returns the DataRenderer responsible for formatting the column data for presentation
	   *
	   * @return string $renderer The renderer responsible for formatting the column data for presentation
	   */
	  public function getRenderer() {

	  		 return $this->renderer;
	  }

	  /**
	   * Sets the DataTransformer responsible for transforming data before committing to the database
	   *
	   * @param string $transformer The class name of the DataTransformer responsible for transforming the
	   *        column data before its committed to the database.
	   * @return void
	   */
	  public function setTransformer($transformer) {

	  		 $this->transformer = $transformer;
	  }

	  /**
	   * Returns the class name of the DataTransformer responsible for transforming data before committing to the database
	   *
	   * @return string $transformer The class name of the DataTransformer
	   */
	  public function getTransformer() {

	  		 return $this->transformer;
	  }

	  /**
	   * Sets an optional sequence name used for pgsql serial|bigserial fields
	   *
	   * @param string $name The sequence name
	   * @return void
	   */
	  public function setSequence($name) {

	         $this->sequence = $name;
	  }

	  /**
	   * Returns an optional sequence name used for pgsql serial|bigserial fields
	   *
	   * @return string The pgsql sequence name
	   */
	  public function getSequence() {

	         return $this->sequence;
	  }

	  /**
	   * Returns boolean flag indicating whether or not this column should have its data sanitized by default.
	   *
	   * @return boolean False if orm.xml contains a sanitize="false" for this column, true otherwise.
	   */
	  public function getSanitize() {

	  		 return $this->sanitize;
	  }

	  /**
	   * If enabled, referenced objects are not fetched, persisted, merged, or deleted with child operations.
	   *
	   * @param bool $bool True to enable lazy loading, false otherwise. Defaults to true.
	   * @return void
	   */
	  public function setLazy($bool = true) {

	  		 $this->lazy = $bool;
	  }

	  /**
	   * Returns flag indicating whether or not referenced objects are not fetched, persisted, merged, or deleted with child operations.
	   *
	   * @return bool True if lazy loading is enabled for this column, false otherwise.
	   * @return void
	   */
	  public function isLazy() {

	  		 return $this->lazy;
	  }

	  /**
	   * Returns the name which is used to access/mutate model properties/fields. If a property
	   * attribute has been configured in orm.xml for the column, the property value is
	   * returned, otherwise the column name is returned instead.
	   *
	   * @return String Property attribute value in orm.xml for the column if it exists, else
	   * 		 the column name attribute value is returned instead.
	   */
	  public function getModelPropertyName() {

	  		 return $this->getProperty() == null ? $this->getName() : $this->getProperty();
	  }

	  /**
	   * If orm.xml contains a valid 'display' attribute, this value is
	   * returned, otherwise, the column name is returned.
	   *
	   * @return String The orm.xml 'display' attribute value if it exists, otherwise the column name
	   */
	  public function getViewDisplayName() {

	  		 return $this->getDisplay() ? $this->getDisplay() : ucfirst($this->getName());
	  }
}
?>