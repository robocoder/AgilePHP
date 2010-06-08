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
 * @package com.makeabyte.agilephp.webservice.rest
 */

/**
 * Transforms JSON string data into a populated domain model.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.webservice.rest
 */
class JSONTransformer implements Transformer {

	  /**
	   * Transforms the specified data into a populated domain model.
	   * 
	   * @param string $data The string data which represents the domain model
	   * 					 and state to create.
	   * @return Object The domain model specified in the string $data
	   */
	  public function transform( $data ) {

	  		 $o = json_decode( $data );

	  		 if( !is_object( $o ) ) {

	  		 	 Log::debug( 'JSONTransformer::transform Received malformed data ' . $data );
	  		 	 throw new RestServiceException( 406 );
	  		 }

	  		 $vars = get_object_vars( $o );
	  		 $modelName = key( $vars );

	  		 return $this->convert( $modelName, $vars[$modelName] );
	  }

	  /**
	   * Accepts a model name and a stdClass object and creates a new model
	   * instance and copies the data from the stdClass into the model instance.
	   * 
	   * @param string $modelName The name of the domain model to instantiate
	   * @param stdClass $jsonObject A stdClass object which represents a JSON decoded object
	   */
	  private function convert( $modelName, $jsonObject ) {

			  $model = new $modelName();

	  		  $values = get_object_vars( $jsonObject );
	  		  foreach( $values as $field => $value ) {

	  		  		   $mutator = 'set' . ucfirst( $field );
	  		  		   $model->$mutator( (is_object( $value ) ? $this->convert( $field, $value ) : $value) );
	  		  }

	  		  return $model;
	  }	  
}
?>