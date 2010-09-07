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
 * @package com.makeabyte.agilephp.orm.dialect
 */

/**
 * Handles PostgreSQL specific queries
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.orm.dialect
 */
final class PGSQLDialect extends BaseDialect implements SQLDialect {

	  private $connectFlag = -1;

	  /**
	   * Initalize PostgreSQLDialect
	   *
	   * @param Database $db The Database object representing orm.xml
	   * @return void
	   */
	  public function __construct(Database $db) {

	  	     try {
	  	     		$this->database = $db;

	  	     		$conn = 'pgsql:' .
	  	     				(($db->getName()) ? 'dbname=' . $db->getName() . ';': '') .
	  	  					(($db->getHostname()) ? 'host=' . $db->getHostname() . ';': '') .
	  	  					(($db->getPort()) ? 'port=' . $db->getPort() . ';' : '') .
	  	  					(($db->getUsername()) ? 'user=' . $db->getUsername() . ';': '') .
	  	  					(($db->getPassword()) ? 'password=' . $db->getPassword() . ';' : '');

	  	  			$this->pdo = new PDO($conn);
	  	  			$this->connectFlag = 1;
	  	     }
	  	     catch(PDOException $pdoe){

	  	     	    Log::debug('PostgreSQLDialect::__construct Warning about \'' . $pdoe->getMessage() . '\'.');

	  	     		// If the database doesnt exist, try a generic connection to the server. This allows the create() method to
	  	     		// be invoked to create the database schema.
	  	     	    if(strpos($pdoe->getMessage(), 'does not exist')) {

	  	     	    	$conn = 'pgsql:' .
	  	  					(($db->getHostname()) ? 'host=' . $db->getHostname() . ';': '') .
	  	  					(($db->getUsername()) ? 'user=' . $db->getUsername() . ';': '') .
	  	  					(($db->getPassword()) ? 'password=' . $db->getPassword() . ';' : '');

	  	     	    	$this->pdo = new PDO($conn);
	  	     	    	$this->connectFlag = 0;
	  	     	    }
	  	     	    else {

	  	     	    	$this->connectFlag = -1;
	  	     	    	throw new ORMException('Failed to create PostgreSQLDialect instance. ' . $pdoe->getMessage());
	  	     	    }
	  	     }

	 	     $this->database = $db;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#isConnected()
	   */
	  public function isConnected() {

	  		 return $this->connectFlag;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#create()
	   */
	  public function create() {

	  		 $this->query('CREATE DATABASE ' . $this->database->getName() . ';');

	  		 // Now that the database is present, connect directly to the database.
	  		 $this->pdo = new PDO('pgsql:host=' . $this->database->getHostname() . ';dbname=' . $this->database->getName(),
	  		 						 $this->database->getUsername(), $this->database->getPassword());

	  		 $constraintFails = array();
	  		 foreach($this->database->getTables() as $table) {

	  		 		  try {
	  		 		  	    $sql = $this->toCreateTableSQL($table);
			   		  		$this->query($sql);
	  		 		  }
	  		 		  catch(ORMException $e) {

	  		 		  		 if(preg_match('/does not exist/', $e->getMessage())) {

	  		 		  		 	 array_push($constraintFails, $sql);
	  		 		  		 	 Log::warn('PGSQLDialect::create Failed to create table \'' . $table->getName() . '\'. Will retry after processing all tables in case this is a foreign key constraint issue due to a table listed further down in orm.xml');
	  		 		  		 	 continue;
	  		 		  		 }

	  		 		  		 Log::debug($e->getMessage());
	  		 		  }
	  		 }

	  		 // Constraint hack continued
	  		 if(count($constraintFails))
	  		 	 foreach($constraintFails as $sql)
	  		 	 		if(!$this->query($sql))
		  		 	 		throw new ORMException(print_r($e, true));
	  }

	  /**
	   * Generates SQL CREATE TABLE for the specified table.
	   *
	   * @param Table $table The table to generate the CREATE TABLE code for
	   * @return string The SQL CREATE TABLE code
	   */
	  private function toCreateTableSQL(Table $table) {

	  		  $defaultKeywords = array('CURRENT_TIMESTAMP');  // Default values that get passed unquoted

  	 		  $sql = 'CREATE TABLE ' . $table->getName() . ' (';

  	 		  foreach($table->getColumns() as $column) {

  	 				   $sql .= $column->getName() . ' ' . $column->getType() .
  	 						   (($column->getLength()) ? '(' . $column->getLength() . ')' : '') .
  	 						   (($column->isRequired() == true && !$column->isAutoIncrement()) ? ' NOT NULL' : '') .
  	 						   (($column->getDefault()) ? ' DEFAULT ' . (in_array($column->getDefault(),$defaultKeywords) ? $column->getDefault() : '\'' . $column->getDefault() . '\'') . '': '') .
  	 						   ((!$column->getDefault() && !$column->isRequired() && !$column->isAutoIncrement()) ? ' DEFAULT NULL' : '') . ', ';
  	 		  }

   			  $pkeyColumns = $table->getPrimaryKeyColumns();
   			  if(count($pkeyColumns)) {

   			  	  $sql .= ' PRIMARY KEY (';
   				  for($i=0; $i<count($pkeyColumns); $i++) {

   					   $sql .= $pkeyColumns[$i]->getName();

   					   if(($i+1) < count($pkeyColumns))
   						   $sql .= ', ';
   				  }
   				  $sql .= ')';
   			  }

	   		  if($table->hasForeignKey()) {

	   		  	  $foreignKeyColumns = $table->getForeignKeyColumns();
	   		  	  for($i=0; $i<count($foreignKeyColumns); $i++) {

	   		  	  	   $fk = $foreignKeyColumns[$i]->getForeignKey();

	   		  	  	   $sql .= ', CONSTRAINT ' . $fk->getName() . ' FOREIGN KEY (' .
	   		  	  	   				$fk->getColumnInstance()->getName() .
   	  	  		       	        ') REFERENCES ' . $fk->getReferencedTable() . ' (' .
   	  	  		    	        $fk->getReferencedColumn() . ') ';

     	  		   	   $sql .= (($fk->getOnUpdate()) ? ' ON UPDATE ' . $fk->getOnUpdate() : '');
     	  		   	   $sql .= (($fk->getOnDelete()) ? ' ON DELETE ' . $fk->getOnDelete() : '');
	   		  	  }
	   		  }

			  $sql .= ');';

	   		  return $sql;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#createTable(Table $table)
	   */
	  public function createTable(Table $table) {

	  		 $this->query($this->toCreateTableSQL($table));
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#dropTable(Table $table)
	   */
	  public function dropTable(Table $table) {

	  		 $this->query('DROP TABLE ' . $table->getName());
	  }

		/**
	     * Attempts to locate the specified model by values. Any fields set in the object are used
	     * in search criteria. Alternatively, setRestrictions and setOrderBy methods can be used to
	     * filter results.
	     *
	     * @param $model A domain model object. Any fields which are set in the object are used to filter results.
	     * @throws ORMException If any primary keys contain null values or any
	     * 		   errors are encountered executing queries
	     */
	    public function find(DomainModel $model) {

	    	   $table = $this->getTableByModel($model);
			   $newModel = $table->getModelInstance();
			   $values = array();

			   Log::debug('BaseDialect::find Performing find on model \'' . $table->getModel() . '\'.');

	  		   try {
	  		   		 if($this->isEmpty($model)) {

	    	   	         $sql = 'SELECT ' . (($this->isDistinct() == null) ? '*' : 'DISTINCT ' . $this->isDistinct()) . ' FROM ' . $table->getName();

	    	   	         $order = $this->getOrderBy();
	    	   	         $offset = $this->getOffset();
	    	   	         $groupBy = $this->getGroupBy();

    	   	         	 $sql .= ($this->getRestrictions() != null) ? $this->createRestrictSQL() : '';
					 	 $sql .= ($order != null) ? ' ORDER BY ' . $order['column'] . ' ' . $order['direction'] : '';
					 	 $sql .= ($groupBy)? ' GROUP BY ' . $this->getGroupBy() : '';
					 	 $sql .= ($offset && $this->getMaxResults()) ? ' LIMIT ' . $offset . ', ' . $this->getMaxResults() : '';
					 	 $sql .= (!$offset && $this->getMaxResults()) ? ' LIMIT ' . $this->getMaxResults() : '';
    	   	         	 $sql .= ';';
	    	   		 }
	    	   		 else {
	    	   		 		$where = '';

	    	   		 		$columns = $table->getColumns();
							for($i=0; $i<count($columns); $i++) {

							 	 if($columns[$i]->isLazy()) continue;

							 	 $accessor = $this->toAccessor($columns[$i]->getModelPropertyName());
						     	 if($model->$accessor() == null) continue;

						     	 $where .= (count($values) ? ' ' . $this->getRestrictionsLogicOperator() . ' ' : ' ') . $columns[$i]->getName() . ' ' . $this->getComparisonLogicOperator() . ' ?';

						     	 if(is_object($model->$accessor())) {

						     	 	 $refAccessor = $this->toAccessor($columns[$i]->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName());

						     	 	 if($transformer = $columns[$i]->getTransformer())
						     	        array_push($values, $transformer::transform($model->$accessor()->$refAccessor()));
						     	     else
				     	 	     	    array_push($values, $model->$accessor()->$refAccessor());
						     	 }
						     	 else {

				     	 	     	 if($transformer = $columns[$i]->getTransformer())
						     	        array_push($values, $transformer::transform($model->$accessor()));
						     	     else
				     	 	     	    array_push($values, $model->$accessor());
						     	 }
						    }

						    $sql = $table->getFind();
						    if($where) {

						       $sql = 'SELECT * FROM ' . $table->getName() . ' WHERE' . $where;
						       $sql .= ' LIMIT ' . $this->getMaxResults() . ';';
						    }
	    	   		 }

	    	   		 $this->setDistinct(null);
	   	         	 $this->setRestrictions(array());
	   	         	 $this->setRestrictionsLogicOperator('AND');
	   	         	 $this->setOrderBy(null, 'ASC');
	   	         	 $this->setGroupBy(null);

					 $this->prepare($sql);
					 $this->PDOStatement->setFetchMode(PDO::FETCH_OBJ);
					 $result = $this->execute($values);

					 if(!count($result)) {

					 	 Log::debug('BaseDialect::find Empty result set for model \'' . $table->getModel() . '\'.');
					 	 return null;
					 }

				 	 $index = 0;
				 	 $models = array();
					 foreach($result as $stdClass) {

					 		  $m = $table->getModelInstance();
					 	   	  foreach(get_object_vars($stdClass) as $name => $value) {

					 	   	  		   $modelProperty = $this->getPropertyNameForColumn($table, $name, true);

							 	   	   // Create foreign model instances from foreign values
						 	 		   foreach($table->getColumns() as $column) {

						 	 		   		    if($column->getName() != $name) continue;
						 	 		   		    if($column->isLazy()) continue;

						 	 		   		    if($renderer = $column->getRenderer())
                        				   	       $value = $renderer::render($value);

                        				   	    if(!$value) continue;

						 	 		  		    if($column->isForeignKey()) {

						 	 		  		   	    $foreignModel = $column->getForeignKey()->getReferencedTableInstance()->getModel();
						 	 		  		   	    $foreignInstance = new $foreignModel();

						 	 		  		   	    $foreignMutator = $this->toMutator($column->getForeignKey()->getReferencedColumnInstance()->getModelPropertyName());
						 	 		  		   	    $foreignInstance->$foreignMutator($value);

						 	 		  		   	    $persisted = $this->find($foreignInstance);

						 	 		  		   	    // php namespace support - remove \ character from fully qualified paths
							 	 		  		   	$foreignModelPieces = explode('\\', $foreignModel);
							 	 		  		   	$foreignClassName = array_pop($foreignModelPieces);

						 	 		  		   	    $instanceMutator = $this->toMutator($modelProperty);
						 	 		  		   	    $m->$instanceMutator($persisted[0]);
						 	 		  		    }
						 	 		  		    else {

						 	 		  		   		$mutator = $this->toMutator($modelProperty);
					 	   	   		  				$m->$mutator($value);
						 	 		  		    }
						 	 		   }
					 	   	  }

					 	   	  array_push($models, $m);
					 	   	  $index++;
					 	   	  if($index == $this->getMaxResults())  break;
				     }

				     return $models;
	  		 }
	  		 catch(Exception $e) {

	  		 		throw new ORMException($e->getMessage(), $e->getCode());
	  		 }
	    }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#drop()
	   */
	  public function drop() {

	  		 $this->pdo = null;

	  		 $conn = 'pgsql:dbname=' . $this->database->getUsername() . ';' .
	  	  					(($this->database->getHostname()) ? 'host=' . $this->database->getHostname() . ';': '') .
	  	  					(($this->database->getUsername()) ? 'user=' . $this->database->getUsername() . ';': '') .
	  	  					(($this->database->getPassword()) ? 'password=' . $this->database->getPassword() . ';' : '');

	  	     $p = new PDO($conn);
  	 	 	 $p->query('DROP DATABASE ' . $this->database->getName() . ';');
  	 	 	 $p = null;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#call(DomainModel $model)
	   */
	  public function call(DomainModel $model, $action = null) {

	  		 $outs = array();
	  		 $params = array();
	  		 $values = array();
	  		 $references = array();
	  		 $class = get_class($model);

	  		 // Assign "default" action (ACTION_DEFAULT)
	  		 $proc = $this->getProcedureByModel($model);
	  		 $procedureName = $proc->getName();
	  		 $parameters = $proc->getParameters();

	  		 switch($action) {

	             case StoredProcedure::ACTION_PERSIST:
	                  $xml = ORMFactory::getConfiguration();
	                  foreach($xml->database->procedure as $procedure) {
	                      if($procedure->attributes()->name == $procedureName) {
	                          if($procedure->persist) {
	                             if(!$query = (string)$procedure->persist) {
	                                 if($reference = $procedure->persist->attributes()->references) {
       	                                 $proc = $this->getProcedureByName($reference);
       	                                 $procedureName = $proc->getName();
      	                                 $parameters = array();
    	                                 foreach($procedure->persist->parameter as $parameter)
    	                                   array_push($parameters, new ProcedureParam($parameter));
	                                 }
	                             }
	                          }
	                      }
	                  }
	             break;

	             case StoredProcedure::ACTION_MERGE:
	                  $xml = ORMFactory::getConfiguration();
	                  foreach($xml->database->procedure as $procedure) {
	                      if($procedure->attributes()->name == $procedureName) {
	                          if($procedure->merge) {
	                             if(!$query = (string)$procedure->merge) {
    	                             if($reference = $procedure->merge->attributes()->references) {
      	                                 $proc = $this->getProcedureByName($reference);
       	                                 $procedureName = $proc->getName();
      	                                 $parameters = array();
    	                                 foreach($procedure->merge->parameter as $parameter)
    	                                   array_push($parameters, new ProcedureParam($parameter));
    	                             }
    	                         }
	                          }
	                      }
	                  }
	             break;

	             case StoredProcedure::ACTION_DELETE:
	                  $xml = ORMFactory::getConfiguration();
	                  foreach($xml->database->procedure as $procedure) {
	                      if($procedure->attributes()->name == $procedureName) {
	                          if($procedure->delete) {
	                             if(!$query = (string)$procedure->delete) {
	                                 if($reference = $procedure->delete->attributes()->references) {
        	                            $proc = $this->getProcedureByName($reference);
       	                                $procedureName = $proc->getName();
          	                            $parameters = array();
        	                            foreach($procedure->delete->parameter as $parameter)
        	                                array_push($parameters, new ProcedureParam($parameter));
    	                             }
	                             }
	                          }
	                      }
	                  }
	             break;

	             case StoredProcedure::ACTION_GET:
	                  $xml = ORMFactory::getConfiguration();
	                  foreach($xml->database->procedure as $procedure) {
	                      if($procedure->attributes()->name == $procedureName) {
	                          if($procedure->get) {
	                             if(!$query = (string)$procedure->get) {
	                                 if($reference = $procedure->get->attributes()->references) {
        	                            $proc = $this->getProcedureByName($reference);
       	                                $procedureName = $proc->getName();
          	                            $parameters = array();
        	                            foreach($procedure->get->parameter as $parameter)
        	                                array_push($parameters, new ProcedureParam($parameter));
    	                             }
	                             }
	                          }
	                      }
	                  }
	             break;

	             case StoredProcedure::ACTION_FIND:
	                  $xml = ORMFactory::getConfiguration();
	                  foreach($xml->database->procedure as $procedure) {
	                      if($procedure->attributes()->name == $procedureName) {
	                          if($procedure->find) {
	                             if(!$query = (string)$procedure->find) {
	                                 if($reference = $procedure->find->attributes()->references) {
        	                            $proc = $this->getProcedureByName($reference);
       	                                $procedureName = $proc->getName();
          	                            $parameters = array();
        	                            foreach($procedure->find->parameter as $parameter)
        	                                array_push($parameters, new ProcedureParam($parameter));
    	                             }
	                             }
	                          }
	                      }
	                  }
	             break;

	             // default:
	             // assigned before switch statement
	         }

	  		 // Parse IN, OUT, & INOUT parameters
	  		 foreach($proc->getParameters() as $param) {

	  		         if($ref = $param->getReference())
	  		            $references[$param->getName()] = $ref;

	  		 		 if($param->getMode() == 'IN' || $param->getMode() == 'INOUT') {

	  		 		    $accessor = $this->toAccessor($param->getModelPropertyName());
	  		 			array_push($params, $model->$accessor());
	  		 		 }

	  		 		 if($param->getMode() == 'OUT' || $param->getMode() == 'INOUT')
	  		 		    $outs[$param->getName()] = $param->getModelPropertyName();
	  		 }

	  		 $query = 'SELECT * FROM ' . $proc->getName() . '(';
	  		 for($i=0; $i<count($params); $i++) {

	  		 		$values[$i] = $params[$i];
	  		 		$query .= '?' . (($i+1) < count($params) ? ', ': '');
	  		 }
	  		 $query .= ');';

	  		 $this->prepare($query);
	  		 $stmt = $this->execute($values);
	  		 $stmt->setFetchMode(PDO::FETCH_ASSOC);
	  		 $results = $stmt->fetchAll();
	  		 $stmt->closeCursor();

	  		 if(!$results) return false;  // nothing to return

	  		 if(count($results) > 1) {

		  		 $models = array();
		 		 foreach($results as $record) {

		 		 		  $m = new $class;
		 		 		  foreach($record as $column => $value) {

		 		 		          if(array_key_exists($column, $outs)) {

		 		 		             $mutator = $this->toMutator($outs[$column]);

		 		 		  		     // References act like table foreign keys - they allow associations to other objects
		 		 		             if(array_key_exists($column, $references))
		 		 		                $value = $this->callReference($column, $references, $outs, $value);

		  		 		  		     $m->$mutator($value);
		 		 		          }
		 		 		  }
		 		 		  array_push($models, $m);
	 		 	 }
	 		 	 return $models;
	  		 }

	  		 foreach($results as $record) {

	  		 		  $m = new $class;
		 		 	  foreach($record as $column => $value) {

                              if(array_key_exists($column, $outs)) {

        	 		 		  	 $mutator = $this->toMutator($outs[$column]);

        	 		 		  	 // References act like table foreign keys - they allow associations to other objects
        	 		 		     if(array_key_exists($column, $references))
        	 		 		        $value = $this->callReference($column, $references, $outs, $value);

        	  		 		  	 $m->$mutator($value);
                              }
		 		      }
		 		 	  return $m;
	  		 }
	  }

	  /**
	   * Returns the total number of records in the specified model.
	   *
	   * @param Object $model The domain object to get the count for.
	   * @return Integer The total number of records in the table.
	   */
	  public function count(DomainModel $model) {

	  		 $sql = 'SELECT count(*) as count FROM ' . $this->getTableByModel($model)->getName();
			 $sql .= ($this->createRestrictSQL() == null) ? '' : $this->createRestrictSQL();
			 $sql .= ';';

	     	 $stmt = $this->query($sql);
  			 $stmt->setFetchMode(PDO::FETCH_OBJ);
  			 $result = $stmt->fetchAll();

  			 return ($result == null) ? 0 : $result[0]->count;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#reverseEngineer()
	   */
	  public function reverseEngineer() {

	  		 /**
	  		  * Organizes table lookup query result set. Result set contains one large
	  		  * array with all tables and column names. This function creates a new
	  		  * associative array with the table name as the key which contains all
	  		  * of its columns underneath.
	  		  *
	  		  * @param array $tables Array of stdClass table objects returned from $tstmt
	  		  * @return array
	  		  */
 	         function organizeTables(array $tables) {

      	     		  $t = array();

      	     		  foreach($tables as $table) {

      	     		  		if(array_key_exists($table->table_name, $t))
      	     		  			array_push($t[$table->table_name], $table);
      	     		  		else
      	     		  			$t[$table->table_name] = array($table);
      	     		  }

      	     		  return $t;
      	     }

	  		 $Database = new Database();
	  		 $Database->setName($this->database->getName());
	  		 $Database->setType($this->database->getType());
	  		 $Database->setHostname($this->database->getHostname());
	  		 $Database->setUsername($this->database->getUsername());
	  		 $Database->setPassword($this->database->getPassword());

	  		 // Get all primary keys
	  		 $pstmt = $this->query('SELECT
										tc.table_name,
										cu.column_name
									 FROM
										information_schema.key_column_usage cu,
										information_schema.table_constraints tc
									 WHERE cu.constraint_name = tc.constraint_name
									 AND cu.table_name = tc.table_name
									 AND tc.constraint_type = \'PRIMARY KEY\'
									 AND tc.table_catalog = \'' . $Database->getName() . '\'
									 AND tc.table_schema = \'public\';');
	  		 $pstmt->setFetchMode(PDO::FETCH_OBJ);
	  		 $pkeys = $pstmt->fetchAll();

	  		 // Get all relationships
	  		 $rstmt = $this->query('SELECT
	  		 							rc.constraint_name,
										tc.table_name,
										kcu.column_name,
										ccu.table_name as referenced_table,
										ccu.column_name as referenced_column,
										rc.update_rule,
										rc.delete_rule
									FROM
										information_schema.key_column_usage kcu,
										information_schema.table_constraints tc,
										information_schema.referential_constraints as rc,
										information_schema.constraint_column_usage as ccu
									WHERE kcu.constraint_name = tc.constraint_name
									AND ccu.constraint_name = kcu.constraint_name
									AND rc.constraint_name = kcu.constraint_name
									AND kcu.table_name = tc.table_name
									AND tc.constraint_type = \'FOREIGN KEY\'
									AND tc.table_catalog = \'' . $Database->getName() . '\'
									AND tc.table_schema = \'public\';');

	  		 $rstmt->setFetchMode(PDO::FETCH_OBJ);
	  		 $relationships = $rstmt->fetchAll();

	  		 // Get all tables
	  		 $tstmt = $this->query('SELECT
	  		 							table_name,
	  		 							column_name,
	  		 							is_nullable,
	  		 							udt_name as type,
	  		 							character_maximum_length as length
	  		 						FROM information_schema.columns
	  		 						WHERE table_schema = \'public\'
	  		 						AND table_catalog = \'' . $Database->getName() . '\'
	  		 						ORDER BY table_name, ordinal_position;');
      	     $tstmt->execute();
      	     $tstmt->setFetchMode(PDO::FETCH_OBJ);
      	     $tables = $tstmt->fetchAll();
      	     $tables = organizeTables($tables);

      	     foreach($tables as $name => $columns) {

      	     		  $Table = new Table();
      	     		  $Table->setName(str_replace(' ', '_', $name));
      	     		  $Table->setModel(ucfirst($name));

      	      		  foreach($columns as $column) {

      	      		  	   $type = ($column->type == 'int4') ? 'serial' : $column->type;

	      	      		   $Column = new Column(null, $Table->getName());
						   $Column->setName($column->column_name);
						   $Column->setType($type);
						   $Column->setLength($column->length);

						   if($column->is_nullable == 'YES') $Column->setRequired(true);

						   foreach($pkeys as $pkey)
						   		if($pkey->table_name == $name && $pkey->column_name == $column->column_name)
						   			$Column->setPrimaryKey(true);

						   if($type == 'serial' || $type == 'bigserial')
						   	   $Column->setAutoIncrement(true);

      	      		  	   foreach($relationships as $fkey) {

									if($fkey->table_name == $Table->getName() &&
										$fkey->column_name == $Column->getName()) {

											$ForeignKey = new ForeignKey(null, $fkey->table_name, $fkey->column_name);
											$ForeignKey->setName($fkey->constraint_name);
											$ForeignKey->setType('one-to-many');
											$ForeignKey->setReferencedTable($fkey->referenced_table);
											$ForeignKey->setReferencedColumn($fkey->referenced_column);
											$ForeignKey->setReferencedController(ucfirst($fkey->referenced_table) . 'Controller');
											$ForeignKey->setOnDelete($fkey->delete_rule);
											$ForeignKey->setOnUpdate($fkey->update_rule);

											$Column->setForeignKey($ForeignKey);
											$Column->setProperty(ucfirst($fkey->referenced_table));
										}
	      	      		   }
      	      		  	   $Table->addColumn($Column);
      	      		   }
      	      		   $Database->addTable($Table);
      	      }
      	      return $Database;
	  }
}
?>