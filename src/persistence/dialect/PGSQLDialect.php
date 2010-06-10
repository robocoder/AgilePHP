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
 * Handles PostgreSQL specific queries
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.persistence.dialect
 */
class PGSQLDialect extends BasePersistence implements SQLDialect {

	  private $connectFlag = -1;

	  /**
	   * Initalize PostgreSQLDialect
	   * 
	   * @param Database $db The Database object representing persistence.xml
	   * @return void
	   */
	  public function __construct( Database $db ) {

	  	     try {
	  	     		$conn = 'pgsql:' .
	  	     				(($db->getName()) ? 'dbname=' . $db->getName() . ';': '' ) .
	  	  					(($db->getHostname()) ? 'host=' . $db->getHostname() . ';': '' ) .
	  	  					(($db->getUsername()) ? 'user=' . $db->getUsername() . ';': '' ) .
	  	  					(($db->getPassword()) ? 'password=' . $db->getPassword() . ';' : '' );

	  	  			$this->pdo = new PDO( $conn );
	  	  			$this->connectFlag = 1;	
	  	     }
	  	     catch( PDOException $pdoe ){

	  	     	    Log::debug( 'PostgreSQLDialect::__construct Warning about \'' . $pdoe->getMessage() . '\'.' );

	  	     		// If the database doesnt exist, try a generic connection to the server. This allows the create() method to
	  	     		// be invoked to create the database schema.
	  	     	    if( strpos( $pdoe->getMessage(), 'does not exist' ) ) {

	  	     	    	$conn = 'pgsql:' .
	  	  					(($db->getHostname()) ? 'host=' . $db->getHostname() . ';': '' ) .
	  	  					(($db->getUsername()) ? 'user=' . $db->getUsername() . ';': '' ) .
	  	  					(($db->getPassword()) ? 'password=' . $db->getPassword() . ';' : '' );

	  	     	    	$this->pdo = new PDO( $conn );
	  	     	    	$this->connectFlag = 0;
	  	     	    }
	  	     	    else {

	  	     	    	$this->connectFlag = -1;
	  	     	    	throw new PersistenceException( 'Failed to create PostgreSQLDialect instance. ' . $pdoe->getMessage() );
	  	     	    }
	  	     }

	 	     $this->database = $db;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#isConnected()
	   */
	  public function isConnected() {

	  		 return $this->connectFlag == true;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#create()
	   */
	  public function create() {

	  		 $this->query( 'CREATE DATABASE ' . $this->database->getName() . ';' );

	  		 // Now that the database is present, connect directly to the database.
	  		 $this->pdo = new PDO( 'pgsql:host=' . $this->database->getHostname() . ';dbname=' . $this->database->getName(),
	  		 						 $this->database->getUsername(), $this->database->getPassword() );

	  		 $constraintFails = array();
	  		 foreach( $this->database->getTables() as $table ) {

	  		 		  try {
	  		 		  	    $sql = $this->toCreateTableSQL( $table );
			   		  		$this->query( $sql );
	  		 		  }
	  		 		  catch( PersistenceException $e ) {

	  		 		  		 if( preg_match( '/does not exist/', $e->getMessage() ) ) {

	  		 		  		 	 array_push( $constraintFails, $sql );
	  		 		  		 	 Log::warn( 'PGSQLDialect::create Failed to create table \'' . $table->getName() . '\'. Will retry after processing all tables in case this is a foreign key constraint issue due to a table listed further down in persistence.xml' );
	  		 		  		 	 continue;
	  		 		  		 }

	  		 		  		 Log::debug( $e->getMessage() );
	  		 		  }
	  		 }

	  		 // Constraint hack continued
	  		 if( count( $constraintFails ) )
	  		 	 foreach( $constraintFails as $sql )
	  		 	 		if( !$this->query( $sql ) )
		  		 	 		throw new PersistenceException( print_r( $e, true ) );
	  }

	  /**
	   * Generates SQL CREATE TABLE for the specified table.
	   * 
	   * @param Table $table The table to generate the CREATE TABLE code for
	   * @return string The SQL CREATE TABLE code
	   */
	  private function toCreateTableSQL( Table $table ) {

	  		  $defaultKeywords = array( 'CURRENT_TIMESTAMP' );  // Default values that get passed unquoted

  	 		  $sql = 'CREATE TABLE ' . $table->getName() . ' ( ';

  	 		  foreach( $table->getColumns() as $column ) {

  	 				   $sql .= $column->getName() . ' ' . $column->getType() . 
  	 						   (($column->getLength()) ? '(' . $column->getLength() . ')' : '') .
  	 						   (($column->isRequired() == true && !$column->isAutoIncrement()) ? ' NOT NULL' : '') .
  	 						   (($column->getDefault()) ? ' DEFAULT ' . (in_array($column->getDefault(),$defaultKeywords) ? $column->getDefault() : '\'' . $column->getDefault() . '\'') . '': '') .
  	 						   ((!$column->getDefault() && !$column->isRequired() && !$column->isAutoIncrement()) ? ' DEFAULT NULL' : '') . ', ';
  	 		  }

   			  $pkeyColumns = $table->getPrimaryKeyColumns();
   			  if( count( $pkeyColumns ) ) {

   			  	  $sql .= ' PRIMARY KEY ( ';
   				  for( $i=0; $i<count( $pkeyColumns ); $i++ ) {

   					   $sql .= $pkeyColumns[$i]->getName();

   					   if( ($i+1) < count( $pkeyColumns ) )
   						   $sql .= ', ';
   				  }
   				  $sql .= ' )';

   				  /*
   				  if( count( $pkeyColumns ) > 1 )
   				  	  $sql .= ', UNIQUE KEY `' . $pkeyColumns[0]->getName() . '` (`' . $pkeyColumns[0]->getName() . '`)';
   				  */
   			  }

	   		  if( $table->hasForeignKey() ) {

	   		  	  $foreignKeyColumns = $table->getForeignKeyColumns();
	   		  	  for( $i=0; $i<count( $foreignKeyColumns ); $i++ ) {

	   		  	  	   $fk = $foreignKeyColumns[$i]->getForeignKey();

	   		  	  	   $sql .= ', CONSTRAINT ' . $fk->getName() . ' FOREIGN KEY ( ' . 
	   		  	  	   				$fk->getColumnInstance()->getName() .
   	  	  		       	        ' ) REFERENCES ' . $fk->getReferencedTable() . ' ( ' .
   	  	  		    	        $fk->getReferencedColumn() . ' ) ';

     	  		   	   $sql .= (($fk->getOnUpdate()) ? ' ON UPDATE ' . $fk->getOnUpdate() : '' );
     	  		   	   $sql .= (($fk->getOnDelete()) ? ' ON DELETE ' . $fk->getOnDelete() : '' );
	   		  	  }
	   		  }

			  $sql .= ');';

	   		  return $sql;
	  }

	  public function createTable( Table $table ) {

	  		 $this->query( $this->toCreateTableSQL( $table ) );
	  }

	  public function dropTable( Table $table ) {
	  	
	  		 $this->query( 'DROP TABLE ' . $table->getName() );
	  }

	    public function persist( $model ) {

	    	   $this->model = $model;

	   		   $values = array();
			   $table = $this->getTableByModel( $model );

			   Log::debug( 'BasePersistence::persist Performing persist on model \'' . $table->getModel() . '\'.' );

	   		   $this->validate( $table, true );

			   $sql = 'INSERT INTO ' . $table->getName() . '( ';

			   $columns = $table->getColumns();
			   for( $i=0; $i<count( $columns ); $i++ ) {

			   		if( $columns[$i]->isAutoIncrement() )
			   			continue;

			   		$sql .= $columns[$i]->getName();

			   		if( ($i + 1) < count( $columns ) )
			   			$sql .= ', ';
			   }
			   $sql .= ' ) VALUES ( ';
			   for( $i=0; $i<count( $columns ); $i++ ) {

			   		if( $columns[$i]->isAutoIncrement() )
			   				continue;

			   		$sql .= '?';
			   	    $method = $this->toAccessor( $columns[$i]->getModelPropertyName() );

			   	    if( $columns[$i]->isForeignKey() ) {

			   	    	// php namespace support - extract the class name from the fully qualified class path
  		  		   	    $foreignModelPieces = explode( '\\', $columns[$i]->getForeignKey()->getReferencedTableInstance()->getModel() );
  		  		   	    $foreignModelName = array_pop( $foreignModelPieces );

  		  		   	    // Create accessor names for both the foreign model instance and the foreign model's instance accessor
						$instanceAccessor = $this->toAccessor( $foreignModelName );
			   	    	$accessor = $this->toAccessor( $columns[$i]->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName() );

			   	    	if( is_object( $model->$instanceAccessor() ) ) {

			   	    		// Get foreign key value from the referenced field/instance accessor
			   	    		if( $model->$instanceAccessor()->$accessor() != null ) {
			   	    			array_push( $values, $model->$instanceAccessor()->$accessor() );
			   	    		}
			   	    		else {
			   	    			// Persist the referenced model instance and use its new id as the foreign key value
				   	    		$this->persist( $model->$instanceAccessor() );
				   	    		$this->prepare( 'SELECT currval(?) AS lastInsertId;' );
				   	    		$stmt = $this->execute( array( $columns[$i]->getForeignKey()->getReferencedTableInstance()->getName() . '_id_seq' ) );
				   	    		$lastInsertId = $stmt->fetch();
				   	    		array_push( $values, $lastInsertId[0] );
			   	    		}
			   	    	}
			   	    	else {
			   	    		// @todo Extract using the foreign key property (is this bad or should we stick to objects only?)
			   	        	array_push( $values, ($model->$method() == '') ? NULL : $model->$method() );
			   	        }
			   	    }
			   	    else // No foreign key
			   	    	array_push( $values, (($model->$method() == '') ? NULL : $model->$method()) );

			   		if( ($i + 1) < count( $columns ) )
				   		$sql .= ', ';
			   }
			   $sql .= ' );';

	   		   $this->prepare( $sql );
	  		   return $this->execute( $values );
	    }

	    /**
	     * Merges/updates a persisted domain model object
	     * 
	     * @param $model The model object to merge/update
	     * @return PDOStatement
	     * @throws PersistenceException
	     */
	    public function merge( $model ) {

	    	   $this->model = $model;
	    	   $table = $this->getTableByModel( $model );

	    	   Log::debug( 'BasePersistence::merge Performing merge on model \'' . $table->getModel() . '\'.' );

	    	   $this->model = $model;
	  	       $values = array();
	  	       $cols = array();
			   $this->validate( $table );

			   $sql = 'UPDATE ' . $table->getName() . ' SET ';

	  		   $columns = $table->getColumns();
	  		   $naCount = 0;
			   for( $i=0; $i<count( $columns ); $i++ ) {

			   	    if( $columns[$i]->isPrimaryKey() || $columns[$i]->isAutoIncrement() )
			   			continue;

			   		$accessor = $this->toAccessor( $columns[$i]->getModelPropertyName() );

			   		// Extract foreign key value from the referenced column
			   	    if( $columns[$i]->isForeignKey() ) {

			   	    	// php namespace support - extract the class name from the fully qualified class path
  		  		   	    $foreignModelPieces = explode( '\\', $columns[$i]->getForeignKey()->getReferencedTableInstance()->getModel() );
  		  		   	    $foreignModelName = array_pop( $foreignModelPieces );

  		  		   	    // Create accessor name for the foreign model instance
						$instanceAccessor = $this->toAccessor( $foreignModelName );

			   	    	if( is_object( $model->$instanceAccessor() ) ) {

			   	    		// Create name for the foreign model's instance accessor
			   	    		$accessor = $this->toAccessor( $columns[$i]->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName() );

			   	    		// Get foreign key value from the referenced instance
			   	    		if( $model->$instanceAccessor()->$accessor() != null ) {

			   	    			array_push( $values, $model->$instanceAccessor()->$accessor() );
			   	    			// @todo Need a way to check if the model is dirty so we dont waste database calls updating data that already exists.
			   	    			//		 Possibly #@Column interceptor and move away from xml altogether? Getting rid of xml will speed up apps with a
			   	    			//		 large database since a large persistence.xml file will have to be parsed with each page hit... 
			   	    			$this->merge( $model->$instanceAccessor() );
			   	    		}
			   	    		else {
			   	    			// Persist the referenced model instance, and use its new id as the foreign key value
				   	    		$this->persist( $model->$instanceAccessor() );
				   	    		$this->prepare( 'SELECT currval(?) AS lastInsertId;' );
				   	    		$stmt = $this->execute( array( $columns[$i]->getForeignKey()->getReferencedTableInstance()->getName() . '_id_seq' ) );
				   	    		$lastInsertId = $stmt->fetch();
				   	    		array_push( $values, $lastInsertId[0] );
			   	    		}
			   	    	}
			   	    	else {
			   	    		// @todo Extract using the foreign key property (is this bad or should we stick to objects only?)
			   	        	array_push( $values, ($model->$accessor() == '') ? NULL : $model->$accessor() );
			   	        }
			   	    }
			   	    else // not a foreign key
			   	    	array_push( $values, $model->$accessor() );

			   	    array_push( $cols, $columns[$i]->getName() );
			   }

			   $sql .= implode( $cols, '=?, ' ) . '=? WHERE ';

			   $pkeyColumns = $table->getPrimaryKeyColumns();
			   for( $i=0; $i<count( $pkeyColumns ); $i++ ) {

			  	    $accessor = $this->toAccessor( $columns[$i]->getModelPropertyName() );
			  		$sql .= $columns[$i]->getName() . '=\'' . $model->$accessor() . '\'';

			  		if( ($i+1) < count( $pkeyColumns ) )
			  		    $sql .= ' AND ';
			   }

			   $sql .= ';';

		       $this->prepare( $sql );
	  	       return $this->execute( $values );
	    }
	    
		/**
	     * Attempts to locate the specified model by values. Any fields set in the object are used
	     * in search criteria. Alternatively, setRestrictions and setOrderBy methods can be used to
	     * filter results.
	     * 
	     * @param $model A domain model object. Any fields which are set in the object are used to filter results.
	     * @throws PersistenceException If any primary keys contain null values or any
	     * 		   errors are encountered executing queries
	     */
	    public function find( $model ) {

	    	   $table = $this->getTableByModel( $model );
			   $newModel = $table->getModelInstance();
			   $values = array();

			   Log::debug( 'PGSQLDialect::find Performing find on model \'' . $table->getModel() . '\'.' );

	  		   try {
	  		   		 if( $this->isEmpty( $model ) ) {

	    	   	         $sql = 'SELECT ' . (($this->getDistinct() == null) ? '*' : 'DISTINCT ' . $this->getDistinct()) . ' FROM ' . $table->getName();

	    	   	         $order = $this->getOrderBy();
	    	   	         $offset = $this->getOffset();
	    	   	         $groupBy = $this->getGroupBy();

    	   	         	 $sql .= ($this->getRestrictions() != null) ? $this->createRestrictSQL() : '';
					 	 $sql .= ($order != null) ? ' ORDER BY ' . $order['column'] . ' ' . $order['direction'] : '';
					 	 $sql .= ($groupBy)? ' GROUP BY ' . $this->getGroupBy() : '';
					 	 $sql .= ($offset && $this->getMaxResults()) ? ' LIMIT ' . $offset . ', ' . $this->getMaxResults() : '';
					 	 $sql .= (!$offset && $this->getMaxResults()) ? ' LIMIT ' . $this->getMaxResults() : '';
    	   	         	 $sql .= ';';

	   	   	         	 $this->setDistinct( null );
    	   	         	 $this->setRestrictions( array() );
    	   	         	 $this->setRestrictionsLogicOperator( 'AND' );
    	   	         	 $this->setOrderBy( null, 'ASC' );
    	   	         	 $this->setGroupBy( null );
	    	   		 }
	    	   		 else {
	    	   		 		$where = '';

	    	   		 		$columns = $table->getColumns();
							for( $i=0; $i<count( $columns ); $i++ ) {

							 	 $accessor = $this->toAccessor( $columns[$i]->getModelPropertyName() );
						     	 if( $model->$accessor() == null ) continue;

						     	 $where .= (count($values) ? ' ' . $this->getRestrictionsLogicOperator() . ' ' : ' ') . $columns[$i]->getName() . ' ' . $this->getComparisonLogicOperator() . ' ?';
				     	 	     array_push( $values, $model->$accessor() );
						    }
						    $sql = 'SELECT * FROM ' . $table->getName() . ' WHERE' . $where;
						    $sql .= ' LIMIT ' . $this->getMaxResults() . ';';
	    	   		 }

					 $this->prepare( $sql );
					 $this->PDOStatement->setFetchMode( PDO::FETCH_OBJ );
					 $result = $this->execute( $values );

					 if( !count( $result ) ) {

					 	 Log::debug( 'PGSQLDialect::find Empty result set for model \'' . $table->getModel() . '\'.' );
					 	 return null;
					 }

				 	 $count = 0;
				 	 $models = array();
					 foreach( $result as $stdClass  ) {

					 		  $m = $table->getModelInstance();
					 	   	  foreach( get_object_vars( $stdClass ) as $name => $value ) {

					 	   	  		   if( !$value ) continue;
					 	   	  		   $modelProperty = $this->getPropertyNameForColumn( $table, $name );

							 	   	   // Create foreign model instances from foreign values
						 	 		   foreach( $table->getColumns() as $column ) {

						 	 		  		    if( $column->isForeignKey() && strtolower( $column->getName() ) == $name ) {

						 	 		  		   	    $foreignModel = $column->getForeignKey()->getReferencedTableInstance()->getModel();
						 	 		  		   	    $foreignInstance = new $foreignModel();

						 	 		  		   	    $foreignMutator = $this->toMutator( $column->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName() );
						 	 		  		   	    $foreignInstance->$foreignMutator( $value );

						 	 		  		   	    $persisted = $this->find( $foreignInstance );

						 	 		  		   	    // php namespace support - remove \ character from fully qualified paths
							 	 		  		   	$foreignModelPieces = explode( '\\', $foreignModel );
							 	 		  		   	$foreignClassName = array_pop( $foreignModelPieces );

						 	 		  		   	    $instanceMutator = $this->toMutator( $foreignClassName );
						 	 		  		   	    $m->$instanceMutator( $persisted[0] );
						 	 		  		    }
						 	 		  		    else {

						 	 		  		   		$mutator = $this->toMutator( $modelProperty );
					 	   	   		  				$m->$mutator( $value );
						 	 		  		    }
						 	 		   }
					 	   	  }

					 	   	  array_push( $models, $m );
					 	   	  $count++;
					 	   	  if( $count == $this->getMaxResults() )  break;
				     }

				     return $models;
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new PersistenceException( $e->getMessage(), $e->getCode() );
	  		 }
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#drop()
	   */
	  public function drop() {

	  		 $this->pdo = null;

	  		 $conn = 'pgsql:dbname=' . $this->database->getUsername() . ';' .
	  	  					(($this->database->getHostname()) ? 'host=' . $this->database->getHostname() . ';': '' ) .
	  	  					(($this->database->getUsername()) ? 'user=' . $this->database->getUsername() . ';': '' ) .
	  	  					(($this->database->getPassword()) ? 'password=' . $this->database->getPassword() . ';' : '' );

	  	     $pdo = new PDO( $conn );
  	 	 	 $pdo->query( 'DROP DATABASE ' . $this->database->getName() . ';' );
  	 	 	 $pdo = null;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#reverseEngineer()
	   */
	  public function reverseEngineer() {

	  		 $Database = new Database();
	  		 $Database->setId( $this->database->getId() );
	  		 $Database->setName( $this->database->getName() );
	  		 $Database->setType( $this->database->getType() );
	  		 $Database->setHostname( $this->database->getHostname() );
	  		 $Database->setUsername( $this->database->getUsername() );
	  		 $Database->setPassword( $this->database->getPassword() );

	  		 $stmt = $pm->prepare( 'SHOW TABLES' );
      	     $stmt->execute();
      	     $stmt->setFetchMode( PDO::FETCH_OBJ );
      	     $tables = $stmt->fetchAll();

      	     $tblIndex = 'Tables_in_' . $pm->getDatabase()->getName();

      	     foreach( $tables as $sqlTable ) {

      	     		  $Table = new Table();
      	     		  $Table->setName( str_replace( ' ', '_', $sqlTable->$tblIndex ) );
      	     		  $Table->setModel( ucfirst( $Table->getName() ) );

      	      		  $stmt = $pm->query( 'DESC ' . $sqlTable->$tblIndex );
      	      		  $stmt->setFetchMode( PDO::FETCH_OBJ );
      	      		  $descriptions = $stmt->fetchAll();
      	      		   
      	      		  foreach( $descriptions as $desc ) {

      	      		   	   $type = $desc->Type;
	      	      		   $length = null;
	      	      		   $pos = strpos( $desc->Type, '(' );
	
	      	      		   if( $pos !== false ) {
	      	      		   	 
	      	      		   	   $type = preg_match_all( '/^(.*)\((.*)\)$/i', $desc->Type, $matches );
	      	      		   	   
	      	      		   	   $type = $matches[1][0];
	      	      		   	   $length = $matches[2][0];
	      	      		   }

	      	      		   $Column = new Column( null, $Table->getName() );
						   $Column->setName( $desc->Field );
						   $Column->setType( $type );
						   $Column->setLength( $length );

						   if( $desc->Default )
						   	    $Column->setDefault( $desc->Default );

						   if( $desc->NULL == 'NO' )
						   	   $Column->setRequired( true );

						   if( $desc->KEY == 'PRI' )
						   	   $Column->setPrimaryKey( true );

						   if( $desc->Extra == 'auto_increment' )
						   	   $Column->setAutoIncrement( true );
      	      		   
      	      		  	   $Table->addColumn( $Column );
      	      		   }

      	      		   $Database->addTable( $Table );	   
      	      }

      	      return $Database;
	  }
}
?>