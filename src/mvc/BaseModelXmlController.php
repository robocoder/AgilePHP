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
 * @package com.makeabyte.agilephp.mvc
 */

/**
 * Provides base implementation for model xml controllers.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @version 0.1a
 * @abstract
 */
abstract class BaseModelXmlController extends BaseModelController {

		 protected function __construct() {

		 		   parent::__construct();
		 }

		 /**
		  * Creates an XML document representing a model. If the 'id' parameter is set, a
		  * lookup is performed for the model with the specified 'id' and the XML is returned with
		  * data populated from the database result set.. If there is no 'id' set, the model's property
		  * nodes will be null. A custom controller and action can be set to modify default behavior.
		  * 
		  * @param String $controller Optional controller to use for add/update/delete operations. Defaults to the controller
		  *   						  that invoked this method.
		  * @param String $action The controllers action method to invoke. Defaults to the model name (lowercase) followed
		  * 			  		  by the action mode 'Add' or 'Edit'. For example, a user model would be either 'userAdd' or 'userEdit'.
		  * @param array $params An array of parameters to pass into the action method 
	      */
	     protected function getModelAsFormXML( $controller = null, $action = null, $params = null ) {

  			 	   $thisController = new ReflectionClass( $this );
  			 	   $c = ($controller) ? $controller : $thisController->getName();
  			 	   $a = ($action) ? $action : 'modelAction';

  			 	   $xml = '<Form>
  			 	   			<' . $this->getModelName() . '>';

  			 	   $fieldCount = 0;

  			 	   $isMerge = false;
  			 	   $table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );
  			 	   $pkeyColumns = $table->getPrimaryKeyColumns();
  			 	   foreach( $pkeyColumns as $column ) {

  			 	   	        $accessor = $this->toAccessor( $column->getName() );
  			 	   	        if( $this->getModel()->$accessor() )
  			 	   	        	$isMerge = true;
  			 	   }

  			 	   if( $isMerge ) {

  			 	   	   $models = $this->getPersistenceManager()->find( $this->getModel() );

  			 	   	   foreach( $table->getColumns() as $column ) {

  			 	   	   			$accessor = $this->toAccessor( $column->getModelPropertyName() );

  			 	   	   			$fieldCount++;
  			 	   	   	     	if( is_object( $models[0]->$accessor() ) ) continue;

  			 	   	   	     	$xml .= ($column->getType() == 'bit') ? 
  			 	   	   	     				'<' . $column->getModelPropertyName() . '>' . ( (ord($models[0]->$accessor()) == 1) ? '1' : '0') . '</' . $column->getModelPropertyName() . '>'
  			 	   	   	     				: '<' . $column->getModelPropertyName() . '>' . $models[0]->$accessor() . '</' . $column->getModelPropertyName() . '>';
  			 	   	   			
  			 	   	   }
  			 	   }
  			 	   else {

  			 	   	   $modelRefl = new ReflectionClass( $this->getModelName() );
  			 	   	   $properties = $modelRefl->getProperties();

  			 	   	   foreach( $properties as $property ) {

  			 	   	   			$fieldCount++;
		     		   	        $xml .= '<' . $property->name . '/>';
  			 	   	   }
  			 	   }

	  			   $xml .= '</' . $this->getModelName() . '>
	  			 	   		<controller>' . $c . '</controller>
	  			 	   		<action>' . $a . '</action>';
	  			   $xml .= ($params ? '<params>' . $params . '</params>' : '');
	  			   $xml .= '<fieldCount>' . $fieldCount . '</fieldCount>
	  			 	   	</Form>';

	  			   Logger::getInstance()->debug( 'BaseModelXmlController::getModelAsFormXML called with parameters controller = ' . $controller . ', action = ' . $action );
	  			   Logger::getInstance()->debug( 'BaseModelXmlController::getModelAsFormXML returning xml ' . $xml );

  			 	   return $xml;
	     }

		 /**
	      * Returns a result set from the database as XML. The XML document is returned
	      * with the root node 'ResultList' containing an element named after the model
	      * which then contains each of the models properties and values as children.
	      * For example:
	      * <ResultList>
	      * 	<your_model_name>
	      * 		<model_prop1>*</model_prop1>
	      * 		<model_prop2>*</model_prop2>
	      * 	</your_model_name>
	      * </ResultList>. 
	      * 
	      * @return An XML document representing the result list
	      */
	     protected function getResultListAsXML() {

	     		   $table = $this->getPersistenceManager()->getTableByModelName( $this->getModelName() );

	  	     	   $doc = new DomDocument( '1.0' );
      	     	   $xml = $doc->createElement( 'ResultList' );
             	   $xml = $doc->appendChild( $xml );

             	   if( !$this->getResultList() )
             	   	   throw new AgilePHP_Exception( 'BaseModelXmlController::getResultListAsXml() requires a valid result set to transform to XML.' );

	     		 	foreach( $this->getResultList() as $model ) {

             	   	   		    $class = new ReflectionClass( $model );

             	   	   			$modelName = $doc->createElement( $this->getModelName() );
				 	   	        $xml->appendChild( $modelName );

				 	   	        // Process foreign keys
             	   	   			if( $table->hasForeignKey() ) {

					      		    $bProcessedKeys = array();
					   		  	    $fKeyColumns = $table->getForeignKeyColumns();
					   		  	    for( $i=0; $i<count( $fKeyColumns ); $i++ ) {

					   	  	  		  	 $fk = $fKeyColumns[$i]->getForeignKey();

					   		  	  		 if( in_array( $fk->getName(), $bProcessedKeys ) )
					   		  	  		     continue;

					     	  	       	 // Get foreign keys which are part of the same relationship
					     	  	       	 $relatedKeys = $table->getForeignKeyColumnsByKey( $fk->getName() );

				         		   	 	 $foreignModelName = $doc->createElement( $relatedKeys[0]->getReferencedTableInstance()->getModel() );
				    	        		 $foreignModel = $modelName->appendChild( $foreignModelName );

					         		   	 for( $j=0; $j<count( $relatedKeys ); $j++ ) {

									  		   try {
									  		   		  // The model has been intercepted
									  		 		  $class = new ReflectionClass( $model->getInterceptedInstance() );
									  		 		  $props = $class->getProperties();
								       	       		  foreach( $props as $prop ) {
					
								       	       		  		   $accessor = $this->toAccessor( $prop->getName() );
								       	       		  		   $val = $model->$accessor();
								       	       		 	  	   if( $prop->getName() == $relatedKeys[$j]->getColumnInstance()->getName() ) {
								
								     	  	       		   	 	   $child = $doc->createElement( $relatedKeys[$j]->getReferencedColumnInstance()->getModelPropertyName() );
													  			   $child = $foreignModel->appendChild( $child );
											                  	   $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
															  	   $value = $doc->createTextNode( $fieldvalue );
															  	   $value = $child->appendChild( $value );
								       	       		   	 	   }
								       	       		   }
								   	  	  		   	   array_push( $bProcessedKeys, $fk->getName() );
									  		 	}
									  		 	catch( ReflectionException $e ) {

									  		 		   // The model is not intercepted
									  		 		   $props = $class->getProperties();
								       	       		   foreach( $props as $prop ) {

								       	       		  		    $accessor = $this->toAccessor( $prop->getName() );
								       	       		  		    $val = $model->$accessor();
								       	       		 	  	    if( $prop->getName() == $relatedKeys[$j]->getColumnInstance()->getName() ) {
								
								     	  	       		   	 	    $child = $doc->createElement( $relatedKeys[$j]->getReferencedColumnInstance()->getModelPropertyName() );
													  			    $child = $foreignModel->appendChild( $child );
											                  	    $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
															  	    $value = $doc->createTextNode( $fieldvalue );
															  	    $value = $child->appendChild( $value );
								       	       		   	 	    }
								       	       		    }
								   	  	  		   	    array_push( $bProcessedKeys, $fk->getName() );
									  		 	 }	
					         		   	 }
					   		  	     }
					             }

                   				 // Process the parent model
					  		     try {
					  		     	  // The model has been intercepted
					  		   		  $m = $class->getMethod( 'getInterceptedInstance' );
					  		   		  $class = new ReflectionClass( $model->getInterceptedInstance() );
					  		   		  $props = $class->getProperties();

				       	       		     foreach( $props as $prop ) {

				       	       		  		      $accessor = $this->toAccessor( $prop->getName() );
				       	       		  		      $val = $model->$accessor();

				       	       		  		      if( is_object( $val ) ) continue; // Foreign model

				       	       		  		      if( $this->isBit( $table, $prop->getName() ) )
				 	   	   			         		  $val = (ord($val) == 1) ? 'Yes' : 'No';

						 	   	   			 	  $child = $doc->createElement( $prop->getName() );
							  				 	  $child = $modelName->appendChild( $child );
					                  		 	  $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
									  		 	  $value = $doc->createTextNode( $fieldvalue );
									  		 	  $value = $child->appendChild( $value );
				       	       		     }
					  		 	  }
					  		 	  catch( ReflectionException $e ) {

					  		 	  		 // This model is not intercepted
					  		 	  		 $props = $class->getProperties();
				       	       		     foreach( $props as $prop ) {

				       	       		  		      $accessor = $this->toAccessor( $prop->getName() );
				       	       		  		      $val = $model->$accessor();

				       	       		  		      if( is_object( $val ) ) continue; // Foreign model

				       	       		  		      if( $this->isBit( $table, $prop->getName() ) )
				 	   	   			         		  $val = (ord($val) == 1) ? 'Yes' : 'No';

						 	   	   			 	  $child = $doc->createElement( $prop->getName() );
							  				 	  $child = $modelName->appendChild( $child );
					                  		 	  $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
									  		 	  $value = $doc->createTextNode( $fieldvalue );
									  		 	  $value = $child->appendChild( $value );
				       	       		     }
					  		 	  }
             	   	   }

			 	   $xml = $doc->saveXML();

	  			   return $xml;
	     }

		 /**
	      * Returns a paged result set from the database as XML (including foreign model instances with their
	      * primary keys set). The XML document is returned in the following format:
	      * 
	      * <ResultList>
	      * 	<Model>
	      * 		<your_model_name>
		  *     		<foreign_model_name>
		  *     			<primary_key1>*</primary_key1>
		  *     			<primary_key2>*</primary_key2> 
		  *     		</foreign_model_name>
	      * 			<model_prop1>*</model_prop1>
	      * 			<model_prop2>*</model_prop2>
	      *			</your_model_name>
	      *		</Model>
	      *		<Pagination>
	      *			<page>*</page>
	      *	        <pageCount>*</pageCount>
	      *	        <nextExists>*</nextExists>
	      *	       	<previousExists>*</previousExists>
	      *			<controller>*</controller>
	      *			<action>*</action>
	      *		</Pagination>
	      * </ResultList> 
	      * 
	      * @return An XML document representing the result list
	      */
	     protected function getResultListAsPagedXML( $controller = null, $action = null, $params = null ) {

	     		   $c = (!$controller) ? new ReflectionClass( $this ) : new ReflectionClass( $controller );
   		   		   $a = (!$action) ? 'modelList' : $action;
   		   		   $table = $this->getPersistenceManager()->getTableByModelName( $this->getModelName() );

   		   		   $fkeyColumns = $table->getForeignKeyColumns();
   		   		   $hasFkeyColumns = count( $fkeyColumns ) > 0 ? true : false;

	  	     	   $doc = new DomDocument( '1.0' );
      	     	   $root = $doc->createElement( 'ResultList' );
             	   $root = $doc->appendChild( $root );

             	   $xml = $doc->createElement( 'Model' );
             	   $xml = $root->appendChild( $xml );

             	   if( $this->getResultList() ) {

             	   	   foreach( $this->getResultList() as $model ) {

             	   	   		    $class = new ReflectionClass( $model );

             	   	   			$modelName = $doc->createElement( $this->getModelName() );
				 	   	        $xml->appendChild( $modelName );

				 	   	        // Process foreign keys
             	   	   			if( $table->hasForeignKey() ) {

					      		    $bProcessedKeys = array();
					   		  	    $fKeyColumns = $table->getForeignKeyColumns();
					   		  	    for( $i=0; $i<count( $fKeyColumns ); $i++ ) {

					   	  	  		  	 $fk = $fKeyColumns[$i]->getForeignKey();

					   		  	  		 if( in_array( $fk->getName(), $bProcessedKeys ) )
					   		  	  		     continue;

					     	  	       	 // Get foreign keys which are part of the same relationship
					     	  	       	 $relatedKeys = $table->getForeignKeyColumnsByKey( $fk->getName() );

				         		   	 	 $foreignModelName = $doc->createElement( $relatedKeys[0]->getReferencedTableInstance()->getModel() );
				    	        		 $foreignModel = $modelName->appendChild( $foreignModelName );

					         		   	 for( $j=0; $j<count( $relatedKeys ); $j++ ) {

									  		   try {
									  		   		  // The model has been intercepted
									  		 		  $class = new ReflectionClass( $model->getInterceptedInstance() );
									  		 		  $props = $class->getProperties();
								       	       		  foreach( $props as $prop ) {
					
								       	       		  		   $accessor = $this->toAccessor( $prop->getName() );
								       	       		  		   $val = $model->$accessor();
								       	       		 	  	   if( $prop->getName() == $relatedKeys[$j]->getColumnInstance()->getName() ) {
								
								     	  	       		   	 	   $child = $doc->createElement( $relatedKeys[$j]->getReferencedColumnInstance()->getModelPropertyName() );
													  			   $child = $foreignModel->appendChild( $child );
											                  	   $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
															  	   $value = $doc->createTextNode( $fieldvalue );
															  	   $value = $child->appendChild( $value );
								       	       		   	 	   }
								       	       		   }
								   	  	  		   	   array_push( $bProcessedKeys, $fk->getName() );
									  		 	}
									  		 	catch( ReflectionException $e ) {

									  		 		   // The model is not intercepted
									  		 		   $props = $class->getProperties();
								       	       		   foreach( $props as $prop ) {

								       	       		  		    $accessor = $this->toAccessor( $prop->getName() );
								       	       		  		    $val = $model->$accessor();
								       	       		 	  	    if( $prop->getName() == $relatedKeys[$j]->getColumnInstance()->getName() ) {
								
								     	  	       		   	 	    $child = $doc->createElement( $relatedKeys[$j]->getReferencedColumnInstance()->getModelPropertyName() );
													  			    $child = $foreignModel->appendChild( $child );
											                  	    $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
															  	    $value = $doc->createTextNode( $fieldvalue );
															  	    $value = $child->appendChild( $value );
								       	       		   	 	    }
								       	       		    }
								   	  	  		   	    array_push( $bProcessedKeys, $fk->getName() );
									  		 	 }	
					         		   	 }
					   		  	     }
					             }

                   				 // Process the parent model
					  		     try {
					  		     	  // The model has been intercepted
					  		   		  $m = $class->getMethod( 'getInterceptedInstance' );
					  		   		  $class = new ReflectionClass( $model->getInterceptedInstance() );
					  		   		  $props = $class->getProperties();

				       	       		     foreach( $props as $prop ) {

				       	       		  		      $accessor = $this->toAccessor( $prop->getName() );
				       	       		  		      $val = $model->$accessor();

				       	       		  		      if( is_object( $val ) ) continue; // Foreign model

				       	       		  		      if( $this->isBit( $table, $prop->getName() ) )
				 	   	   			         		  $val = (ord($val) == 1) ? 'Yes' : 'No';

						 	   	   			 	  $child = $doc->createElement( $prop->getName() );
							  				 	  $child = $modelName->appendChild( $child );
					                  		 	  $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
									  		 	  $value = $doc->createTextNode( $fieldvalue );
									  		 	  $value = $child->appendChild( $value );
				       	       		     }
					  		 	  }
					  		 	  catch( ReflectionException $e ) {

					  		 	  		 // This model is not intercepted
					  		 	  		 $props = $class->getProperties();
				       	       		     foreach( $props as $prop ) {

				       	       		  		      $accessor = $this->toAccessor( $prop->getName() );
				       	       		  		      $val = $model->$accessor();

				       	       		  		      if( is_object( $val ) ) continue; // Foreign model

				       	       		  		      if( $this->isBit( $table, $prop->getName() ) )
				 	   	   			         		  $val = (ord($val) == 1) ? 'Yes' : 'No';

						 	   	   			 	  $child = $doc->createElement( $prop->getName() );
							  				 	  $child = $modelName->appendChild( $child );
					                  		 	  $fieldvalue = mb_convert_encoding( html_entity_decode( $val ), 'UTF-8', 'ISO-8859-1' );
									  		 	  $value = $doc->createTextNode( $fieldvalue );
									  		 	  $value = $child->appendChild( $value );
				       	       		     }
					  		 	  }
             	   	   }
             	   }

			 	   $pagination = $doc->createElement( 'Pagination' );
			 	   $pagination = $root->appendChild( $pagination );

			 	   $page = $doc->createElement( 'page', $this->getPage() );
			 	   $pagination->appendChild( $page );

			 	   $pageCount = $doc->createElement( 'pageCount', $this->getPageCount() );
			 	   $pagination->appendChild( $pageCount );
			 	   
			 	   $nextExists = $doc->createElement( 'nextExists', ($this->nextExists() == true) ? 1 : 0 );
			 	   $pagination->appendChild( $nextExists );

			 	   $prevExists = $doc->createElement( 'previousExists', ($this->previousExists()) ? 1 : 0 );
			 	   $pagination->appendChild( $prevExists );

			 	   $resultCount = $doc->createElement( 'resultCount', $this->getResultCount() );
			 	   $pagination->appendChild( $resultCount );

			 	   $recordCount = $doc->createElement( 'recordCount', $this->getCount() );
			 	   $pagination->appendChild( $recordCount );

			 	   $start = ($this->getPage() * $this->getMaxResults()) - ($this->getMaxResults() - 1);
			 	   if( !$this->getCount() ) $start = 0;

			 	   $recordStart = $doc->createElement( 'recordStart', ($start <= 0) ? 0 : $start );
			 	   $pagination->appendChild( $recordStart );

			 	   $end = $start + ($this->getMaxResults() - 1);
			 	   if( $end > $this->getCount() ) $end = $this->getCount();

			 	   $recordEnd = $doc->createElement( 'recordEnd', $end );
			 	   $pagination->appendChild( $recordEnd );

			 	   $controller = $doc->createElement( 'controller', $c->getName() );
			 	   $pagination->appendChild( $controller );

			 	   $action = $doc->createElement( 'action', $a );
			 	   $pagination->appendChild( $action );

			 	   if( $params ) {

			 	   	   $paramz = $doc->createElement( 'params', $params );
			 	   	   $pagination->appendChild( $paramz );
			 	   }

	  			   $xml = $doc->saveXML();

	  			   return $xml;
	     }

	     /**
	      * Returns the type of action which the controllers should take when deciphering
	      * whether the operation is a persist or merge operation. If the primary key(s)
	      * contain a value, the action is assumed a merge. If the primary key(s) do not
	      * contain a value, the action is assumed persist.
	      * 
	      * @return 'persist' if the primary key value(s) are not present, 'merge' if
	      * 	    the primary keys are present.
	      */
	     protected function getModelPersistenceAction() {

	     		   $table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );
	     		   $pkeyColumns = $table->getPrimaryKeyColumns();
  			 	   foreach( $pkeyColumns as $column ) {

  			 	   			$accessor = 'get' . ucfirst( $column->getModelPropertyName() );
  			 	   			if( !$this->getModel()->$accessor() )
  			 	   				return 'persist';
  			 	   }

  			 	   return 'merge';
	     }

		 /**
		  * Returns boolean response based on the configured 'type' attribute for the specified column.
		  *  
		  * @param Table $table The Table instance containing the column
		  * @param String $columnName The name of the column to search
		  * @return bool True if the column's 'type' attribute is set to 'bit', false otherwise.
		  */
		 private function isBit( $table, $columnName ) {
	
		  		 foreach( $table->getColumns() as $column )
		  		 		  if( $column->getName() == $columnName )
		  		 		  	  return $column->getType() == 'bit';

		  		 return false;
		  }
}
?>