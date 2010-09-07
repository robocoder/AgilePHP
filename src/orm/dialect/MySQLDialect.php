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
 * Handles MySQL specific queries
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.orm.dialect
 */
final class MySQLDialect extends BaseDialect implements SQLDialect {

	  private $connectFlag = -1;

	  /**
	   * Initalize MySQLDialect
	   *
	   * @param Database $db The Database object representing orm.xml
	   * @return void
	   */
	  public function __construct(Database $db) {

	  	     try {
	  	            $parameters = 'mysql:host=' . $db->getHostname() . ';' .
	  	                         (($db->getPort()) ? 'port=' . $db->getPort() . ';' : '') .
	  	                         'dbname=' . $db->getName() . ';';

	  	  			$this->pdo = new PDO($parameters, $db->getUsername(), $db->getPassword());
	  	  			$this->connectFlag = 1;
	  	     }
	  	     catch(PDOException $pdoe) {

	  	     	    Log::debug('MySQLDialect::__construct Warning about \'' . $pdoe->getMessage() . '\'.');

	  	     		// If the database doesnt exist, try a generic connection to the server. This allows the create() method to
	  	     		// be invoked to create the database schema.
	  	     	    if(strpos($pdoe->getMessage(), 'Unknown database')) {

	  	     	    	$this->pdo = new PDO('mysql:host=' . $db->getHostname() . ';', $db->getUsername(), $db->getPassword());
	  	     	    	$this->connectFlag = 0;
	  	     	    }
	  	     	    else {

	  	     	    	$this->connectFlag = -1;
	  	     	    	throw new ORMException('Failed to create MySQLDialect instance. ' . $pdoe->getMessage());
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
	   * @see src/orm/dialect/SQLDialect#call($model)
	   *
	   * @todo this could use some cleaning up
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

	  		 // Execute the stored procedure using each of the IN and INOUT variables
	  		 // in the ordinal position they were specified in orm.xml
	  		 $paramCount = count($params);
  		     $query = 'call ' . $procedureName . '(';
	  		 for($i=0; $i<$paramCount; $i++) {

	  		 		$values[$i] = $params[$i];
	  		 		$query .= '?' . (($i+1) < $paramCount ? ', ': '');
	  		 }
	  		 $query .= ');';

	  		 $this->prepare($query);
	  		 $stmt = $this->execute($values);
	  		 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  		 $stmt->closeCursor();

	  		 if(!$results) return false;  // nothing to return

	  		 // Result set has more than one item
	  		 if(count($results) > 1) {

		  		 $models = array();
		 		 foreach($results as $record) {

		 		 		  $m = new $class;
		 		 		  foreach($record as $column => $value) {

		 		 		          $mutator = $this->toMutator($outs[$column]);

    		 		 		      // References act like table foreign keys - they allow associations to other objects
		 		 		          if(array_key_exists($column, $references))
		 		 		             $value = $this->callReference($column, $references, $outs, $value);

		 		 		          if(!$value) continue;

		  		 		  		  $m->$mutator($value);
		 		 		  }
		 		 		  array_push($models, $m);
	 		 	 }

	 		 	 return $models;
	  		 }

	  		 // Single item returned in the result set
	  		 foreach($results as $record) {

	  		 		  $m = new $class;
		 		 	  foreach($record as $column => $value) {

		 		 		  	   $mutator = $this->toMutator($outs[$column]);

		 		 		  	   // References act like table foreign keys - they allow associations to ther objects
		 		 		       if(array_key_exists($column, $references))
		 		 		          $value = $this->callReference($column, $references, $outs, $value);

		 		 		       if(!$value) continue;

		  		 		  	   $m->$mutator($value);
		 		      }
		 		 	  return $m;
	  		 }
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#create()
	   *
	   * @todo Add engine and charset attributes to orm.xml 'table' element
	   * 	   and assign values from xml definitions. Also need support for dynamic
	   * 	   setting of unique key, fulltext, key, index, etc...
	   */
	  public function create() {

	  		 $this->query('CREATE DATABASE ' . $this->database->getName() . ';');

	  		 // Now that the database is present, connect directly to the database.
	  		 $this->pdo = new PDO('mysql:host=' . $this->database->getHostname() . ';dbname=' . $this->database->getName(),
	  		 						 $this->database->getUsername(), $this->database->getPassword());

			 $this->query('SET foreign_key_checks = 0;');

	  		 foreach($this->database->getTables() as $table)
			   		  $this->query($this->toCreateTableSQL($table));

	  		 $this->query('SET foreign_key_checks = 1;');
	  }

	  /**
	   * (non-PHPdoc)
	   *
	   * @see src/orm/dialect/SQLDialect#createTable(Table $table)
	   */
	  public function createTable(Table $table) {

      	     $this->query($this->toCreateTableSQL($table));
	  }

	  /**
	   * Generates SQL CREATE TABLE for the specified table.
	   *
	   * @param Table $table The table to generate the CREATE TABLE code for
	   * @return string The SQL CREATE TABLE code
	   */
	  private function toCreateTableSQL(Table $table) {

	  		  $defaultKeywords = array('CURRENT_TIMESTAMP');  // Default values that get passed unquoted

  	 		  $sql = 'CREATE TABLE `' . $table->getName() . '` (';

  	 		  foreach($table->getColumns() as $column) {

  	 				   $sql .= '`' . $column->getName() . '` ' . $column->getType() .
  	 						   (($column->getLength()) ? '(' . $column->getLength() . ')' : '') .
  	 						   (($column->isRequired() == true) ? ' NOT NULL' : '') .
  	 						   (($column->isAutoIncrement() === true) ? ' AUTO_INCREMENT' : '') .
  	 						   (($column->getDefault()) ? ' DEFAULT ' . (in_array($column->getDefault(),$defaultKeywords) ? $column->getDefault() : '\'' . $column->getDefault() . '\'') . '': '') .
  	 						   ((!$column->getDefault() && !$column->isRequired()) ? ' DEFAULT NULL' : '') . ', ';
  	 		  }

   			  $pkeyColumns = $table->getPrimaryKeyColumns();
   			  if(count($pkeyColumns)) {

   			  	  $sql .= ' PRIMARY KEY (';
   				  for($i=0; $i<count($pkeyColumns); $i++) {

   					   $sql .= '`' . $pkeyColumns[$i]->getName() . '`';

   						   if(($i+1) < count($pkeyColumns))
   						   	   $sql .= ', ';
   				  }
   				  $sql .= ')';

   				  /*
   				  if(count($pkeyColumns) > 1)
   				  	  $sql .= ', UNIQUE KEY `' . $pkeyColumns[0]->getName() . '` (`' . $pkeyColumns[0]->getName() . '`)';
   				  */
   			  }
   			  else
   			      $sql = substr($sql, 0, -2); // chop off trailing comma due to missing primary key

	   		  if($table->hasForeignKey()) {

	      		  $bProcessedKeys = array();
	   		  	  $foreignKeyColumns = $table->getForeignKeyColumns();
	   		  	  for($h=0; $h<count($foreignKeyColumns); $h++) {

	   		  	  		   $fk = $foreignKeyColumns[$h]->getForeignKey();

   		  	  		       if(in_array($fk->getName(), $bProcessedKeys))
	   		  	  		      continue;

   	  	  	       		   // Get foreign keys which are part of the same relationship
   	  	  	       		   $relatedKeys = $table->getForeignKeyColumnsByKey($fk->getName());

   	  	  	       		   $sql .= ', KEY `' . $fk->getName() . '` (';

   	  	  	       		   for($j=0; $j<count($relatedKeys); $j++) {

   	  	  	       		   		array_push($bProcessedKeys, $relatedKeys[$j]->getName());
   	  	  	       		   		$sql .= '`' . $relatedKeys[$j]->getColumnInstance()->getName() . '`';
   	  	  	       		   		if(($j+1) < count($relatedKeys))
   	  	  	       		   		    $sql .= ', ';
   	  	  	       		   }
   	  	  	       		   $sql .= '), CONSTRAINT `' . $fk->getName() . '`';
       	       		   	 	   $sql .= ' FOREIGN KEY (';
   	  	  		    	   for($j=0; $j<count($relatedKeys); $j++) {

   	  	  	       		   	 	$sql .= '`' . $relatedKeys[$j]->getColumnInstance()->getName() . '`';
   	  	  	       		   		if(($j+1) < count($relatedKeys))
   	  	  	       		   		    $sql .= ', ';
   	  	  	       		   }
						   $sql .= ') REFERENCES `' . $fk->getReferencedTable() . '` (';
   	  	  		    	   for($j=0; $j<count($relatedKeys); $j++) {

       	       		   		 	    $sql .= '`' . $relatedKeys[$j]->getReferencedColumn() . '`';
   	  	  	       		   	    if(($j+1) < count($relatedKeys))
   	  	  	       		   		     $sql .= ', ';
   	  	  		    	   }
   	  	  	       		   $sql .= ') ';
     	  		   			   $sql .= (($fk->getOnUpdate()) ? ' ON UPDATE ' . $fk->getOnUpdate() : '');
     	  		   			   $sql .= (($fk->getOnDelete()) ? ' ON DELETE ' . $fk->getOnDelete() : '');

	   		  	  		   array_push($bProcessedKeys, $fk->getName());
	   		  	  }
	   		  }

   			  $engineType = ($table->hasForeignKey() || $table->hasForeignKeyReferences()) ? 'INNODB' : 'MYISAM';
			  $sql .= ') ENGINE=' . $engineType . ' DEFAULT CHARSET=latin1;';

	   		  return $sql;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#dropTable(Table $table)
	   */
	  public function dropTable(Table $table) {

  	 	 	 $this->query('DROP TABLE ' . $table->getName() . ';');
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#drop()
	   */
	  public function drop() {

  	 	 	 $this->query('DROP DATABASE ' . $this->database->getName() . ';');
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/orm/dialect/SQLDialect#reverseEngineer()
	   */
	  public function reverseEngineer() {

	  		 $Database = new Database();
	  		 $Database->setName($this->database->getName());
	  		 $Database->setType($this->database->getType());
	  		 $Database->setHostname($this->database->getHostname());
	  		 $Database->setUsername($this->database->getUsername());
	  		 $Database->setPassword($this->database->getPassword());

	  		 $stmt = $this->prepare('SHOW TABLES');
      	     $stmt->execute();
      	     $stmt->setFetchMode(PDO::FETCH_OBJ);
      	     $tables = $stmt->fetchAll();

      	     $tblIndex = 'Tables_in_' . $this->getDatabase()->getName();

      	     $fkeyQuery  = 'SELECT
								kcu.CONSTRAINT_NAME AS \'constraint\',
								kcu.TABLE_NAME AS \'table\',
								kcu.COLUMN_NAME AS \'column\',
								kcu.REFERENCED_TABLE_NAME AS \'referenced_table\',
								kcu.REFERENCED_COLUMN_NAME AS \'referenced_column\',
								rc.UPDATE_RULE AS \'update_rule\',
								rc.DELETE_RULE AS \'delete_rule\'
							FROM information_schema.key_column_usage AS kcu
							INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS AS rc
							WHERE kcu.TABLE_SCHEMA = rc.CONSTRAINT_SCHEMA
							  AND kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
							  AND rc.CONSTRAINT_SCHEMA = \'' . $Database->getName() . '\'
							  AND kcu.REFERENCED_TABLE_NAME IS NOT NULL';

      	     $stmt = $this->query($fkeyQuery);
      	     $stmt->setFetchMode(PDO::FETCH_OBJ);
			 $foreignKeys = $stmt->fetchAll();

      	     foreach($tables as $sqlTable) {

      	     		 $Table = new Table();
      	     		 $Table->setName(str_replace(' ', '_', $sqlTable->$tblIndex));
      	     		 $Table->setModel(ucfirst($Table->getName()));

      	      		 $stmt = $this->query('DESC ' . $sqlTable->$tblIndex);
      	      		 $stmt->setFetchMode(PDO::FETCH_OBJ);
      	      		 $descriptions = $stmt->fetchAll();

      	      		 foreach($descriptions as $desc) {

      	      		   	   $type = $desc->Type;
	      	      		   $length = null;
	      	      		   $pos = strpos($desc->Type, '(');

	      	      		   if($pos !== false) {

	      	      		   	   $type = preg_match_all('/^(.*)\((.*)\).*$/i', $desc->Type, $matches);

	      	      		   	   $type = $matches[1][0];
	      	      		   	   $length = $matches[2][0];
	      	      		   }

	      	      		   $Column = new Column(null, $Table->getName());
						   $Column->setName($desc->Field);
						   $Column->setType($type);
						   $Column->setLength($length);

						   if(isset($desc->Default) && $desc->Default)
						   	  $Column->setDefault($desc->Default);

						   if(isset($desc->Null) && $desc->Null == 'NO')
						   	  $Column->setRequired(true);

						   if(isset($desc->Key) && $desc->Key == 'PRI')
						   	  $Column->setPrimaryKey(true);

						   if(isset($desc->Extra) && $desc->Extra == 'auto_increment')
						   	  $Column->setAutoIncrement(true);

	      	      		   foreach($foreignKeys as $fkey) {

									if($fkey->table == $Table->getName() &&
									   $fkey->column == $Column->getName()) {

											$ForeignKey = new ForeignKey(null, $fkey->table, $fkey->column);
											$ForeignKey->setName($fkey->constraint);
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