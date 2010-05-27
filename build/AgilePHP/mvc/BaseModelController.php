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
 * AgilePHP :: MVC BaseModelController
 * Provides base implementation for model controllers.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.mvc
 * @version 0.2a
 * @abstract
 */
abstract class BaseModelController extends BaseController {

	     private $persistence = null;			// PersistenceManager

	     /**
	      * Initalizes parent and executes a default SELECT query for the first 25 records.
	      */
	     protected function __construct() {

	     	       parent::__construct();

	     	       if( !$this->getModel() )
	     	       		return;

	     	       $this->persistence = new PersistenceManager();
	     	       $this->persistence->setModel( $this->getModel() );
	     }

		 /**
	      * Returns an instance of PersistenceManager
	      * 
	      * @return PersistenceManager
	      * @throws AgilePHP_Exception if the instance of PersistenceManager is null
	      */
	     protected function getPersistenceManager() {

	     		   return $this->persistence;
	     }

	     /**
	      * Returns the total number of records in the current result list.
	      * 
	      * @return The number of records in the current result list
	      */
	     protected function getResultCount() {

	     	       $count = $this->getPersistenceManager()->getResultCount();
	     	       return ($count > 0) ? $count : 0;
	     }

	     /**
	      * Returns a result set from the database. The result list is returned as
	      * an array of stdClass objects, each representing a row in the database.
	      * 
	      * @return An array of stdClass objects, eaching representing a row in the database
	      */
	     protected function getResultList() {

	     	  	   return $this->getPersistenceManager()->getResultList();
	     }

	     /**
		  * Sets the maximum number of records to return in a result list
		  * 
		  * @param Integer $count Maximum number of records to return
		  * @return void
	      */
	     protected function setMaxResults( $count ) {

	     	       $this->getPersistenceManager()->setMaxResults( $count );
	     }

	     /**
	      * Returns the maximum number of results to retrieve. This translates
	      * to an SQL LIMIT clause during SELECT operations.
	      * 
	      * @return The maximum number of results to retrieve during SELECT operations.
	      */
	     protected function getMaxResults() {
	     	
	     		   return $this->getPersistenceManager()->getMaxResults();
	     }

	     /**
	  	  * Sets the SQL statement to use when calling executeQuery.
	  	  * 
	  	  * @param String $sql A valid SQL statement
	      */
	     protected function createQuery( $sql ) {

	     		   $this->getPersistenceManager()->createQuery( $sql );
	     }

	     /**
	      * Returns the current SQL query
	      * 
	      * @return The current SQL query
	      */
	     protected function getQuery() {

	     		   return $this->getPersistenceManager()->getQuery();
	     }

	     /**
		  * Sets the pagination page number and performs an SQL query to populate the 'resultList'
		  * and 'resultCount' properties with their appropriate values for the specified page.
		  * 
		  * @param Integer $pageNumber The page number. Default is 1.
		  * @return void
	      */
	     protected function setPage( $pageNumber = 1 ) {

	     		   if( !$this->getPersistenceManager() )
	     		   	   throw new AgilePHP_Exception( 'PersistenceManager is null in setPage. A valid model is required in the extension class to perform this operation.' );

	     	       $this->getPersistenceManager()->setPage( $pageNumber );
	     }

	     /**
	      * Sets WHERE clause restrictions
	      * 
	      * @param array $restrictions An associative array containing WHERE clause restrictions. (For example: array( 'id' => 21 ) )
	      * @return void
	      */
	     protected function setRestrictions( array $restrictions ) {

	     		   $this->getPersistenceManager()->setRestrictions( $restrictions );
	     }

	     /**
	      * Sets the restriction operator (and|or) used in SQL WHERE clause.
	      * 
	      * @param String $operator The logical operator to be used in SQL WHERE clause. Default is 'AND'. (AND|OR)
	      * @return void
	      */
	     protected function setRestrictionsLogicOperator( $operator ) {

				   $this->getPersistenceManager()->setRestrictionsLogicOperator( $operator );
	     }
	     
		 /**
		  * Sets the comparison operator (<|>|=|LIKE) used in SQL WHERE clause.
		  * 
		  * @param $operator The logical comparison operator used is SQL where clauses. Default is '='.
		  * @return void
		  */
	     protected function setComparisonLogicOperator( $operator ) {

	     	       $this->getPersistenceManager()->setComparisonLogicOperator( $operator );
	     }

	     /**
	      * Sets the SQL 'group by' clause.
	      * 
	      * @param String $column The column name to group the result set by
	      * @return void
	      */
	     protected function setGroupBy( $column ) {

	     		   $this->getPersistenceManager()->setGroupBy( $column );
	     }

	     /**
	      * Sets the SQL 'order by' clause.
	      * 
	      * @param String $column The column name to order the result set by
	      * $param String $direction The direction to sort the result set (ASC|DESC).
	      * @return void
	      */
	     protected function setOrderBy( $column, $direction ) {

	     		   $this->getPersistenceManager()->setOrderBy( $column, $direction );
	     }

	     /**
	      * Returns an associative array containing the current 'orderBy' clause. The results
	      * are returned with the name of the column as the index and the direction as the value.
	      * 
	      * @return Array An associative array containing the name of the column to sort as the key/index
	      * 		and the direction of the sort order (ASC|DESC) as the value. 
	      */
	     protected function getOrderBy() {

	     		   return $this->getPersistenceManager()->getOrderBy();
	     }

	     /**
	      * Returns the total number of records in the database table
	      * 
	      * @return Integer Total number of records
	      */
	     protected function getCount() {

	     		   $count = $this->getPersistenceManager()->getCount();
	     		   return ($count > 0) ? $count : 0;
	     }
	     
	     /**
	      * Returns the current page number
	      * 
	      * @return Integer The current page number
	      */
	     protected function getPage() {

	     		   $page = $this->getPersistenceManager()->getPage();
	     		   return ($page > 0) ? $page : 0;
	     }

	     /**
	      * Returns the total number of pages. This is calculated by dividing the total
	      * number of records by 'maxResults'.
	      * 
	      * @return Integer The total number of pages
	      */
	     protected function getPageCount() {

	     		   $count = $this->getPersistenceManager()->getPageCount();
	     		   return ($count > 0) ? $count : 0;
	     }

	     /**
	      * Returns boolean result based on whether or not a 'next page' result is available during
	      * a pagination request.
	      * 
	      * @return bool True if there is a next page, false if this is the last page
	      */
	     protected function nextExists() {

	     	       return $this->getPersistenceManager()->nextExists();
	     }

	     /**
	      * Returns boolean result based on whether or not a 'previous page' result is available during
	      * a pagination request.
	      * 
	      * @return bool True if there is a previous page, false if this is the first page
	      */
	     protected function previousExists() {

	     		   return $this->getPersistenceManager()->previousExists();
	     }

	     /**
	      * Sets the result list up with a 'next page' of a pagination request
	      * 
	      * @return Causes a 'next page' to load when using the AgilePHP MVC framework
	      */
	     protected function getNextResultList() {

	     	       return $this->getPersistenceManager()->getNextResultList();
	     }

	     /**
	      * Sets the result list up with a 'previous page' of a pagination request
	      * 
	      * @return Causes a 'previous page' to load when using the AgilePHP MVC framework
	      */
	     protected function getPreviousResultList() {

	     	       return $this->getPersistenceManager()->getPreviousResultList();
	     }

	     /**
	      * Executes an SQL count query for total number of records in the database. 
	      * Initalizes 'pageCount' and 'count' properties. 
	      * 
	      * @return void
	      */
	     protected function executeCountQuery() {

	     	       $this->getPersistenceManager()->executeCountQuery();
	     }

	     /**
	      * Executes the current SQL statement as defined in 'sql'.
	      * 
	      * @return void
	      */
	     protected function executeQuery() {

	     	       $this->getPersistenceManager()->executeQuery();
	     }

	     /**
	      * Returns the table name for the model defined in the extension class
	      * 
	      * @return The database table name
	      */
	     protected function getTableName() {

	     	       return $this->getPersistenceManager()->getTableName();
	     }

		 /**
	      * Returns the model's class name.
	      * 
	      * @return The class name of the model or null if a model has not been defined
	      * 		by the extension class.
	      */
	     protected function getModelName() {

	     		   try {
	     		   		 $class = new ReflectionClass( $this->getModel() );
	     		   		 return $class->getName();
	     		   }
	     		   catch( ReflectionException $re ) {

	     		   		  return null;
	     		   }
	     }

	     /**
	      * Search for an ActiveRecord for the model defined in the extension class.
	      * 
	      * @return mixed A new instance of the model with all of its properties filled out
	      * 		according to the persisted ActiveRecord.
	      */
	     protected function find( $model = null ) {

	     		   $m = ($model == null) ? $this->getModel() : $model;
	     		   $this->resultList = $this->getPersistenceManager()->find( $m );
				   $this->resultCount = count( $this->resultList );

	     		   return $this->resultList;
	     }

	     /**
	      * Persists a new model ActiveRecord defined in the extension class.
	      * 
	      * @return void
	      */
	     protected function persist() {

	     		   $this->getPersistenceManager()->persist( $this->getModel() );
	     }

	     /**
	      * Merges the model defined in the extension class. The model must have
	      * its primary key properties defined for this operation to succeed.
	      * 
	      * @return void
	      */
	     protected function merge() {

	     		   $this->getPersistenceManager()->merge( $this->getModel() );
	     }

	     /**
	      * Deletes the model ActiveRecord defined in the extension class. The model
	      * must have its primary key properties defined for this operation to succeed.
	      * 
	      * @return void
	      */
	     protected function delete() {

	     		   $this->getPersistenceManager()->delete( $this->getModel() );
	     }

		 /**
	      * Clears the current model state.
	      * 
	      * @return void
	      */
	     protected function clear() {

	  	           $table = $this->getPersistenceManager()->getTableByModel( $this->getModel() );
	  	           $columns = $table->getColumns();

  			       for( $i=0; $i<count( $columns ); $i++ ) {

	  	     	 	    $mutator = $this->toMutator( $columns[$i]->getModelPropertyName() );
	  	     	 	    $this->getModel()->$mutator( null );
	  	           }

	  	           Logger::getInstance()->debug( 'BaseModelController::clear ' );
	     }

	     /**
	      * Creates an accessor method from the $property parameter. The $property
	      * will be returned with the prefix 'get' and the first letter of the property
	      * uppercased.
	      * 
	      * @param $property The name of the property to convert to an accessor method name
	      * @return The accessor string
	      */
	     protected function toAccessor( $property ) {

	     		   return $this->getPersistenceManager()->toAccessor( $property );
	     }

	     /**
	      * Creates a mutator method from the $property parameter. The $property
	      * will be returned with the prefix 'set' and the first letter of the property
	      * uppercased.
	      * 
	      * @param $property The name of the property to convert to a mutator method name
	      * @return The mutator string
	      */
	     protected function toMutator( $property ) {

	     		   return $this->getPersistenceManager()->toMutator( $property );
	     }

	     abstract protected function getModel();
}
?>