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
 * @abstract
 */
abstract class BaseModelController extends BaseController {

		 protected $page;
		 protected $count;
		 protected $pageCount;
		 protected $resultCount;
		 protected $resultList;
		 protected $sql;

         /**
	      * Sets the domain model the ORM is to manipulate.
	      * 
	      * @param Object $model The domain model the ORM is to manipulate
	      * @return void
	      */
	     protected function setModel($model) {

	    	    $this->model = $model;
	     }

		 /**
	      * Returns the table name for the model defined in the extension class
	      * 
	      * @return The database table name
	      */
	     protected function getTableName() {
 
	     	       if(!$this->getModelName())
	     	           throw new FrameworkException('Property \'model\' must be defined in ORM ' . $this->getModelName());

	     	       return ORM::getTableByModelName($this->getModelName())->getName();
	     }

         /**
	      * Returns the total number of records in the current result list.
	      * 
	      * @return The number of records in the current result list
	      */
	     protected function getResultCount() {

	     	       return $this->resultCount;
	     }

	     /**
	      * Returns a result set from the database. The result list is returned as
	      * an array of stdClass objects, each representing a row in the database.
	      * 
	      * @return An array of stdClass objects, eaching representing a row in the database
	      */
	     protected function getResultList() {

	     	       return $this->resultList;
	     }

	     /**
	      * Returns the maximum number of records a result set will return
	      * 
	      * @return Integer The max number of records per result set
	      */
	     protected function getMaxResults() {

	     		   return ORM::getMaxResults();
	     } 

	     /**
	      * Sets the SQL statement to use when calling executeQuery.
	      * 
	      * @param $sql A valid SQL statement
	      */
	     protected function createQuery($sql) {

	     	       $this->sql = $sql;
	     }

	     /**
	      * Executes the current sql query as set by createQuery.
	      * 
	      * @return PDOStatement The query result.
	      */
	     protected function executeQuery() {

	               Log::debug('BaseModelController::executeQuery ' . $this->sql);
	     	       return ORM::query($this->sql);
	     }

	     /**
	      * Executes an SQL count query for total number of records in the database. 
	      * Initialize 'pageCount' and 'count' properties. 
	      * 
	      * @return void
	      */
	     protected function executeCountQuery() {

	     	       $this->count = ORM::count($this->getModel());
  			 	   $this->pageCount = ceil($this->count / ORM::getMaxResults());
	     }

	     /**
	      * Returns the current SQL query
	      * 
	      * @return The current SQL query
	      */
	     protected function getQuery() {

	     		   return $this->sql;
	     }

	     /**
		  * Sets the pagination page number and performs an SQL query to populate the 'resultList'
		  * and 'resultCount' properties with their appropriate values for the specified page.
		  * 
		  * @param $pageNumber The page number
		  * @return void
	      */
	     protected function setPage($pageNumber) {

	     	    if(!is_numeric($pageNumber) || !$pageNumber)
	     	        $pageNumber = 1;

	     	    $this->page = $pageNumber;
				$this->executeCountQuery();

				if($this->page > $this->pageCount)
				    $pageNumber = $this->pageCount;

				$offset = ($this->getPage() - 1) * ORM::getMaxResults();
	     	    if($offset < 0) $offset = 0;

  	 	     	ORM::setOffset($offset);
				$result = ORM::find($this->getModel());

				if(!$result) return false;

				$this->resultList = $result;
				$this->resultCount = count($result);

				ORM::setGroupBy(null);
	  		 	ORM::setOrderBy(null, 'ASC');
	  		 	ORM::setRestrictions(array());
	  		 	ORM::setRestrictionsLogicOperator('AND');
	  		 	ORM::setComparisonLogicOperator('=');

				Log::debug('BaseModelController::setPage ' . $this->page);
	     }

	     /**
	      * Returns the total number of records in the database table
	      * 
	      * @return Total number of records
	      */
	     protected function getCount() {

	     	       return $this->count;
	     }

	     /**
	      * Returns the current page number
	      * 
	      * @return The current page number
	      */
	     protected function getPage() {

	     		   return ($this->page > 0) ? $this->page : 0;
	     }

	     /**
	      * Returns the total number of pages. This is calculated by dividing the total
	      * number of records by 'maxResults'.
	      * 
	      * @return The total number of pages
	      */
	     protected function getPageCount() {

	     	       return ($this->pageCount > 0) ? $this->pageCount : 0;
	     }

	     /**
	      * Returns boolean result based on whether or not a 'next page' result is available during
	      * a pagination request.
	      * 
	      * @return True if there is a next page, false if this is the last page
	      */
	     protected function nextExists() {

	     	       return $this->page < $this->pageCount;
	     }

	     /**
	      * Returns boolean result based on whether or not a 'previous page' result is available during
	      * a pagination request.
	      * 
	      * @return True if there is a previous page, false if this is the first page
	      */
	     protected function previousExists() {

	     		   return $this->page != 0 && $this->page != 1;
	     }

	     /**
	      * Sets the result list up with a 'next page' of a pagination request
	      * 
	      * @return void
	      */
	     protected function getNextResultList() {

	     	       $this->setPage($this->page + 1);
	     }

	     /**
	      * Sets the result list up with a 'previous page' of a pagination request
	      * 
	      * @return void
	      */
	     protected function getPreviousResultList() {

	     	       $this->setPage($this->page - 1);
	     }

	     /**
	      * Sets WHERE clause restrictions
	      * 
	      * @param array $restrictions An associative array containing WHERE clause restrictions. (For example: array('id' => 21))
	      * @return void
	      */
	     protected function setRestrictions(array $restrictions) {

	     		   ORM::setRestrictions($restrictions);
	     }

	     /**
	      * Sets the restriction operator (and|or) used in SQL WHERE clause.
	      * 
	      * @param String $operator The logical operator to be used in SQL WHERE clause. Default is 'AND'. (AND|OR)
	      * @return void
	      */
	     protected function setRestrictionsLogicOperator($operator) {

				   ORM::setRestrictionsLogicOperator($operator);
	     }
	     
		 /**
		  * Sets the comparison operator (<|>|=|LIKE) used in SQL WHERE clause.
		  * 
		  * @param $operator The logical comparison operator used is SQL where clauses. Default is '='.
		  * @return void
		  */
	     protected function setComparisonLogicOperator($operator) {

	     	       ORM::setComparisonLogicOperator($operator);
	     }

	     /**
	      * Sets the SQL 'group by' clause.
	      * 
	      * @param String $column The column name to group the result set by
	      * @return void
	      */
	     protected function setGroupBy($column) {

	     		   ORM::setGroupBy($column);
	     }

	     /**
	      * Sets the SQL 'order by' clause.
	      * 
	      * @param String $column The column name to order the result set by
	      * $param String $direction The direction to sort the result set (ASC|DESC).
	      * @return void
	      */
	     protected function setOrderBy($column, $direction) {

	     		   ORM::setOrderBy($column, $direction);
	     }

	     /**
	      * Returns an associative array containing the current 'orderBy' clause. The results
	      * are returned with the name of the column as the index and the direction as the value.
	      * 
	      * @return Array An associative array containing the name of the column to sort as the key/index
	      * 		and the direction of the sort order (ASC|DESC) as the value. 
	      */
	     protected function getOrderBy() {

	     		   return ORM::getOrderBy();
	     }

		 /**
	      * Returns the model's class name.
	      * 
	      * @return The class name of the model or null if a model has not been defined
	      * 		by the extension class.
	      */
	     protected function getModelName() {

	     		   try {
	     		   		 $class = new ReflectionClass($this->getModel());
	     		   		 return $class->getName();
	     		   }
	     		   catch(ReflectionException $re) { }
	     }

	     /**
	      * Search for an ActiveRecord for the model defined in the extension class.
	      * 
	      * @return Model $model Optional model instance to search on according to ActiveRecord state.
	      */
	     protected function find($model = null) {

	     		   $m = ($model == null) ? $this->getModel() : $model;
	     		   $this->resultList = ORM::find($m);
				   $this->resultCount = count($this->resultList);

	     		   return $this->resultList;
	     }

	     /**
	      * Persists a new model ActiveRecord defined in the extension class.
	      * 
	      * @return void
	      */
	     protected function persist() {

	     		   $this->getModel()->persist();
	     }

	     /**
	      * Merges the model defined in the extension class. The model must have
	      * its primary key properties defined for this operation to succeed.
	      * 
	      * @return void
	      */
	     protected function merge() {

	     		   $this->getModel()->merge();
	     }

	     /**
	      * Deletes the model ActiveRecord defined in the extension class. The model
	      * must have its primary key properties defined for this operation to succeed.
	      * 
	      * @return void
	      */
	     protected function delete() {

	     		   $this->getModel()->delete();
	     }

		 /**
	      * Clears the current model state.
	      * 
	      * @return void
	      */
	     protected function clear() {

	  	           $table = ORM::getTableByModel($this->getModel());
	  	           $columns = $table->getColumns();

  			       for($i=0; $i<count($columns); $i++) {

	  	     	 	    $mutator = $this->toMutator($columns[$i]->getModelPropertyName());
	  	     	 	    $this->getModel()->$mutator(null);
	  	           }

	  	           Log::debug('BaseModelController::clear');
	     }

	     /**
	      * Creates an accessor method from the $property parameter. The $property
	      * will be returned with the prefix 'get' and the first letter of the property
	      * uppercased.
	      * 
	      * @param $property The name of the property to convert to an accessor method name
	      * @return The accessor string
	      */
	     protected function toAccessor($property) {

	     		   return 'get' . ucfirst($property);
	     }

	     /**
	      * Creates a mutator method from the $property parameter. The $property
	      * will be returned with the prefix 'set' and the first letter of the property
	      * uppercased.
	      * 
	      * @param $property The name of the property to convert to a mutator method name
	      * @return The mutator string
	      */
	     protected function toMutator($property) {

	     		   return 'set' . ucfirst($property);
	     }

	     abstract protected function getModel();
}
?>