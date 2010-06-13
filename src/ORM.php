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
 * @package com.makeabyte.agilephp
 */

require_once 'orm/ORMFactory.php';
require_once 'orm/Database.php';
require_once 'orm/Table.php';
require_once 'orm/Column.php';
require_once 'orm/ForeignKey.php';
require_once 'orm/dialect/BaseDialect.php';

/**
 * ORM Facade
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
final class ORM {

	  /**
	   * Connects the ORM to the specified Database
	   * 
	   * @param Database $db The database to establish a connection with
	   * @return void
	   */
	  public static function connect( Database $db ) {

	  		 return ORMFactory::connect( $db );
	  }
	
	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#isConnected()
	   * @static
	   */
      public static function isConnected() {

     		 return ORMFactory::getDialect()->isConnected();
      }

	  /**
	   * Returns the SQL dialect instance responsible for invoking SQL commands.
	   * 
	   * @return void
	   * @static
	   */
	  public static function getDialect() {

	  	     return ORMFactory::getDialect();
	  }

	  /**
	   * Returns the database object currently in use by the ORM framework.
	   * 
	   * @return void
	   * @static
	   */
	  public static function getDatabase() {

		 	 return ORMFactory::getDialect()->getDatabase();
	  }

	  /**
	   * Adds an SQL distinct clause to 'find' operation.
	   * 
	   * @param $columnName The column name to get the distinct values for
	   * @return void
	   * @static
	   */
	  public static function setDistinct( $columnName ) {

	   		 ORMFactory::getDialect()->setDistinct( $columnName );
	  }

	  /**
	   * Returns the 'distinct' column to use in an SQL SELECT statement
	   * if one has been defined.
	   * 
	   * @return The DISTINCT column name or null if a column name has not been defined.
	   * @static 
	   */
	  public static function isDistinct() {

	  	     return ORMFactory::getDialect()->isDistinct();
	  }

	  /**
	   * Sets the maximum number of records to return in a result list
	   * 
	   * @param $count Maximum number of records to return
	   * @return void
	   * @static
	   */
	  public static function setMaxResults( $maxResults = 25 ) {

	     	 ORMFactory::getDialect()->setMaxResults( $maxResults );
	  }

	  /**
	   * Returns the maximum number of results to retrieve. This translates
	   * to an SQL LIMIT clause during SELECT operations.
	   * 
	   * @return The maximum number of results to retrieve during SELECT operations.
	   * @static
	   */
	  public static function getMaxResults() {
	    	
	   		 return ORMFactory::getDialect()->getMaxResults();
	  }
	     
	  /**
	   * Begins a transaction
	   * 
	   * @return void
	   * @throws ORMException
	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	   * @see http://usphp.com/manual/en/function.PDO-beginTransaction.php
	   * @static
	   */
	  public static function beginTransaction() {

	  	     ORMFactory::getDialect()->beginTransaction();
	  }

	  /**
	   * Commits an already started transaction.
	   * 
	   * @return void
	   * @throws ORMException
	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	   * @see http://usphp.com/manual/en/function.PDO-commit.php
	   * @static
	   */
	  public static function commit() {
	   	
	  		 ORMFactory::getDialect()->commit();
	  }

	  /**
	   * Rolls back a transaction.
	   * 
	   * @param $message Error/reason why the transaction was rolled back
	   * @param $code An error/reason code
	   * @return void
	   * @throws ORMException
	   * @see http://us2.php.net/manual/en/pdo.transactions.php
	   * @see http://usphp.com/manual/en/function.PDO-rollBack.php
	   * @static
	   */
	  public static function rollBack( $message = null, $code = 0 ) {

	  	     ORMFactory::getDialect()->rollBack( $message, $code );
	  }
	  	 
	  /**
	   * Prepares an SQL prepared statement
	   * 
	   * @param $statement The SQL statement to prepare
	   * @return False if the statement could not execute successfully
	   * @see http://usphp.com/manual/en/function.PDO-prepare.php
	   * @static
	   */
	  public static function prepare( $statement ) {

	  	     return ORMFactory::getDialect()->prepare( $statement );
	  }
	  	 
	  /**
	   * Executes a prepared statement (with parameters)
	   * 
	   * @param $inputParameters An array of input parameters
	   * @return True if successful, false on fail
	   * @see http://usphp.com/manual/en/function.PDOStatement-execute.php
	   * @static
	   */
	  public static function execute( array $inputParameters = array() ) {
	  	 	
	  	     return ORMFactory::getDialect()->execute( $inputParameters );
	  }
	  	 
	  /**
	   * Executes an SQL statement and returns the number of rows affected by the query.
	   * 
	   * @param $statement The SQL statement to execute.
	   * @return The number of rows affected by the query.
	   * @see http://usphp.com/manual/en/function.PDO-exec.php
	   * @static
	   */
	  public static function exec( $statement ) {

	  		 return ORMFactory::getDialect()->exec( $statement );
	  }

	  /**
	   * Quotes a string so its theoretically safe to pass into a statement
	   * without the worry of SQL injection.
	   * 
	   * @param $data The data to quote
	   * @return The quoted data
	   * @see http://www.php.net/manual/en/pdo.quote.php
	   * @static
	   */
	  public static function quote( $data ) {

	  		 ORMFactory::getDialect()->quote( $data );
	  }

	  /**
	   * Executes a raw SQL query using PDO::query
	   * 
	   * @param $sql The SQL statement to execute
	   * @return PDO::PDOStatement as returned by PDO::query
	   * @static
	   */
	  public static function query( $sql ) {

	  	     return ORMFactory::getDialect()->query( $sql );
	  }

      /**
	   * Sets WHERE clause restrictions
	   * 
	   * @param $restrictions An associative array containing WHERE clause restrictions. (For example: array( 'id' => 21 ) )
	   * @return void
	   * @static
	   */
	  public static function setRestrictions( array $restrictions ) {

	         ORMFactory::getDialect()->setRestrictions( $restrictions );
	  }

	  /**
	   * Sets the restriction operator (and|or) used in SQL WHERE clause.
	   * 
	   * @param $operator The logical operator 'and'/'or' to be used in SQL WHERE clause. Default is 'AND'.
	   * @return void
	   * @static
	   */
	  public static function setRestrictionsLogicOperator( $operator ) {

	     	 ORMFactory::getDialect()->setRestrictionsLogicOperator( $operator );
	  }

	  /**
	   * Sets the comparison operator (<|>|=|LIKE) used in SQL WHERE clause.
	   * 
	   * @param $operator The logical comparison operator used is SQL where clauses (<|>|=|LIKE). Default is '='.
	   * @return void
	   * @static
	   */
	  public static function setComparisonLogicOperator( $operator ) {

	     	 ORMFactory::getDialect()->setComparisonLogicOperator( $operator );
	  }

	  /**
	   * Sets the SQL 'group by' clause.
	   * 
	   * @param $column The column name to group the result set by
	   * @return void
	   * @static
	   */
	  public static function setGroupBy( $column ) {

	         ORMFactory::getDialect()->setGroupBy( $column );
	  }

	  /**
	   * Returns SQL GROUP BY clause.
	   * 
	   * @return String GROUP BY value
	   * @static
	   */
	  public static function getGroupBy() {

	         return ORMFactory::getDialect()->getGroupBy();
	  }

	  /**
	   * Sets the SQL 'order by' clause.
	   * 
	   * @param $column The column name to order the result set by
	   * $param $direction The direction to sort the result set (ASC|DESC).
	   * @return void
	   * @static
	   */
	  public static function setOrderBy( $column, $direction ) {

	     	 ORMFactory::getDialect()->setOrderBy( $column, $direction );
	  }

	  /**
	   * Returns an associative array containing the current 'orderBy' clause. The results
	   * are returned with the name of the column as the index and the direction as the value.
	   * 
	   * @return An associative array containing the name of the column to sort as the key/index
	   * 		and the direction of the sort order (ASC|DESC) as the value.
	   * @static 
	   */
	  public static function getOrderBy() {

	     	 return ORMFactory::getDialect()->getOrderBy();
	  }

	  /**
	   * Sets the offset used in a SQL LIMIT clause.
	   * 
	   * @param Integer $offset The limit offset.
	   * @return void
	   */
	  public function setOffset( $offset ) {

	 		 ORMFactory::getDialect()->setOffset( $offset );
	  }

	  /**
	   * Returns the SQL LIMIT offset value.
	   * 
	   * @return Integer The LIMIT offset.
	   */
	  public function getOffset() {

			 return ORMFactory::getDialect()->setOffset( $offset );
	  }

	  /**
	   * Returns the 'Table' object which is mapped to the specified $model.
	   * 
	   * @param $model The domain model object to retrieve the table element for. Defaults to the model
	   * 			   currently being managed by the 'ORM'.
	   * @return The 'Table' object responsible for the model's ORM or null if a table
	   * 		 could not be located for the specified $model.
	   * @static
	   */
	  public static function getTableByModel( $model = null ) {

		     return ORMFactory::getDialect()->getTableByModel( $model );
	  }

	  /**
	   * Returns a 'Table' object representing the table configured in orm.xml as
	   * the AgilePHP 'Identity' table.
	   * 
	   * @return The 'Table' object which represents the AgilePHP 'Identity' table, or null
	   * 		 if an 'Identity' table has not been configured.
	   * @static
	   */
	  public static function getTableByModelName( $modelName ) {

			 return ORMFactory::getDialect()->getTableByModelName( $modelName );
	  }

	  /**
	   * Returns a 'Table' object by its name as configured in orm.xml
	   * 
	   * @param $tableName The value of the table's 'name' attribute
	   * @return The 'Table' object or null if the table was not found
	   * @static
	   */
	  public static function getTableByName( $tableName ) {

	  		 return ORMFactory::getDialect()->getTableByName( $tableName );
	  }

	  /**
	   * Returns the column 'name' attribute value configured in orm.xml for the specified
	   * column 'property' attribute.
	   * 
	   * @param $table The 'Table' object to search
	   * @param $property The property attributes value
	   * @return The column name or null if the $property could not be found in the table
	   * @static
	   */
	  public static function getColumnNameForProperty( $table, $property ) {

			 return ORMFactory::getDialect()->getColumnNameForProperty( $table, $property );
      }

	  /**
	   * Returns the 'property' name configured in orm.xml for the specified
	   * column 'name' attribute.
	   * 
	   * @param $table The 'Table' object to search
	   * @param $columnName The column name to search
	   * @return The column name or null if the $property could not be found in the table
	   * @static
	   */
	  public static function getPropertyNameForColumn( $table, $columnName ) {

			 return ORMFactory::getDialect()->getPropertyNameForColumn( $table, $columnName );
      }
    
	  /**
	   * Creates the database context as defined in orm.xml.
	   * 
 	   * @return void
 	   * @static
	   */
	  public static function create() {

		     ORMFactory::getDialect()->create();
	  }
	  
	  /**
	   * (non-PHPdoc)
 	   * 
	   * @see src/orm/dialect/SQLDialect#createTable(Table $table)
	   * @static
 	   */
	  public static function createTable( Table $table ) {

		     ORMFactory::getDialect()->createTable( $table );
	  }

	  /**
	   * Drops the database specified in orm.xml
	   * 
	   * @return void
	   * @throws ORMException
	   * @static
	   */
	  public static function drop() {

	  	     ORMFactory::getDialect()->drop();
	  }
	  	  
	  /**
	   * (non-PHPdoc)
	   * 
	   * @see src/orm/dialect/SQLDialect#dropTable(Table $table)
	   * @static
	   */
	  public static function dropTable( Table $table ) {

		  	 ORMFactory::getDialect()->dropTable( $table );
	  }

  	  /**
	   * Persists a domain model object
	   * 
	   * @param $model The model object to persist
	   * @return PDOStatement
	   * @throws ORMException
	   * @static
	   */
	  public static function persist( $model ) {

	  	     return ORMFactory::getDialect()->persist( $model );
	  }

	  /**
	   * Merges/updates a persisted domain model object
	   * 
	   * @param $model The model object to merge/update
	   * @return PDOStatement
	   * @throws ORMException
	   * @static
	   */
  	  public static function merge( $model ) {

  	  	     return ORMFactory::getDialect()->merge( $model );
	  }

	  /**
	   * Deletes a persisted domain model object
   	   * 
	   * @param $model The domain model object to delete
	   * @return PDOStatement
	   * @throws ORMException
	   * @static
	   */
	  public static function delete( $model ) {

  	     	 return ORMFactory::getDialect()->delete( $model );
  	  }

  	  /**
	   * Truncates the table for the specified domain model object
   	   * 
	   * @param $model A domain model object
	   * @return PDOStatement
	   * @throws ORMException
	   * @static
	   */
	 public static function truncate( $model ) {

  		    return ORMFactory::getDialect()->truncate( $model );
  	 }

  	 /**
   	  * Attempts to locate the specified model by primary key value.
      * 
   	  * @param Object $model A domain model object with its primary key field set
      * @return Returns the same model which was passed (populated with the
      * 		 database values) or null if a matching record could not be found.
      * @throws ORMException
      * @static
      */
  	 public static function find( $model ) {

  		    return ORMFactory::getDialect()->find( $model );
  	 }

	 /**
	  * Returns the total number of records in the database for the specified model.
	  * 
	  * @param Object $model The model to get the count for.
	  * @return Integer The total number of records in the table.
	  */
  	 public static function count( $model ) {

	  		return ORMFactory::getDialect()->count( $model );
	 }

  	 /**
  	  * Returns AgilePHP ORM database structure for the current database.
  	  * 
  	  * @return Array Multi-dimensional array representing the current database structure.
  	  * @static
  	  */
  	 public static function reverseEngineer() {

  	 		return ORMFactory::getDialect()->reverseEngineer();
  	 }

     /**
      * Returns an SQL formatted string containing a WHERE clause built from setRestrictions and setRestrictionsLogicOperator.
      * 
      * @return The formatted SQL string
      * @static
      */
     public static function createRestrictSQL() {

     		return ORMFactory::getDialect()->createRestrictSQL();
     }
}
?>