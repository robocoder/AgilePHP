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

/**
 * Includes base persistence dependencies
 */
require_once 'persistence/dialect/SQLDialect.php';
require_once 'persistence/BasePersistence.php';

/**
 * Facade for working with persisence operations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 * @version 0.2a
 */
class PersistenceManager implements SQLDialect {

	  private static $instance;				// Stores singleton instance
	  private $dialect;						// Stores the dialect object used to execute vendor specific SQL queries
	  private $databases = array();
	  private $database;					// Stores the current database

	  private $sql;							// Current SQL query.
	  private $resultCount;					// Total number of items in result list
	  private $resultList;					// The sql result list
	  private $count; 	    				// Total number of records in the table
	  private $page;						// Holds the current page number
	  private $pageCount;					// Total number of pages
	  protected $model;						// Stores an ActiveRecord model

	  /**
	   * Initalizes the PersistenceManager object based on persistence.xml configuration. Each of the
	   * databases, tables, and columns are transformed into corresponding AgilePHP persistence objects.
	   * If a 'databaseId' is present, PersistenceManager is initalized with the specified database.
	   * 
	   * @param String $databaseId Optional database id as defined in persistence.xml Defaults to the first database in persistence.xml.
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
	  	 }

	  	 /**
	  	  * Returns singleton instance of PersistenceManager
	  	  * 
	  	  * @param String $databaseId Optional database id as defined in persistence.xml Defaults to the first database in persistence.xml.
	  	  * @return PersistenceManager Singeton instance of PersistenceManager
	  	  */
	  	 public static function getInstance( $databaseId = null ) {

	  	 		if( self::$instance == null )
	  	 			self::$instance = new self;

	  	 		return self::$instance;
	  	 }

	  	 /**
	  	  * Establishes a connection to the specified database.
	  	  * 
	  	  * @param String $db A Database object to establish the connection with
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

	  	     	     case 'mssql':

	  	     	     	  if( strtolower( $db->getDriver() ) == 'sqlsrv' ) {

	  	     	     	  	  require_once 'persistence/dialect/SQLSRVDialect.php';
	  	     	     	  	  $this->dialect = new SQLSRVDialect( $db );
	  	     	     	  }
	  	     	     	  else {
	  	     	     	  	  require_once 'persistence/dialect/MSSQLDialect.php';
	  	     	     	  	  $this->dialect = new MSSQLDialect( $db );
	  	     	     	  }
	  	     	     	  break;

  	     		 	case 'pgsql':
  	     		 		
  	     		 		 require_once 'persistence/dialect/PostgreSQLDialect.php';
  	     		 		 $this->dialect = new PostgreSQLDialect( $db );
  	     		 	  	 break;

  	     		 	 /*
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

	  	     	$this->database = $db;
	  	 }

	  	 /**
	  	  * Returns the databases array.
	  	  * 
	  	  * @return Array array of Database objects, each representing a database configured in persistence.xml
	  	  */
	  	 public function getDatabases() {

	  	 		return $this->databases;
	  	 }

	  	 /**
	  	  * Returns the database object currently in use by the ORM framework.
	  	  * 
	  	  * @return void
	  	  */
	  	 public function getDatabase() {

		 	 	return $this->database;
	  	 }

	  	 /**
	  	  * Returns the SQL dialect instance responsible for invoking SQL commands.
	  	  * 
	  	  * @return void
	  	  */
	  	 public function getDialect() {

	  	 		return $this->dialect;
	  	 }

	    /**
	     * Returns the domain model which the PersistenceManager is manipulating.
	     * 
	     * @return Object The domain model instance which the PersistenceManageris manipulating.
	     */
	    public function getModel() {

	    	   return $this->model;
	    }

	    /**
	     * Sets the domain model the PersistenceManager is to manipulate.
	     * 
	     * @param Object $model The domain model the PersistenceManager is to manipulate
	     * @return void
	     */
	    public function setModel( $model ) {

	    	   $this->model = $model;
	    }

	    /**
	     * Checks the two defined model parameters to see if they are equal. The class name,
	     * property name, type and value.
	     *  
	     * @param Object $modelA The first domain model object
	     * @param Object $modelB The second domain model object
	     * @return True if the comparison was successful, false if they differ.
	     */
	    public function compare( $modelA, $modelB ) {
	    	
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
	     public function setMaxResults( $maxResults = 25 ) {

	     	       $this->dialect->setMaxResults( $maxResults );
	     }

	     /**
	      * Returns the maximum number of results to retrieve. This translates
	      * to an SQL LIMIT clause during SELECT operations.
	      * 
	      * @return The maximum number of results to retrieve during SELECT operations.
	      */
	     public function getMaxResults() {
	     	
	     		   return $this->dialect->getMaxResults();
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
	      * Executes the current sql query as set by createQuery.
	      * 
	      * @return PDOStatement The query result.
	      */
	     public function executeQuery() {

	     		Logger::getInstance()->debug( 'PersistenceManager::executeQuery ' . $this->sql );
	     		return $this->dialect->query( $this->sql );
	     }

		 /**
	      * Executes an SQL count query for total number of records in the database. 
	      * Initalizes 'pageCount' and 'count' properties. 
	      * 
	      * @return void
	      */
	     public function executeCountQuery() {

	     	       $this->count = $this->dialect->count( $this->getModel() );     	       
  			 	   $this->pageCount = ceil( $this->count / $this->getMaxResults() );
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

				   $offset = ($this->getPage() - 1) * $this->getMaxResults();
	     	       if( $offset < 0 ) $offset = 0;

  	 	     	   $this->dialect->setOffset( $offset );
				   $result = $this->find( $this->getModel() );

				   if( !$result ) return false;

				   $this->resultList = $result;
				   $this->resultCount = count( $result );

				   $this->groupBy = null;
	  		 	   $this->dialect->setOrderBy( null, 'ASC' );
	  		 	   $this->dialect->setRestrictions( array() );
	  		 	   $this->dialect->setRestrictionsLogicOperator( 'AND' );

				   Logger::getInstance()->debug( 'BaseModelController::setPage ' . $this->page );
	     }

	     /**
	      * Sets WHERE clause restrictions
	      * 
	      * @param $restrictions An associative array containing WHERE clause restrictions. (For example: array( 'id' => 21 ) )
	      * @return void
	      */
	     public function setRestrictions( array $restrictions ) {

	     		$this->dialect->setRestrictions( $restrictions );
	     }

	     /**
	      * Sets the restriction operator (and|or) used in SQL WHERE clause.
	      * 
	      * @param $operator The logical operator 'and'/'or' to be used in SQL WHERE clause. Default is 'AND'.
	      * @return void
	      */
	     public function setRestrictionsLogicOperator( $operator ) {

	     	    $this->dialect->setRestrictionsLogicOperator( $operator );
	     }

	     /**
	      * Sets the SQL 'group by' clause.
	      * 
	      * @param $column The column name to group the result set by
	      * @return void
	      */
	     public function setGroupBy( $column ) {

	     		   $this->dialect->setGroupBy( $column );
	     }

	     /**
	      * Returns SQL GROUP BY clause.
	      * 
	      * @return String GROUP BY value
	      */
	     public function getGroupBy() {

	     		return $this->dialect->getGroupBy();
	     }

	     /**
	      * Sets the SQL 'order by' clause.
	      * 
	      * @param $column The column name to order the result set by
	      * $param $direction The direction to sort the result set (ASC|DESC).
	      * @return void
	      */
	     public function setOrderBy( $column, $direction ) {

	     		$this->dialect->setOrderBy( $column, $direction );
	     }

	     /**
	      * Returns an associative array containing the current 'orderBy' clause. The results
	      * are returned with the name of the column as the index and the direction as the value.
	      * 
	      * @return An associative array containing the name of the column to sort as the key/index
	      * 		and the direction of the sort order (ASC|DESC) as the value. 
	      */
	     public function getOrderBy() {

	     		return $this->dialect->getOrderBy();
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
	    public function getTableByModel( $model = null ) {

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
		public function getColumnNameForProperty( $table, $property ) {

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
		public function getPropertyNameForColumn( $table, $columnName ) {

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
	   	  * @param Object $model A domain model object with its primary key field set
	      * @return Returns the same model which was passed (populated with the
	      * 		 database values) or null if a matching record could not be found.
	      * @throws AgilePHP_PersistenceException
	      */
	  	 public function find( $model ) {

	  		    return $this->dialect->find( $model );
	  	 }

	  	 /**
	  	  * Returns AgilePHP ORM database structure for the current database.
	  	  * 
	  	  * @return Array Multi-dimensional array representing the current database structure.
	  	  */
	  	 public function reverseEngineer() {

	  	 		return $this->dialect->reverseEngineer();
	  	 }

	     /**
	      * Returns an SQL formatted string containing a WHERE clause built from setRestrictions and setRestrictionsLogicOperator.
	      * 
	      * @return The formatted SQL string
	      */
	     public function createRestrictSQL() {

	     		 return $this->dialect->createRestrictSQL();
	     }
}
?>