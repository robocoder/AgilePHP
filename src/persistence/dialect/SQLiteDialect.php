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
 * Responsible for SQLite specific database operations
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.persistence.dialect
 * @version 0.2a
 */
class SQLiteDialect extends BasePersistence implements SQLDialect {

	  public function __construct( Database $db ) {

	  	     $this->pdo = new PDO( 'sqlite:' . $db->getName() . '.sqlite' );
	 	     $this->database = $db;
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/dialect/SQLDialect#create()
	   */
	  public function create() {

	  		 foreach( $this->database->getTables() as $table ) {

	  		 		$sql = 'CREATE TABLE "' . $table->getName() . '" ( ';

	  			  	$bCandidate = false;

	  		  	    // Compound keys are formatted differently in sqlite
	  	            $pkeyColumns = $table->getPrimaryKeyColumns();
	  		 		if( count( $pkeyColumns ) > 1 ) {

		  		 		foreach( $table->getColumns() as $column ) {

		  		 			     if( $column->isAutoIncrement() )
		  		 			     	 Logger::getInstance()->debug( 'Ignoring autoIncrement="true" for column ' . $column->getName() . '. Sqlite does not support the use of auto-increment with compound primary keys' );

		  		 				 $sql .= '"' . $column->getName() . '" ' . $column->getType() .
		  		 						 (($column->isRequired() == true) ? ' NOT NULL' : '') .
		  		 						 (($column->getDefault()) ? ' DEFAULT \'' . $column->getDefault() . '\'' : '') . ', ';
		  		 		}
		  		 		
		  		 		$sql .= 'PRIMARY KEY ( ';
		  		 		for( $i=0; $i<count( $pkeyColumns ); $i++ ) {

		  		 				 $sql .= '"' . $pkeyColumns[$i]->getName() . '"';

		  		 				 if( ($i+1) < count( $pkeyColumns ) )
		  		 				 	 $sql .= ', ';
		  		 		}
		  		 		$sql .= ' ) );';

	  		 		}
	  		 		else {

		  		 		foreach( $table->getColumns() as $column ) {
	
		  		 			     if( $column->isAutoIncrement() && $column->isPrimaryKey() )
		  		 			     	 $bCandidate = true;
	
		  		 				 $sql .= '"' . $column->getName() . '" ' . $column->getType() . (($column->isPrimaryKey() === true) ? ' PRIMARY KEY' : '') .
		  		 						 (($column->isAutoIncrement() === true) ? ' AUTOINCREMENT' : '') .
		  		 						 (($column->isRequired() == true) ? ' NOT NULL' : '') .
		  		 						 (($column->getDefault()) ? ' DEFAULT \'' . $column->getDefault() . '\'' : '') . ', ';
		  		 		}
	
		  		 		// remove last comma and space
				   		$sql = substr( $sql, 0, -2 );
				   		$sql .= ' );';	
	  		 		}

			   		//if( $bCandidate && (count( $table->getPrimaryKeyColumns() ) > 1) )
			   			//throw new AgilePHP_PersistenceException( 'Sqlite does not allow the use of auto-increment with compound primary keys (' . $table->getName() . ')' );

			   		$this->query( $sql );

	  		 		if( $this->pdo->errorInfo() !== null ) {

	  		 			$info = $this->pdo->errorInfo();
	  		 			if( $info[0] == '0000' )
	  		 				continue;
	  		 			
				  	    throw new AgilePHP_PersistenceException( $info[2], $info[1] );
				  	}
	  		 }
	  }

	  /**
	   * (non-PHPdoc)
	   * @see src/persistence/BasePersistence#truncate($model)
	   */
	  public function truncate( $model ) {

	  	     $table = $this->getTableByModel( $model );
	  		 $this->query( 'DELETE FROM ' . $table->getName() . ';' );
	  }

	  /**
	   * Delete the database.
	   * 
	   * @return void
	   */
	  public function drop() {

  	 	 	 $dbfile = AgilePHP::getFramework()->getWebRoot() . '/' . $this->database->getName() . '.sqlite'; 

  	 	 	 if( !file_exists( $dbfile ) )
  	  	 	 	 throw new AgilePHP_PersistenceException( 'Could not locate sqlite database: ' . $dbfile );

  	  	 	 chmod( $dbfile, 0777 );

  	  	 	 if( !unlink( $dbfile ) )
  		 	 	throw new AgilePHP_PersistenceException( 'Could not drop/delete the sqlite database: ' . $dbfile );
	  }
}
?>