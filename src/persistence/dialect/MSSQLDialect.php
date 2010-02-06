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
 * Responsible for MSSQL specific database operations.
 * (NOTE: This class uses the underlying PDO ODBC driver).
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence.dialect
 * @version 0.1a
 */
class MSSQLDialect extends BasePersistence implements SQLDialect {

	  public function __construct( Database $db ) {

	  	     $this->pdo = new PDO( 'odbc:DRIVER={' . $db->getDriver() . '};SERVER=' . $db->getHostname() . ';DATABASE=' . $db->getName(), 
	  	     			$db->getUsername(), $db->getPassword() );

	 	     $this->database = $db;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#create()
	   */
	  public function create() { }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/BasePersistence#truncate($model)
	   */
	  public function truncate( $model ) {

	  	     $table = $this->getTableByModel( $model );
	  		 $this->query( 'TRUNCATE ' . $table->getName() . ';' );
	  }
	  
	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#drop()
	   */
	  public function drop() {

  	 	 	 $this->query( 'DROP DATABASE ' . $this->getDatabase()->getName() );
	  }

	  /**
	   * Overrides parent find method to provide MSSQL specific TOP command to limit returned result sets.
	   * 
	   * @param $model A domain model object. Any fields which are set in the object are used to filter results.
	   * @throws AgilePHP_PersistenceException If any primary keys contain null values or any
	   * 		   errors are encountered executing queries
	   */
	  public function find( $model ) {

	    	 $table = $this->getTableByModel( $model );
			 $newModel = $table->getModelInstance();

			 Logger::getInstance()->debug( 'BasePersistence::find Performing find on model \'' . $table->getModel() . '\'.' );

	    	 // Perform search on the requested $model parameter
	  		 try {
	  		  	    $pkeyColumns = $table->getPrimaryKeyColumns();
	  		   		if( $this->isEmpty( $model ) ) {

	    	   	        $sql = 'SELECT' . ($this->getMaxResults() ? ' TOP ' . $this->getMaxResults() : '') . 
	    	   	        				   (($this->getDistinct() == null) ? ' *' : 'DISTINCT ' . $this->getDistinct()) . 
	    	   	        		' FROM ' . $table->getName();

	    	   	        $order = $this->getOrderBy();
	    	   	        $offset = $this->getOffset();
	    	   	        $groupBy = $this->getGroupBy();

    	   	         	$sql .= ($this->getRestrictions() != null) ? $this->createRestrictSQL() : '';
					 	$sql .= ($order != null) ? ' ORDER BY ' . $order['column'] . ' ' . $order['direction'] : '';
					 	$sql .= ($groupBy)? ' GROUP BY ' . $this->getGroupBy() : '';
    	   	         	$sql .= ';';

	   	   	         	 $this->setDistinct( null );
    	   	         	 $this->setRestrictions( array() );
    	   	         	 $this->setRestrictionsLogicOperator( 'AND' );
    	   	         	 $this->setOrderBy( null, 'ASC' );
    	   	         	 $this->setGroupBy( null );
	    	   		 }
	    	   		 else {
	    	   		 		 if( !count( $pkeyColumns ) ) return null;

			  		   		 $sql = 'SELECT' . ($this->getMaxResults() ? ' TOP ' . $this->getMaxResults() : '') .
			  		   		 		' * FROM ' . $table->getName() . ' WHERE ';
							 for( $i=0; $i<count( $pkeyColumns ); $i++ ) {

							 	  $accessor = $this->toAccessor( $pkeyColumns[$i]->getModelPropertyName() );
						     	  if( $model->$accessor() == null ) {

								      Logger::getInstance()->debug( 'BasePersistence::find Warning about null primary key for table \'' . $table->getName() . '\' column \'' .
								      					 $pkeyColumns[$i]->getName() . '\'. Primary keys are used in search criteria. Returning null...' );
								      return null;
								  }

						   		  $sql .= $pkeyColumns[$i]->getName() . '=\'' . $model->$accessor() . '\'';
								  $sql .= ( (($i+1) < count( $pkeyColumns ) ) ? ' AND ' : '' );
						     }
	    	   		 }

				     // Execute query
					 $stmt = $this->query( $sql );
					 $stmt->setFetchMode( PDO::FETCH_OBJ );
					 $result = $stmt->fetchall();

					 if( !count( $result ) ) {

					 	 Logger::getInstance()->debug( 'BasePersistence::find Empty result set for model \'' . $table->getModel() . '\'.' );
					 	 return null;
					 }

				 	 $index = 0;
				 	 $models = array();
					 foreach( $result as $stdClass  ) {

					 		  $m = $table->getModelInstance();
					 	   	  foreach( get_object_vars( $stdClass ) as $name => $value ) {

					 	   	  		   if( !$value ) continue;
					 	   	  		   $modelProperty = $this->getPropertyNameForColumn( $table, $name );

							 	   	   // Create foreign model instances from foreign values
						 	 		   foreach( $table->getColumns() as $column ) {

						 	 		  		    if( $column->isForeignKey() && $column->getName() == $name ) {

						 	 		  		   	    $foreignModel = $column->getForeignKey()->getReferencedTableInstance()->getModel();
						 	 		  		   	    $foreignInstance = new $foreignModel();

						 	 		  		   	    $foreignMutator = $this->toMutator( $column->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName() );
						 	 		  		   	    $foreignInstance->$foreignMutator( $value );

						 	 		  		   	    $persisted = $this->find( $foreignInstance );

						 	 		  		   	    $instanceMutator = $this->toMutator( $foreignModel );
						 	 		  		   	    $m->$instanceMutator( $persisted[0] );
						 	 		  		    }
						 	 		  		    else {

						 	 		  		   		$mutator = $this->toMutator( $modelProperty );
					 	   	   		  				$m->$mutator( $value );
						 	 		  		    }
						 	 		   }
					 	   	  }

					 	   	  array_push( $models, $m );
					 	   	  $index++;
					 	   	  if( $index == $this->getMaxResults() )  break;
				     }

				     return $models;
	  		 }
	  		 catch( Exception $e ) {

	  		 		throw new AgilePHP_PersistenceException( $e->getMessage(), $e->getCode() );
	  		 }

	  		 return null;
	  }
	  
	  public function reverseEngineer() {
	  	
	  		 $Database = new Database();
	  		 $Database->setId( $this->database->getId() );
	  		 $Database->setName( $this->database->getName() );
	  		 $Database->setType( $this->database->getType() );
	  		 $Database->setHostname( $this->database->getHostname() );
	  		 $Database->setUsername( $this->database->getUsername() );
	  		 $Database->setPassword( $this->database->getPassword() );

	  		 // Get table names
	  		 $stmt = $this->prepare( 'select * from information_schema.tables;' );
	  		 $stmt->execute();
			 $stmt->setFetchMode( PDO::FETCH_OBJ );
	  		 $stmt->execute();
			 $tables = $stmt->fetchAll();

			 foreach( $tables as $table ) {
			 	
			 		$Table = new Table();
			 		$Table->setName( $table->TABLE_NAME );
			 		$Table->setModel( ucfirst( $table->TABLE_NAME ) );
			 		
			 		$stmt = $this->prepare( 'exec sp_columns ' . $table->TABLE_NAME );
			 		$stmt->execute();
					$stmt->setFetchMode( PDO::FETCH_OBJ );
			  		$stmt->execute();
					$columns = $stmt->fetchAll();
			 		
					foreach( $columns as $column ) {

							$type = preg_match_all( '/^(.*)\\s+(identity).*$/i', $column->TYPE_NAME, $matches );
							$identity = null;

							if( count( $matches ) == 3 && !empty( $matches[1] ) ) {

								$type = $matches[1][0];
	      	      		   		$identity = $matches[2][0];
							}
							else {

								$type = $column->TYPE_NAME;
							}

							$Column = new Column( null, $table->TABLE_NAME );
							$Column->setName( $column->COLUMN_NAME );
							$Column->setType( $type );
							$Column->setLength( $column->LENGTH );
							$Column->setRequired( ($column->IS_NULLABLE == 'YES') ? true : false );
							
							if( $identity )
								$Column->setPrimaryKey( true );
							
							/** @todo Need to work out logic for auto increment */

							$Table->addColumn( $Column );
					}

					$Database->addTable( $Table );
			 }

			 return $Database;
	  }
}
?>