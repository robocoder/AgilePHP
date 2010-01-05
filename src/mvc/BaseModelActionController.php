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
 * Provides a base implementation of a working model action controller. 
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @version 0.1a
 * @abstract
 */
abstract class BaseModelActionController extends BaseModelXslController {

		 private $xsltRenderer;

		 /**
		  * Base constructor which allows configuration options in extended classes. 
		  * 
		  * @param bool $requireLogon True to require the user to be logged in, false to allow calls
		  * 			to unauthenticated calls (uses AgilePHP Identity component to validate logged in session).
		  * @param String $requiredRole An optional role to require
		  * @return void
		  */
	     protected function __construct( $requireLogon = true, $requiredRole = 'admin' ) {

	     	       if( $requireLogon ) {

		     		   if( !Identity::getInstance()->isLoggedIn() )
		  	     		   throw new AgilePHP_NotLoggedInException( 'Login Required' );

			  	       if( !Identity::getInstance()->hasRole( $requiredRole ) )
			  	     	   throw new AgilePHP_AccessDeniedException( 'Access Denied. This area is reserved for ' . $requiredRole );
	     		   }

	     		   parent::__construct();
	     		   $this->xsltRenderer = MVC::getInstance()->createRenderer( 'XSLTRenderer' );
	     		   $this->getRenderer()->set( 'title', 'Administration :: ' . $this->getModelName() );
	     }

	     /**
	      * Returns an instance of the MVC XSLTRenderer object.
	      * 
	      * @return An instance of XSLTRenderer
	      */
	     protected function getXsltRenderer() {

	     		   return $this->xsltRenderer;
	     }

	     /**
	      * Handles the dispatching of action events from the view.
	      * 
	      * @param String $action The action event type - add, edit, merge, delete
	      * @param String $ids The primary key values associated with the ActiveRecord.
	      * @param Integer $page A page number to load (keeps result list state when
	      * 			  performing updates/deletes)
	      * @param String $view The 'view' to render. Default is 'admin'.
	      * @return void
	      */
	     public function modelAction( $action, $ids = '', $page = 1 , $view = 'admin' ) {

	     		$this->setPrimaryKeys( $ids );

	     		switch( $action ) {

		     			case 'filteredList':
		     				$table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );
		     				$class = new ReflectionClass( $this->getModel() );
		     				foreach( $class->getProperties() as $property ) {

		     						 $accessor = $this->toAccessor( $property->name );
		     						 if( $this->getModel()->$accessor() )
		     						 	 $this->setRestrictions( array( $this->getPersistenceManager()->getColumnNameByProperty( $table, $property->name ) => $this->getModel()->$accessor() ) );
		     				}
		     				
		     				$this->setPage( $page );

				  		    $content = $this->getXsltRenderer()->transform( $this->getModelListXSL(), $this->getResultListAsPagedXML() );

				  	        $this->getRenderer()->set( 'content', $content );
				  	        $this->getRenderer()->render( $view );
				  	        break;

	     				case 'read':
			  	     		 $this->getRenderer()->set( 'content', $this->getXsltRenderer()->transform( $this->getModelAsReadOnlyXSL(), $this->getModelAsFormXML() ) );
			  	     	     $this->getRenderer()->render( $view );
	     					 break;

		  	    	    case 'add':
		  	    	    	 $this->setPage( $page );
			  	     		 $this->getRenderer()->set( 'content', $this->getXsltRenderer()->transform( $this->getModelFormXSL(), $this->getModelAsFormXML() ) );
			  	     	     $this->getRenderer()->render( $view );
			  	     	     break;

 		  	     	    case 'edit':
 		  	     	    	 $this->setPage( $page );
	  	     	     		 $this->getRenderer()->set( 'content', $this->getXsltRenderer()->transform( $this->getModelFormXSL(), $this->getModelAsFormXML() ) );
	  	     	     		 $this->getRenderer()->render( $view );
	  	     		 		 break;

	  	     			case 'persist':
	  	     				 $this->setPage( $page );
	  	     		 		 $this->persist();
	  	     		 		 break;

	  	     			case 'merge':
	  	     				 $this->setPage( $page );
	  	     		 		 $this->merge();
	  	     		 		 break;

	  	     			case 'delete':
	  	     				 $this->setPage( $page );
	  	     		 		 $this->delete();
	  	     		 		 break;

	  	     			default:
	  	     				throw new AgilePHP_Exception( 'Invalid model action \''. $action . '\'.' );
	  	     				break;
     		   }
	     }

	     /**
	      * Performs a search for the model defined in the extension class and displays a
	      * paginated result list with edit and delete actions, as well as sortable
	      * column headers.
	      * 
	      * @param Integer $page The page number within the result set to display. Default is page 1.
	      * @param String $view The view to render. Default is 'admin'.
	      * @return void
	      */
	     public function modelList( $page = 1, $view = 'admin', $model = null ) {

			    if( $model ) {

	     			$this->setModel( new $model() );
	     			$this->__construct();
	     		}

	  		    $this->setPage( $page );

	  		    $content = $this->getXsltRenderer()->transform( $this->getModelListXSL(), $this->getResultListAsPagedXML() );

	  	        $this->getRenderer()->set( 'content', $content );
	  	        $this->getRenderer()->render( $view );
	     }

	     /**
	      * Sorts the modelList according to the specified column name and direction.
	      * 
	      * @param String $column The column name to sort on
	      * @param String $direction The direction to sort. Default is 'ASC' (ascending). (ASC|DESC)
	      * @param Integer $page The page within the result set to display.
	      * @param String $view The view to render. Default is 'admin'.
	      * @return void
	      */
	     public function modelSort( $column, $direction, $page = 1, $view = 'admin' ) {

	     		$this->setOrderBy( $column, $direction );
	     		$this->setPage( $page );
				$this->setOrderBy( $column, ($direction == 'ASC') ? 'DESC' : 'ASC' );	     		
	     		
	     		$content = $this->getXsltRenderer()->transform( $this->getModelListXSL(), $this->getResultListAsPagedXML() );

	  	        $this->getRenderer()->set( 'content', $content );
	  	        $this->getRenderer()->render( $view );
	     }

	     /**
	      * Persists a the ActiveRecord state defined by the model in the
	      * extension class.
	      * 
	      * @return void
	      */
	     public function persist() {

	     		$this->setModelValues();

	    	    parent::persist( $this->getModel() );
	  	 	    $this->modelList( $this->getPage() );
	     }

 	     /**
	      * Merges the ActiveRecord state defined by the model in the
	      * extension class.
	      * 
	      * @return void
	      */
	     public function merge() {

	  		    $this->setModelValues();

	  	 	    parent::merge( $this->getModel() );
	  	 	    $this->modelList( $this->getPage() );
	     }

	     /**
	      * Deletes the ActiveRecord state defined by the model in the
	      * extension class. 
	      * 
	      * @return void
	      */
	     public function delete() {

	  		    $this->setModelValues();

	  		    parent::delete( $this->getModel() );
	  		    $this->modelList( $this->getPage() );
	     }

	     /**
	      * Parses $ids sent in by the modelAction method and sets all primary keys
	      * in the model defined in the extension class.
	      * 
	      * @param String $ids The 'ids' as passed in from the form submit. These 'ids' are
	      * 			       automagically generated by BaseModelXslController.
	      * @return void
	      */
	     protected function setPrimaryKeys( $ids ) {

	  		       if( $ids == null ) return;

	  	           $table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );
	  	           $pkeyColumns = $table->getPrimaryKeyColumns();

  			       // Single primary key
	  	           if( strpos( $ids, '_' ) === false ) {

		  	           $mutator = 'set' . ucfirst( $pkeyColumns[0]->getModelPropertyName() );
	  		 	       $this->getModel()->$mutator( $ids );
	  		 	       return;
	  		       }

	  		       // Compound primary key
			       $idz = explode( '_', $ids );
	  	           for( $i=0; $i<count( $idz ); $i++ ) {

	  	     	 	    $mutator = 'set' . ucfirst( $pkeyColumns[$i]->getModelPropertyName() );
	  	     	 	    $this->getModel()->$mutator( $idz[$i] );
	  	           }
	     }

		 /**
	      * Uses the AgilePHP RequestScope component to retrieve POST parameters
	      * from a form submit and set the model properties defined in the extension
	      * class. This method expects the form element names to match the name
	      * of the model's property.
	      * 
	      * @return void
	      */
	     protected function setModelValues() {

	  		       $request = Scope::getInstance()->getRequestScope();
	     	       $table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );

	  		       if( !$request->getParameters() )
	  		  	       return;

	  		       foreach( $request->getParameters() as $name => $value ) {

	  		     		    $mutator = $this->toMutator( $name );

	  		     		    if( $name == 'AGILEPHP_REQUEST_TOKEN' ) continue;

	  		     		    if( $name == 'password1' || $name == 'password2' ) {

	  		     		  	    if( $name == 'password2' ) continue;

	  		     		  	    if( $request->getSanitized( 'password1' ) !== $request->getSanitized( 'password2' ) ) {

	  		     		  	 	    $this->getRenderer()->set( 'error', 'Passwords dont match' );
	  		     		  	  	    $this->getRenderer()->render( 'error' );
	  		     		  	  	    return;
	  		     		  	    }

	  		     		  	    $hashed = Crypto::getInstance()->getDigest( $value );
	  		     		  	    $this->getModel()->setPassword( $hashed );

	  		     		  	    if( Identity::getInstance()->isLoggedIn() )
	  		     		  	        Identity::getInstance()->setPassword( $value ); // identity takes care of hashing

	  		     		  	    continue;
	  		     		    }

	  		     		  	$value = $request->sanitize( $value ); // Sanitize user input by default
	  		 	  	        foreach( $table->getColumns() as $column ) {

		     		   			   // Bit columns/checkboxes dont get sent with the post if they are unchecked.
		     		   			   // This is a hack to make sure the 'off' bit gets set.
		     		   			   if( $column->getType() == 'bit' ) {

									   if( !$request->getSanitized( $name ) ) {

									   	   $this->getModel()->$mutator( 0 );
									   	   continue;
									   }
		     		   			   }

		     		   			   // Type cast to PHP data type from database types in persistence.xml
		     		   			   if( $column->getModelPropertyName() == $name ) {

				     		   			   switch( $column->getType() ) {

							 	       	  		   case 'boolean':
							 	       	  		  		$this->getModel()->$mutator( $value == true ? true : false );
							 	       	  		  		break;
				
							 	       	  		   case 'integer':
							 	       	  		  		$this->getModel()->$mutator( (integer)$value );
							 	       	  		  		break;
							
							 	       	  		   case 'int':
							 	       	  		  		$this->getModel()->$mutator( (int)$value );
							 	       	  		  		break;
							 	       	  		  		 
							 	       	  		   case 'bigint':
														$this->getModel()->$mutator( $this->getPersistenceManager()->toBigInt( $value ) );
														break;
														
							 	       	  		   case 'double':
							 	       	  		  		$this->getModel()->$mutator( (float)$value );
							 	       	  		  		break;
							 	       	  		  		
							 	       	  		   case 'decimal':
							 	       	  		  		$this->getModel()->$mutator( (float)$value );
							 	       	  		  		break;

							 	       	  		   case 'varchar':
							 	       	  		   		$this->getModel()->$mutator( (string)$value );
							 	       	  		   		break;

							 	       	  		   case 'float':
							 	       	  		  		$this->getModel()->$mutator( (float)$value );
							 	       	  		  		break;
				
							 	       	  		   case 'text':
							 	       	  		  		$this->getModel()->$mutator( (string)$value );
							 	       	  		  		break;

							 	       	  		   case 'date':
							 	       	  		  		$this->getModel()->$mutator( date( 'c', strtotime( $value ) ) );
							 	       	  		  		break;

							 	       	  		   case 'datetime':
							 	       	  		  		$this->getModel()->$mutator( date( 'c', strtotime( $value ) ) );
							 	       	  		  		break;

							 	       	  		   default:
							 	       	  		   		Logger::getInstance()->debug( 'BaseModelActionController::setModelValues Warning about unsupported persistence data type \'' . $column->getType() .
							 	       	  		   									  '\'. Using (sanitized) value \'' . $value . '\'.' );
							 	       	  		   		$this->getModel()->$mutator( $value );
							 	       	  		   		break;
				     		   			   }
		     		   			  }
	  		 	  	      }
	  		     }
	     }		
}
?>