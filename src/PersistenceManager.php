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
 * @package com.makeabyte.agilephp
 */

/**
 * Includes base persistence dependencies
 */
require_once 'persistence/dialect/SQLDialect.php';
require_once 'persistence/BasePersistence.php';

/**
 * AgilePHP :: PersistenceManager
 * Facade for working with persisence operations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.1a
 */
class PersistenceManager {

	  private $dialect;					// Stores the dialect object used to execute vendor specific SQL queries
	  private $databases = array();

	  private $sql;							// The SQL statement to execute
	  private $distinct;						// Boolean flag for SELECT DISTINCT
	  private $resultCount;					// Total number of items in result list
	  private $resultList;					// The sql result list
	  private $maxResults = 25;       		// Maximum number of items to return
	  private $count; 	    				// Total number of records in the table
	  private $page;							// Holds the current page number
	  private $pageCount;					// Total number of pages
	  private $restrictions;					// WHERE clause restrictions
	  private $restrictionsLogic = 'AND';	// Logic operator to use in WHERE clause (and|or)
	  private $groupBy;						// The column to group the SQL query result set by
	  private $orderBy;						// Stores the column name to sort the result set by
	  private $orderDirection;				// The direction to sort the result set (Default is 'ASC')
	  private $model;						// Stores an ActiveRecord model

	  /**
	   * Initalizes the PersistenceManager object based on persistence.xml configuration. Each of the
	   * databases, tables, and columns are transformed into corresponding AgilePHP persistence objects.
	   * If a 'databaseId' is present, PersistenceManager is initalized with the specified database.
	   * 
	   * @param $databaseId The id of the database to initalize PersistenceManager with. Defaults to the first database in persistence.xml.
	   * @return void
	   */
	  public function __construct( $databaseId = null ) {

	   	     $persistence_xml = AgilePHP::getFramework()->getWebRoot() . '/persistence.xml';

	   	     if( !file_exists( $persistence_xml ) )
	   	     	 throw new AgilePHP_PersistenceException( 'PersistenceManager requires the presence of persistence.xml at \'' . $persistence_xml . '\'.' );

	  	     $xml = simplexml_load_file( $persistence_xml );

 	  	     $dom = new DOMDocument();
 			 $dom->Load( $persistence_xml );
			 if( !$dom->validate() )
			 	 throw new AgilePHP_PersistenceException( 'persistence.xml Document Object Model validation failed.' );

			 if( !$xml->database ) return;

			 foreach( $xml->database as $db )
			 	      array_push( $this->databases, new Database( $db ) );

			 if( $databaseId )
			 	 foreach( $this->databases as $db )
			 	 		  if( $db->getId() == $databaseId )
			 	 		  	  $this->connect( $db );

			 if( !$this->dialect )
			 	 $this->connect( $this->databases[0] );

			 if( !$this->getModel() ) return;

			 $this->createDefaultSQL();
     	     $stmt = $this->query( $this->sql );
  		 	 $stmt->setFetchMode( PDO::FETCH_OBJ );
  		 	 $this->resultList = $stmt->fetchAll();
  		 	 $this->resultCount = sizeof( $this->resultList );
  		 	 $this->executeCountQuery();
	  	 }

	  	 /**
	  	  * Establishes a connection to the specified database.
	  	  * 
	  	  * @param $db A Database object to establish the connection with
	  	  * @return void
	  	  */
	  	 public function connect( Database $db ) {

	  	 		switch( $db->getType() ) {

	  	 			 case 'sqlite':
	  	     		 	  require_once 'persistence/dialect/SQLiteDialect.php';
	  	     		 	  $this->dialect = new SQLiteDialect( $db );
	  	     		 	  break;

	  	     	     case 'mysql':
	  	     		 	  require_once 'persistence/dialect/MySQLDialect.php';
	  	     		 	  $this->dialect = new MySQLDialect( $db );
	  	     		 	  break;

	  	     		 	/*
	  	     		 	case 'pgsql':
	  	     		 		 $this->pdo = new PDO( "pgsql:host=$host;dbname=$name", $username, $password );
	  	     		 	  	 break;

	  	     		 	case 'firebird':
	  	     		    	 $this->pdo = new PDO( "firebird:dbname=$host:$name", $username, $password );
	  	     		      	 break;

	  	     		 	case 'informix':
	  	     		 		 $this->pdo = new PDO( "informix:DSN=$name", $username, $password );
	  	     		 	  	 break;

	  	     		 	case 'oracle':
	  	     		 		 $this->pdo = new PDO( "OCI:dbname=$name;charset=UTF-8", $username, $password );
	  	     		 	  	 break;

	  	     		 	case 'dblib':
	  	     		 		 $this->pdo = new PDO( "dblib:host=$host;dbname=$name", $username, $password );
	  	     		 	  	 break;

	  	     		 	case 'ibm':
	  	     		 		 $this->pdo = new PDO( "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$name;HOSTNAME=$host;PROTOCOL=TCPIP;", $username, $password );
	  	     		 	  	 break;
	  	     		 	*/

	  	     		 	default:
	  	     		 		 throw new AgilePHP_PersistenceException( "Invalid database type specified in persistence.xml for database with id '" . $db->getId() . "'." );
	  	     		 	  	 break;
	  	     	}
	  	 }
	  	 
	    /**
	     * Returns the domain model which the PersistenceManager is manipulating.
	     * 
	     * @return The domain model instance which the PersistenceManageris manipulating.
	     */
	    public function getModel() {

	    	   return $this->model;
	    }

	    /**
	     * Sets the domain model the PersistenceManager is to manipulate.
	     * 
	     * @param $model The domain model the PersistenceManager is to manipulate
	     * @return void
	     */
	    public function setModel( $model ) {

	    	   $this->model = $model;
	    }

	    /**
	     * Checks the two defined model parameters to see if they are equal. The class name,
	     * property name, type and value.
	     *  
	     * @param $modelA The first domain model object
	     * @param $modelB The second domain model object
	     * @return True if the comparison was successful, false if they differ.
	     */
	    public function compareModels( $modelA, $modelB ) {
	    	
	    	   return $this->dialect->compare( $modelA, $modelB );
	    }

		 /**
	      * Returns the model's class name.
	      * 
	      * @return The class name of the model or null if a model has not been defined
	      * 		by the extension class.
	      */
	     public function getModelName() {

	     		   if( $this->getModel() ) {

	     		   	   $class = new ReflectionClass( $this->getModel() );
	     		   	   return $class->getName();
	     		   }

	     		   return null;
	     }
		
		 /**
	      * Returns the table name for the model defined in the extension class
	      * 
	      * @return The database table name
	      */
	     public function getTableName() {

	     	     if( !$this->getModelName() )
	     	          throw new AgilePHP_Exception( 'Property \'model\' must be defined in PersistenceManager ' . $this->getModelName() );

	     	     return $this->getTableByModelName( $this->getModelName() )->getName();
	     }

	     /**
		  * Adds an SQL distinct clause to 'find' operation.
		  * 
		  * @param $columnName The column name to get the distinct values for
		  * @return void
		  */
	  	 public function setDistinct( $columnName ) {

	  	 		$this->dialect->setDistinct( $columnName );
	  	 }

	  	 /**
		  * Returns the 'distinct' column to use in an SQL SELECT statement
		  * if one has been defined.
		  * 
		  * @return The DISTINCT column name or null if a column name has not been defined. 
		  */
	  	 public function getDistinct() {

	  	 		return $this->dialect->getDistinct();
	  	 }

	  	 /**
	      * Returns the total number of records in the current result list.
	      * 
	      * @return The number of records in the current result list
	      */
	     public function getResultCount() {

	     	       return $this->resultCount;
	     }

	     /**
	      * Returns a result set from the database. The result list is returned as
	      * an array of stdClass objects, each representing a row in the database.
	      * 
	      * @return An array of stdClass objects, eaching representing a row in the database
	      */
	     public function getResultList() {

	     	  	   return $this->resultList;
	     }

	     /**
		  * Sets the maximum number of records to return in a result list
		  * 
		  * @param $count Maximum number of records to return
		  * @return void
	      */
	     public function setMaxResults( $count ) {

	     	       $this->maxResults = $count;
	     }

	     /**
	      * Returns the maximum number of results to retrieve. This translates
	      * to an SQL LIMIT clause during SELECT operations.
	      * 
	      * @return The maximum number of results to retrieve during SELECT operations.
	      */
	     public function getMaxResults() {
	     	
	     		   return $this->maxResults;
	     }
	     
	     /**
	  	  * Begins a transaction
	  	  * 
	  	  * @return void
	  	  * @throws AgilePHP_PersistenceException
	  	  * @see http://us2.php.net/manual/en/pdo.transactions.php
	  	  * @see http://usphp.com/manual/en/function.PDO-beginTransaction.php
	  	  */
	  	 public function beginTransaction() {

	  	 		$this->dialect->beginTransaction();
	  	 }

	  	 /**
	  	  * Commits an already started transaction.
	  	  * 
	  	  * @return void
	  	  * @throws AgilePHP_PersistenceException
	  	  * @see http://us2.php.net/manual/en/pdo.transactions.php
	  	  * @see http://usphp.com/manual/en/function.PDO-commit.php
	  	  */
	  	 public function commit() {
	  	 	
	  	 		$this->dialect->commit();
	  	 }

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
	  	 public function rollBack( $message = null, $code = 0 ) {

	  	 		$this->dialect->rollBack( $message, $code );
	  	 }
	  	 
	  	 /**
		  * Prepares an SQL prepared statement
		  * 
		  * @param $statement The SQL statement to prepare
		  * @return False if the statement could not execute successfully
		  * @see http://usphp.com/manual/en/function.PDO-prepare.php
	  	  */
	  	 public function prepare( $statement ) {

	  	 		return $this->dialect->prepare( $statement );
	  	 }
	  	 
	  	 /**
	  	  * Executes a prepared statement (with parameters)
	  	  * 
	  	  * @param $inputParameters An array of input parameters
	  	  * @return True if successful, false on fail
	  	  * @see http://usphp.com/manual/en/function.PDOStatement-execute.php
	  	  */
	  	 public function execute( array $inputParameters = array() ) {
	  	 	
	  	 		return $this->dialect->execute( $inputParameters );
	  	 }
	  	 
		 /**
	  	  * Executes an SQL statement and returns the number of rows affected by the query.
	  	  * 
	  	  * @param $statement The SQL statement to execute.
	  	  * @return The number of rows affected by the query.
	  	  * @see http://usphp.com/manual/en/function.PDO-exec.php
	  	  */
	  	 public function exec( $statement ) {

	  		    return $this->dialect->exec( $statement );
	  	 }

	  	 /**
	  	 * Quotes a string so its theoretically safe to pass into a statement
	  	 * without the worry of SQL injection.
	  	 * 
	  	 * @param $data The data to quote
	  	 * @return The quoted data
	  	 * @see http://www.php.net/manual/en/pdo.quote.php
	  	 */
	  	public function quote( $data ) {

	  		   $this->dialect->quote( $data );
	  	}

		/**
	   	 * Executes a raw SQL query using PDO::query
	   	 * 
	   	 * @param $sql The SQL statement to execute
	   	 * @return PDO::PDOStatement as returned by PDO::query
	   	 */
	  	public function query( $sql ) {

	  	 	   return $this->dialect->query( $sql );
	  	}

	     /**
	  	  * Sets the SQL statement to use when calling executeQuery.
	  	  * 
	  	  * @param $sql A valid SQL statement
	      */
	     public function createQuery( $sql ) {

	     		   $this->sql = $sql;
	     }

	     /**
	      * Returns the current SQL query
	      * 
	      * @return The current SQL query
	      */
	     public function getQuery() {

	     		   return $this->sql;
	     }

	     /**
		  * Sets the pagination page number and performs an SQL query to populate the 'resultList'
		  * and 'resultCount' properties with their appropriate values for the specified page.
		  * 
		  * @param $pageNumber The page number
		  * @return void
	      */
	     public function setPage( $pageNumber ) {

	     	       if( !is_numeric( $pageNumber ) || !$pageNumber )
	     	           $pageNumber = 1;

	     	       $this->page = $pageNumber;
				   $this->executeCountQuery();

				   if( $this->page > $this->pageCount )
				       $pageNumber = $this->pageCount;

	     	       $this->createSQL();
				   $this->executeQuery();
	     }

	     /**
	      * Sets WHERE clause restrictions
	      * 
	      * @param $restrictions An associative array containing WHERE clause restrictions. (For example: array( 'id' => 21 ) )
	      * @return void
	      */
	     public function setRestrictions( array $restrictions ) {

	     		   $this->restrictions = $restrictions;
	     }

	     /**
	      * Sets the restriction operator (and|or) used in SQL WHERE clause.
	      * 
	      * @param $operator The logical operator 'and'/'or' to be used in SQL WHERE clause. Default is 'AND'.
	      * @return void
	      */
	     public function setRestrictionsLogicOperator( $operator ) {

	     	       if( strtolower( $operator ) !== 'and' && strtolower( $operator ) !== 'or' )
	     	    	   throw new AgilePHP_PersistenceException( 'Restrictions logic operator must be either \'and\' or \'or\'. Found \'' . $operator . '\'.' );

	     	       $this->restrictionsLogic = $operator;
	     }

	     /**
	      * Sets the SQL 'group by' clause.
	      * 
	      * @param $column The column name to group the result set by
	      * @return void
	      */
	     public function setGroupBy( $column ) {

	     		   $this->groupBy = $column;
	     }

	     /**
	      * Sets the SQL 'order by' clause.
	      * 
	      * @param $column The column name to order the result set by
	      * $param $direction The direction to sort the result set (ASC|DESC).
	      * @return void
	      */
	     public function setOrderBy( $column, $direction ) {

	     		   $this->orderBy = $column;
	     		   $this->orderDirection = $direction;
	     }

	     /**
	      * Returns an associative array containing the current 'orderBy' clause. The results
	      * are returned with the name of the column as the index and the direction as the value.
	      * 
	      * @return An associative array containing the name of the column to sort as the key/index
	      * 		and the direction of the sort order (ASC|DESC) as the value. 
	      */
	     public function getOrderBy() {

	     		   return array( 'column' => $this->orderBy, 'direction' => $this->orderDirection );
	     }

	     /**
	      * Returns the total number of records in the database table
	      * 
	      * @return Total number of records
	      */
	     public function getCount() {

	     	       return $this->count;
	     }
	     
	     /**
	      * Returns the current page number
	      * 
	      * @return The current page number
	      */
	     public function getPage() {

	     		   return $this->page;
	     }

	     /**
	      * Returns the total number of pages. This is calculated by dividing the total
	      * number of records by 'maxResults'.
	      * 
	      * @return The total number of pages
	      */
	     public function getPageCount() {

	     	       return $this->pageCount;
	     }

	     /**
	      * Returns boolean result based on whether or not a 'next page' result is available during
	      * a pagination request.
	      * 
	      * @return True if there is a next page, false if this is the last page
	      */
	     public function nextExists() {

	     	       return $this->page < $this->pageCount;
	     }

	     /**
	      * Returns boolean result based on whether or not a 'previous page' result is available during
	      * a pagination request.
	      * 
	      * @return True if there is a previous page, false if this is the first page
	      */
	     public function previousExists() {

	     		   return $this->page != 0 && $this->page != 1;
	     }

	     /**
	      * Sets the result list up with a 'next page' of a pagination request
	      * 
	      * @return void
	      */
	     public function getNextResultList() {

	     	       $this->setPage( $this->page + 1 );
	     }

	     /**
	      * Sets the result list up with a 'previous page' of a pagination request
	      * 
	      * @return void
	      */
	     public function getPreviousResultList() {

	     	       $this->setPage( $this->page - 1 );
	     }

	  	 /**
	  	  * Returns the database currently being used by the PersistenceManager.
	  	  * 
	  	  * @return The 'Database' object currently in use by the PersistenceManager.
	  	  */
	  	 public function getSelectedDatabase() {

	  	 		return $this->dialect->getDatabase();
	  	 }

	  	/**
	     * Returns the 'Table' object which is mapped to the specified $model.
	     * 
	     * @param $model The domain model object to retrieve the table element for. Defaults to the model
	     * 			   currently being managed by the 'PersistenceManager'.
	     * @return The 'Table' object responsible for the model's persistence or null if a table
	     * 		 could not be located for the specified $model.
	     */
	    public function getTableByModel( $model ) {

			   return $this->dialect->getTableByModel( $model );
	    }

	    /**
	     * Returns a 'Table' object representing the table configured in persistence.xml as
	     * the AgilePHP 'Identity' table.
	     * 
	     * @return The 'Table' object which represents the AgilePHP 'Identity' table, or null
	     * 		 if an 'Identity' table has not been configured.
	     */
	    public function getTableByModelName( $modelName ) {

			   return $this->dialect->getTableByModelName( $modelName );
	  	}

	  	/**
	  	 * Returns a 'Table' object by its name as configured in persistence.xml
	  	 * 
	  	 * @param $tableName The value of the table's 'name' attribute
	  	 * @return The 'Table' object or null if the table was not found
	  	 */
	  	public function getTableByName( $tableName ) {

	  		   return $this->dialect->getTableByName( $tableName );
	  	}

	  	/**
	   	 * Returns a SimpleXMLElement representing the table configured in persistence.xml as
	   	 * an AgilePHP 'Identity' table.
		 * 
	     * @return The SimpleXMLElement instance containing the 'Identity' table, or null
	     * 		   if an 'Identity' table has not been configured.
	     */
	    public function getIdentityTable() {

			   return $this->dialect->getIdentityTable();
	    }

	    /**
	     * Returns the domain object model responsible for 'Identity' persistence.
	     * 
	     * @return The domain object model responsible for 'Identity' persistence.
	     */
	    public function getIdentityModel() {

	  		   return $this->dialect->getIdentityModel();
	    }

	    /**
	     * Returns a SimpleXMLElement representing the table configured in persistence.xml as
	     * an AgilePHP 'SessionScope' session table.
	     * 
	     * @return The SimpleXMLElement instance containing the 'SessionScope' session table
	     * 		 or null if a session table has not been configured.
	     */
	    public function getSessionTable() {

			   return $this->dialect->getSessionTable();
		}

	  	/**
	     * Returns the domain model object responsible for 'SessionScope' sessions.
	     * 
	     * @return void
	     */
	  	public function getSessionModel() {

	  		   return $this->dialect->getSessionModel();
		}

		/**
		 * Returns the column 'name' attribute value configured in persistence.xml for the specified
		 * column 'property' attribute.
		 * 
		 * @param $table The 'Table' object to search
		 * @param $property The property attributes value
		 * @return The column name or null if the $property could not be found in the table
		 */
		public function getColumnNameByProperty( $table, $property ) {

			   return $this->dialect->getColumnNameForProperty( $table, $property );
		}

		/**
		 * Returns the 'property' name configured in persistence.xml for the specified
		 * column 'name' attribute.
		 * 
		 * @param $table The 'Table' object to search
		 * @param $columnName The column name to search
		 * @return The column name or null if the $property could not be found in the table
		 */
		public function getPropertyNameByColumn( $table, $columnName ) {

			   return $this->dialect->getPropertyNameForColumn( $table, $columnName );
		}
		
		/**
	     * Creates an accessor method from the $property parameter. The $property
	     * will be returned with the prefix 'get' and the first letter of the property
	     * uppercased.
	     * 
	     * @param $property The name of the property to convert to an accessor method name
	     * @return The accessor string
	     */
	    public function toAccessor( $property ) {

	     	   return $this->dialect->toAccessor( $property );
	    }

	    /**
	     * Creates a mutator method from the $property parameter. The $property
	     * will be returned with the prefix 'set' and the first letter of the property
	     * uppercased.
	     * 
	     * @param $property The name of the property to convert to a mutator method name
	     * @return The mutator string
	     */
	    public function toMutator( $property ) {

	     	   return $this->dialect->toMutator( $property );
	    }

	     /**
	      * Returns the specified number as a 'bigint' 64-bit number.
	      * 
	      * @return void
	      */
	     public function toBigInt( $number ) {

	     		return $this->dialect->toBigInt( $number );
	     }
	     
		 /**
		  * Creates the database context as defined in persistence.xml.
		  * 
		  * @return void
		  */
		 public function create() {

				 $this->dialect->create();
		  }

		  /**
	   	   * Drops the database specified in persistence.xml
	   	   * 
	   	   * @return void
	   	   * @throws AgilePHP_PersistenceException
	   	   */
	  	  public function drop() {

	  			 $this->dialect->drop();
	  	  }

	  	  /**
	   	   * Persists a domain model object
	   	   * 
		   * @param $model The model object to persist
		   * @return PDOStatement
		   * @throws AgilePHP_PersistenceException
		   */
	  	  public function persist( $model ) {

	  			 return $this->dialect->persist( $model );
	  	  }

	  	  /**
	   	   * Merges/updates a persisted domain model object
	   	   * 
		   * @param $model The model object to merge/update
		   * @return PDOStatement
		   * @throws AgilePHP_PersistenceException
		   */
	  	  public function merge( $model ) {

				 return $this->dialect->merge( $model );
	  	  }

		  /**
		   * Deletes a persisted domain model object
	   	   * 
		   * @param $model The domain model object to delete
		   * @return PDOStatement
		   * @throws AgilePHP_PersistenceException
		   */
		  public function delete( $model ) {

	  	     	 return $this->dialect->delete( $model );
	  	  }

	  	  /**
	   	   * Truncates the table for the specified domain model object
	   	   * 
		   * @param $model A domain model object
		   * @return PDOStatement
		   * @throws AgilePHP_PersistenceException
		   */
		 public function truncate( $model ) {

	  		    return $this->dialect->truncate( $model );
	  	 }

	  	 /**
	   	  * Attempts to locate the specified model by primary key value.
	      * 
	   	  * @param $model A domain model object with its primary key field set
	   	  * @param $findAll True/false flag indicating whether or not to perform
	   	  * 				a SELECT * for empty model.
	      * @return Returns the same model which was passed (populated with the
	      * 		 database values) or null if a matching record could not be found.
	      * @throws AgilePHP_PersistenceException
	      */
	  	 public function find( $model, $findAll = false ) {

	  		    return $this->dialect->find( $model, $findAll );
	  	 }

 		 /**
	      * Executes an SQL count query for total number of records in the database. 
	      * Initalizes 'pageCount' and 'count' properties. 
	      * 
	      * @return void
	      */
	     public function executeCountQuery() {

	     	       $sql = 'SELECT count(*) as count FROM ' . $this->getTableName();
				   $sql .= ($this->createRestrictSQL() == null) ? '' : $this->createRestrictSQL();
				   $sql .= ';';

	     	       Logger::getInstance()->debug( 'BaseModelController::executeCountQuery executing raw sql query ' . $sql );

	     	       $stmt = $this->query( $sql );
  			 	   $stmt->setFetchMode( PDO::FETCH_OBJ );
  			 	   $result = $stmt->fetchAll();

  			 	   $this->pageCount = ceil( $result[0]->count / $this->maxResults );
  			 	   $this->count = $result[0]->count;
	     }

	     /**
	      * Executes the current SQL statement as defined in 'sql'.
	      * 
	      * @return void
	      */
	     public function executeQuery() {

	     	       if( !$this->sql )
	     	       	   $this->createDefaultSQL();

				   Logger::getInstance()->debug( 'BaseModelController::executeQuery executing raw sql query ' . $this->sql );

	     	       $stmt = $this->query( $this->sql );
  			 	   $stmt->setFetchMode( PDO::FETCH_OBJ );

  			 	   $this->resultList = $stmt->fetchAll();
  			 	   $this->resultCount = sizeof( $this->resultList );

  			 	   $this->sql = null;
  			 	   $this->groupBy = null;
  			 	   $this->orderBy = null;
  			 	   $this->orderDirection = 'ASC';
  			 	   $this->restrictions = null;
  			 	   $this->restrictionsLogic = 'AND';
	     }

		 /**
	      * Creates an SQL query and stores it in $this->sql. This method sets
	      * all possible criteria options which have been defined using this
	      * objects 'set' methods (ie. setOrderBy, setGroupBy, setMaxResults, etc...)
	      * 
	      * @return void
	      */
	     private function createSQL() {

	     	 	 $offset = ($this->getPage() - 1) * $this->getMaxResults();
	     	     if( $offset < 0 ) $offset = 0;

	     	     $this->sql = 'SELECT * FROM ' . $this->getTableName();
				 $this->sql .= ($this->groupBy == null) ? '' : ' GROUP BY ' . $this->groupBy;
				 $this->sql .= ($this->orderBy == null) ? '' : ' ORDER BY ' . $this->orderBy . ' ' . $this->orderDirection;
				 $this->sql .= ($this->createRestrictSQL() == null) ? '' : $this->createRestrictSQL();
				 $this->sql .= ($offset > 0) ? ' LIMIT ' . $offset . ', ' . $this->getMaxResults() . ';' 
											 : ' LIMIT ' . $this->getMaxResults() . ';';

				 Logger::getInstance()->debug( 'BaseModelController::createSQL '. $this->sql );
	     }

	     /**
	      * Creates a default SQL select query
	      * 
	      * @return void
	      */
	     private function createDefaultSQL() {

	     	     $this->sql = 'SELECT * FROM ' . $this->getTableName() . ' LIMIT ' . $this->maxResults . ';';

	     	     Logger::getInstance()->debug( 'BaseModelController::createDefaultSQL ' . $this->sql );
	     }

	     /**
	      * Returns an SQL formatted string containing a WHERE clause built from setRestrictions and setRestrictionsLogicOperator.
	      * 
	      * @return The formatted SQL string
	      */
	     private function createRestrictSQL() {

	     		 $restricts = null;
				 if( count( $this->restrictions ) ) {

				  	 $restricts = ' WHERE ';
					 $index = 0;
					 foreach( $this->restrictions as $key => $val ) {

					   		  $index++;
					   		  $restricts .= $key . '=\'' . $val . '\'';

					   		  if( $index < count( $this->restrictions ) )
					   			  $restricts .= ' ' . $this->restrictionsLogic . ' ';
					 }
				 }

				 return $restricts;
	     }
}
?>