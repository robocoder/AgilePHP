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
 * @package com.makeabyte.agilephp.persistence.dialect
 */

/**
 * Defines contract used by AgilePHP ORM framework to implement vendor
 * specific SQL dialect/syntax.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence.dialect
 * @version 0.2a
 */
interface SQLDialect {

		  /**
	       * Checks the two defined model parameters to see if they are equal. The class name,
	       * property name, type and value.
	       *  
	       * @param Object $modelA The first domain model object
	       * @param Object $modelB The second domain model object
	       * @return True if the comparison was successful, false if they differ.
	       */
	      public function compare( $modelA, $modelB );

	      /**
		   * Adds an SQL distinct clause to 'find' operation.
		   * 
		   * @param $columnName The column name to get the distinct values for
		   * @return void
		   */
	  	  public function setDistinct( $columnName );

	  	  /**
		   * Returns the 'distinct' column to use in an SQL SELECT statement
		   * if one has been defined.
		   * 
		   * @return The DISTINCT column name or null if a column name has not been defined. 
		   */
	  	  public function getDistinct();

	      /**
		   * Sets the maximum number of records to return in a result list. Defaults to 25.
		   * 
		   * @param $count Maximum number of records to return
		   * @return void
	       */
	      public function setMaxResults( $maxResults = 25 );

	      /**
	       * Returns the maximum number of results to retrieve. This translates
	       * to an SQL LIMIT clause during SELECT operations.
	       * 
	       * @return The maximum number of results to retrieve during SELECT operations.
	       */
	      public function getMaxResults();
	     
	      /**
	  	   * Begins a transaction
	  	   * 
	  	   * @return void
	  	   * @throws AgilePHP_PersistenceException
	  	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	  	   * @see http://usphp.com/manual/en/function.PDO-beginTransaction.php
	  	   */
	  	  public function beginTransaction();

	  	  /**
	  	   * Commits an already started transaction.
	  	   * 
	  	   * @return void
	  	   * @throws AgilePHP_PersistenceException
	  	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	  	   * @see http://usphp.com/manual/en/function.PDO-commit.php
	  	   */
	  	  public function commit();

	  	  /**
	  	   * Rolls back a transaction.
	  	   * 
	  	   * @param $message Error/reason why the transaction was rolled back
	  	   * @param $code An error/reason code
	  	   * @return void
	  	   * @throws AgilePHP_PersistenceException
	  	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	  	   * @see http://usphp.com/manual/en/function.PDO-rollBack.php
	  	   */
	  	  public function rollBack( $message = null, $code = 0 );
	  	 
	  	  /**
		   * Prepares an SQL prepared statement
		   * 
		   * @param $statement The SQL statement to prepare
		   * @return False if the statement could not execute successfully
		   * @see http://usphp.com/manual/en/function.PDO-prepare.php
	  	   */
	  	  public function prepare( $statement );
	  	 
	  	  /**
	  	   * Executes a prepared statement (with parameters)
	  	   * 
	  	   * @param $inputParameters An array of input parameters
	  	   * @return True if successful, false on fail
	  	   * @see http://usphp.com/manual/en/function.PDOStatement-execute.php
	  	   */
	  	  public function execute( array $inputParameters = array() );
	  	 
		  /**
	  	   * Executes an SQL statement and returns the number of rows affected by the query.
	  	   * 
	  	   * @param $statement The SQL statement to execute.
	  	   * @return The number of rows affected by the query.
	  	   * @see http://usphp.com/manual/en/function.PDO-exec.php
	  	   */
	  	  public function exec( $statement );

	  	  /**
	  	   * Quotes a string so its theoretically safe to pass into a statement
	  	   * without the worry of SQL injection.
	  	   * 
	       * @param $data The data to quote
	  	   * @return The quoted data
	  	   * @see http://www.php.net/manual/en/pdo.quote.php
	  	   */
	  	  public function quote( $data );

		  /**
	   	   * Executes a raw SQL query using PDO::query
	   	   * 
	   	   * @param $sql The SQL statement to execute
	   	   * @return PDO::PDOStatement as returned by PDO::query
	   	   */
	  	  public function query( $sql );

	      /**
	       * Sets WHERE clause restrictions
	       * 
	       * @param $restrictions An associative array containing WHERE clause restrictions. (For example: array( 'id' => 21 ) )
	       * @return void
	       */
	      public function setRestrictions( array $restrictions );

	      /**
	       * Sets the restriction operator (and|or) used in SQL WHERE clause.
	       * 
	       * @param $operator The logical operator 'and'/'or' to be used in SQL WHERE clause. Default is 'AND'.
	       * @return void
	       */
	      public function setRestrictionsLogicOperator( $operator );

	      /**
	       * Sets the SQL 'group by' clause.
	       * 
	       * @param $column The column name to group the result set by
	       * @return void
	       */
	      public function setGroupBy( $column );

	      /**
	       * Returns SQL GROUP BY clause.
	       * 
	       * @return String GROUP BY value
	       */
	      public function getGroupBy();

	      /**
	       * Sets the SQL 'order by' clause.
	       * 
	       * @param $column The column name to order the result set by
	       * $param $direction The direction to sort the result set (ASC|DESC).
	       * @return void
	       */
	      public function setOrderBy( $column, $direction );

	      /**
	       * Returns an associative array containing the current 'orderBy' clause. The results
	       * are returned with the name of the column as the index and the direction as the value.
	       * 
	       * @return An associative array containing the name of the column to sort as the key/index
	       * 		and the direction of the sort order (ASC|DESC) as the value. 
	       */
	      public function getOrderBy();

	  	  /**
	       * Returns the 'Table' object which is mapped to the specified $model.
	       * 
	       * @param $model The domain model object to retrieve the table element for. Defaults to the model
	       * 			   currently being managed by the 'PersistenceManager'.
	       * @return The 'Table' object responsible for the model's persistence or null if a table
	       * 		 could not be located for the specified $model.
	       */
	      public function getTableByModel( $model = null );

	      /**
	       * Returns a 'Table' object representing the table configured in persistence.xml as
	       * the AgilePHP 'Identity' table.
	       * 
	       * @return The 'Table' object which represents the AgilePHP 'Identity' table, or null
	       * 		 if an 'Identity' table has not been configured.
	       */
	      public function getTableByModelName( $modelName );

	  	  /**
	  	   * Returns a 'Table' object by its name as configured in persistence.xml
	  	   * 
	  	   * @param $tableName The value of the table's 'name' attribute
	  	   * @return The 'Table' object or null if the table was not found
	  	   */
	  	  public function getTableByName( $tableName );

	  	  /**
	   	   * Returns a SimpleXMLElement representing the table configured in persistence.xml as
	   	   * an AgilePHP 'Identity' table.
		   * 
	       * @return The SimpleXMLElement instance containing the 'Identity' table, or null
	       * 		   if an 'Identity' table has not been configured.
	       */
	      public function getIdentityTable();

	      /**
	       * Returns the domain object model responsible for 'Identity' persistence.
	       * 
	       * @return The domain object model responsible for 'Identity' persistence.
	       */
	      public function getIdentityModel();

	      /**
	       * Returns a SimpleXMLElement representing the table configured in persistence.xml as
	       * an AgilePHP 'SessionScope' session table.
	       * 
	       * @return The SimpleXMLElement instance containing the 'SessionScope' session table
	       * 		 or null if a session table has not been configured.
	       */
	      public function getSessionTable();

	  	  /**
	       * Returns the domain model object responsible for 'SessionScope' sessions.
	       * 
	       * @return void
	       */
	  	  public function getSessionModel();

		  /**
		   * Returns the column 'name' attribute value configured in persistence.xml for the specified
		   * column 'property' attribute.
		   * 
		   * @param $table The 'Table' object to search
		   * @param $property The property attributes value
		   * @return The column name or null if the $property could not be found in the table
		   */
		  public function getColumnNameForProperty( $table, $property );

		  /**
		   * Returns the 'property' name configured in persistence.xml for the specified
		   * column 'name' attribute.
		   * 
		   * @param $table The 'Table' object to search
		   * @param $columnName The column name to search
		   * @return The column name or null if the $property could not be found in the table
		   */
		  public function getPropertyNameForColumn( $table, $columnName );

		  /**
	       * Creates an accessor method from the $property parameter. The $property
	       * will be returned with the prefix 'get' and the first letter of the property
	       * uppercased.
	       * 
	       * @param $property The name of the property to convert to an accessor method name
	       * @return The accessor string
	       */
	      public function toAccessor( $property );

	      /**
	       * Creates a mutator method from the $property parameter. The $property
	       * will be returned with the prefix 'set' and the first letter of the property
	       * uppercased.
	       * 
	       * @param $property The name of the property to convert to a mutator method name
	       * @return The mutator string
	       */
	      public function toMutator( $property );

	      /**
	       * Returns the specified number as a 'bigint' 64-bit number.
	       * 
	       * @return void
	       */
	      public function toBigInt( $number );
	     
		  /**
		   * Creates the active database in use by the ORM framework
		   * (defined in persistence.xml).
		   * 
		   * @return void
		   */
		  public function create();

		  /**
	   	   * Drops the active database in use by the ORM framework
	   	   * (defined in persistence.xml).
	   	   * 
	   	   * @return void
	   	   * @throws AgilePHP_PersistenceException
	   	   */
	  	  public function drop();

	  	 /**
	   	  * Persists a domain model object
	   	  * 
		  * @param $model The model object to persist
		  * @return PDOStatement
		  * @throws AgilePHP_PersistenceException
		  */
  	     public function persist( $model );

	  	 /**
	   	  * Merges/updates a persisted domain model object
	   	  * 
		  * @param $model The model object to merge/update
		  * @return PDOStatement
		  * @throws AgilePHP_PersistenceException
		  */
	  	 public function merge( $model );

		 /**
		  * Deletes a persisted domain model object
	   	  * 
		  * @param $model The domain model object to delete
		  * @return PDOStatement
		  * @throws AgilePHP_PersistenceException
		  */
		 public function delete( $model );

	  	 /**
	   	  * Truncates the table for the specified domain model object
	   	  * 
		  * @param $model A domain model object
		  * @return PDOStatement
		  * @throws AgilePHP_PersistenceException
		  */
		 public function truncate( $model );

	  	 /**
	   	  * Attempts to locate the specified model by primary key value.
	      * 
	   	  * @param Object $model A domain model object with its primary key field set
	      * @return Returns the same model which was passed (populated with the
	      * 		 database values) or null if a matching record could not be found.
	      * @throws AgilePHP_PersistenceException
	      */
	  	 public function find( $model );

	  	 /**
		  * Reverse engineers the active database and returns a Database object that represents
		  * the physical database.
		  * 
		  * @return Database A database object that represents the physical database
		  */
	  	 public function reverseEngineer();

	     /**
	      * Returns an SQL formatted string containing a WHERE clause built from setRestrictions and setRestrictionsLogicOperator.
	      * 
	      * @return The formatted SQL string
	      */
	     public function createRestrictSQL();
}
?>