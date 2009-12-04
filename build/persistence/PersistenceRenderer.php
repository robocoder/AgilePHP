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
 * AgilePHP :: PersistenceRenderer
 * Handles rendering persisted data to HTML
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence
 * @version 0.1a
 */
class PersistenceRenderer {

	  private function __construct() { }

	  /**
	   * Creates an HTML input form element for the specified column.
	   * 
	   * @param $tableElement SimpleXML table element containing the column
	   * @param $columnName The name of the column to render as an HTML input form element
	   * @param $value An optional value to assign to the input element
	   * @param $var A 'special' variant variable used in rendering algorithm. The variable has
	   * 			 a different meaning for each of the contexts it is invoked.
	   * @return An HTML input form element.
	   */
	  public static function toFormElement( $table, $columnName, $value, $var = null ) {

	  		 $value = mb_convert_encoding( html_entity_decode( $value ), 'UTF-8', 'ISO-8859-1' );

	  		 foreach( $table->getColumns() as $column ) {

			  		  $name = $column->getModelPropertyName();

	  		 	      if( $name == $columnName || $columnName == 'password1' || $columnName == 'password2' ) {

	  		 	      	  // Primary key during merge is read only
	  		 	      	  if( $column->isPrimaryKey() )
	  		 	      	      if( $var == 'merge' )
	  		 	      	  	  	  return '<input type="text" readonly="true" name="' . $name . '" value="' . $value . '"/>';

	  		 	      	  // Password field
	  		 	      	  if( $var === 'password' || $columnName == 'password' )
	  		 	      	      return '<input type="password" name="' . $columnName . '" value="' . $value . '"/>';

	  		 	      	  // Auto-increment
	  		 	      	  if( $column->isAutoIncrement() ) {

							  if( $var == 'merge' )
	  		 	      	  	  	  return '<input type="text" readonly="true" name="' . $name . '" value="' . $value . '"/>';

	  		 	      	  	  return '<input type="text" name="' . $name . '" value="' . $value . '"/>';
	  		 	      	  }

	  		 	      	  // bit = checkbox
						  if( $column->getType() == 'bit' ) {

						  	  return ($var > 0) ?
							          '<input type="checkbox" checked="true" name="' . $name . '" value="1"/>'
						  	  	      :
						  	  	      '<input type="checkbox" name="' . $name . '" value="1"/>';
						  }

						  // Textarea
						  if( $column->getType() == 'text' ) {

						  	  $val = str_replace( '{', '', $value );
						  	  $val = str_replace( '}', '', $val );
							  return '<textarea onclick="this.value=\'\'" name="' . $columnName . '"><xsl:value-of select="' . $val . '"/></textarea>';
						  }

						  // file = file upload
						  if( $column->getType() == 'blob' )
							  return '<input type="file" name="' . $columnName . '" value="' . $value . '"/>';
							  
	  		 	      	  // Default element
		  		  		  return '<input type="text" name="' . $name . '" value="' . $value . '"/>';
	  		 	      }
	  		  }
	  }

	  /**
	   * Returns boolean response based on the configured 'type' attribute for the specified column.
	   *  
	   * @param $table The 'Table' object which contains the column
	   * @param $columnName The name of the column to search
	   * @return True if the column's 'type' attribute is set to 'bit', false otherwise.
	   */
	  public static function isBit( $table, $columnName ) {

	  		 foreach( $table->getColumns() as $column )
	  		 		  if( $column->getName() == $columnName )
	  		 		  	  return $column->getType() == 'bit';

	  		 return false;
	  }
}