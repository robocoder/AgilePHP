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
 * @package com.makeabyte.agilephp.persistence.dialect
 */

/**
 * AgilePHP :: MySQLDialect
 * Handles MySQL specific queries
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc.
 * @package com.makeabyte.agilephp.persistence.dialect
 * @version 0.1a
 */
class MySQLDialect extends BasePersistence implements SQLDialect {

	  /**
	   * Initalize MySQLDialect
	   * 
	   * @param Database $db The database to use
	   * @return void
	   */
	  public function __construct( Database $db ) {

	  	     try {
	  	  			$this->pdo = new PDO( 'mysql:host=' . $db->getHostname() . ';dbname=' . $db->getName(), $db->getUsername(), $db->getPassword() );   		
	  	     }
	  	     catch( PDOException $pdoe ){

	  	     	    Logger::getInstance()->debug( 'MySQLDialect::__construct Warning about \'' . $pdoe->getMessage() . '\'.' );

	  	     		// If the database doesnt exist, try a generic connection to the server. This allows the create() method to
	  	     		// be invoked to create the database schema.
	  	     	    if( strpos( $pdoe->getMessage(), 'Unknown database' ) )
	  	     	    	$this->pdo = new PDO( 'mysql:host=' . $db->getHostname() . ';', $db->getUsername(), $db->getPassword() );
	  	     	    else
	  	     	    	throw new AgilePHP_Exception( 'Failed to create MySQLDialect instance. ' . $pdoe->getMessage() );
	  	     }

	 	     $this->database = $db;
	 	     
	  }

	  /**
	   * Creates the database as defined in persistence.xml
	   * 
	   * @todo Add engine and charset attributes to persistence.xml 'table' element
	   * 	   and assign values from xml definitions. Also need support for dynamic
	   * 	   setting of unique key, fulltext, key, index, etc...
	   * 
	   * @return void
	   * @throws AgilePHP_PersistenceException
	   */
	  public function create() {

	  		 $defaultKeywords = array( 'CURRENT_TIMESTAMP' );  // Default values that get passed unquoted

	  		 $this->query( 'CREATE DATABASE ' . $this->database->getName() . ';' );
	  		 
	  		 // Now that the database is present, connect directly to the database.
	  		 $this->pdo = new PDO( 'mysql:host=' . $this->database->getHostname() . ';dbname=' . $this->database->getName(),
	  		 						 $this->database->getUsername(), $this->database->getPassword() );

			 $this->query( 'SET foreign_key_checks = 0;' );

	  		 foreach( $this->database->getTables() as $table ) {

	  		 		  $sql = 'CREATE TABLE `' . $table->getName() . '` ( ';

	  		 		  foreach( $table->getColumns() as $column ) {

	  		 				   $sql .= '`' . $column->getName() . '` ' . $column->getType() . 
	  		 						   (($column->getLength()) ? '(' . $column->getLength() . ')' : '') .
	  		 						   (($column->isRequired() == true) ? ' NOT NULL' : '') .
	  		 						   (($column->isAutoIncrement() === true) ? ' AUTO_INCREMENT' : '') .
	  		 						   (($column->getDefault()) ? ' DEFAULT ' . (in_array($column->getDefault(),$defaultKeywords) ? $column->getDefault() : '\'' . $column->getDefault() . '\'') . '': '') .
	  		 						   ((!$column->getDefault() && !$column->isRequired()) ? ' DEFAULT NULL' : '') . ', ';
	  		 		  }

  	 				  $pkeyColumns = $table->getPrimaryKeyColumns();
  	 				  if( count( $pkeyColumns ) ) {

  	 				  	  $sql .= ' PRIMARY KEY ( ';
	  	 				  for( $i=0; $i<count( $pkeyColumns ); $i++ ) {

	  	 					   $sql .= '`' . $pkeyColumns[$i]->getName() . '`';
	
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

			      		  $bProcessedKeys = array();
			   		  	  $foreignKeyColumns = $table->getForeignKeyColumns();
			   		  	  for( $h=0; $h<count( $foreignKeyColumns ); $h++ ) {

			   		  	  		   $fk = $foreignKeyColumns[$h]->getForeignKey();

		   		  	  		       if( in_array( $fk->getName(), $bProcessedKeys ) )
			   		  	  		       continue;

	   		  	  	       		   // Get foreign keys which are part of the same relationship
	   		  	  	       		   $relatedKeys = $table->getForeignKeyColumnsByKey( $fk->getName() );

	   		  	  	       		   $sql .= ', KEY `' . $fk->getName() . '` ( ';

	   		  	  	       		   for( $j=0; $j<count( $relatedKeys ); $j++ ) {
 
	   		  	  	       		   		array_push( $bProcessedKeys, $relatedKeys[$j]->getName() );
	   		  	  	       		   		$sql .= '`' . $relatedKeys[$j]->getColumnInstance()->getName() . '`';
	   		  	  	       		   		if( ($j+1) < count( $relatedKeys ) )
	   		  	  	       		   		    $sql .= ', ';
	   		  	  	       		   }
	   		  	  	       		   $sql .= ' ), CONSTRAINT `' . $fk->getName() . '`';
   	  	  	       		   	 	   $sql .= ' FOREIGN KEY ( ';
	   		  	  		    	   for( $j=0; $j<count( $relatedKeys ); $j++ ) {
 
	   		  	  	       		   	 	$sql .= '`' . $relatedKeys[$j]->getColumnInstance()->getName() . '`';
	   		  	  	       		   		if( ($j+1) < count( $relatedKeys ) )
	   		  	  	       		   		    $sql .= ', ';
	   		  	  	       		   }
								   $sql .= ' ) REFERENCES `' . $fk->getReferencedTable() . '` ( ';
	   		  	  		    	   for( $j=0; $j<count( $relatedKeys ); $j++ ) {
 
   	  	  	       		   		 	    $sql .= '`' . $relatedKeys[$j]->getReferencedColumn() . '`';
	   		  	  	       		   	    if( ($j+1) < count( $relatedKeys ) )
	   		  	  	       		   		     $sql .= ', ';
	   		  	  		    	   }
	   		  	  	       		   $sql .= ' ) ';
   		  	  		   			   $sql .= (($fk->getOnDelete()) ? ' ON DELETE ' . $fk->getOnDelete() : '' );
   		  	  		   			   $sql .= (($fk->getOnUpdate()) ? ' ON UPDATE ' . $fk->getOnUpdate() : '' );

			   		  	  		   array_push( $bProcessedKeys, $fk->getName() );
			   		  	  }
			   		  }

  	 				  $engineType = ($table->hasForeignKey() || $table->hasForeignKeyReferences()) ? 'INNODB' : 'MYISAM';
					  $sql .= ') ENGINE=' . $engineType . ' DEFAULT CHARSET=latin1;';

			   		  $this->query( $sql );
	  		 }
	  		 $this->query( 'SET foreign_key_checks = 1;' );
	  }

	  /**
	   * Drop/destroy the database.
	   * 
	   * @return void
	   */
	  public function drop() {

  	 	 	 $this->query( 'DROP DATABASE ' . $this->database->getName() . ';' );
	  }
}
?>