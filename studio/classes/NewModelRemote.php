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
 * @package com.makeabyte.agilephp.studio.classes
 */

/**
 * Remoting class responsible for server side processing on behalf of New Model wizard.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
class NewModelRemote {

	  public function __construct() { }

	  /**
	   * Returns a list of database table names for the current database connection.
	   * 
	   * @param stdClass $database A stdClass instance containing client side parameters
	   */
	  #@RemoteMethod
	  public function getDatabaseTables() {

	  		 $tables = array();

	  		 $pm = new PersistenceManager();
	  		 $Database = $pm->reverseEngineer();

	  		 foreach( $Database->getTables() as $table ) {

	  		 	$t = array();
	  		 	$t[0] = $table->getName();

	  		 	array_push( $tables, $t );
	  		 }

	  		 return $tables;
	  }

	  #@RemoteMethod
	  public function getTableColumns( $tableName ) {

	  		 $columns = array();

	  		 $pm = new PersistenceManager();
	  		 $Database = $pm->reverseEngineer();

	  		 foreach( $Database->getTables() as $table ) {

	  		 	if( $table->getName() == $tableName ) {

		  		 	foreach( $table->getColumns() as $column ) {

		  		 			$c = array();
		  		 			$c[0] = $column->getName();

				  		 	array_push( $columns, $c );
		  		 	}
	  		 	}
	  		 }

	  		 return $columns;
	  }

	  #@RemoteMethod
	  public function getTableColumnsMeta( $tableName ) {

	  		 $data = array();

	  		 $pm = new PersistenceManager();
	  		 $Database = $pm->reverseEngineer();

	  		 foreach( $Database->getTables() as $table ) {

	  		 	if( $table->getName() == $tableName ) {

		  		 	foreach( $table->getColumns() as $column ) {

		  		 		    $display = ucfirst( preg_replace( '/[_\-\+\!@#\$%\^&\*\(\)]/', '', $column->getName() ) ); // create default display name

		  		 			$d = array();
		  		 			$d[0] = strtolower( $column->getName() );
		  		 			$d[1] = $column->getName();
		  		 			$d[2] = $column->getDisplay() ? $column->getDisplay() : $display;
		  		 			$d[3] = $column->getType() ? $column->getType() : "";
		  		 			$d[4] = $column->getLength() ? $column->getLength() : 0;
		  		 			$d[5] = $column->getDefault() ? $column->getDefault() : "(null)";
		  		 			$d[6] = $column->isVisible() ? 1 : 0;
		  		 			$d[7] = $column->isRequired() ? 1 : 0;
		  		 			$d[8] = $column->isIndex() ? 1 : 0;
		  		 			$d[9] = $column->isPrimaryKey() ? 1 : 0;
		  		 			$d[10] = $column->isAutoIncrement() ? 1 : 0;
		  		 			$d[11] = 1; // sortable
		  		 			$d[12] = 0; // selectable
		  		 			$d[13] = 1; // sanitize

				  		 	array_push( $data, $d ); 
		  		 	}
	  		 	}
	  		 }

	  		 return $data;
	  }

	  #@RemoteMethod
	  public function getSQLDataTypes() {

	  		 $types = array();
	  		 $values = array( 'boolean', 'integer', 'int', 'bigint', 'double', 'decimal', 'varchar', 'float', 'bit', 'date', 'datetime', 'timestamp',
	                          'blob', 'text', 'password', 'smallint', 'tinyint', 'money', 'char', 'varbinary', 'nvarchar', 'image', 'uniqueidentifier',
	                          'smalldatetime', 'ntext' );

	  		 foreach( $values as $type ) {

		  		 	$t = array();
		  		 	$t[0] = $type;

		  		 	array_push( $types, $t );
	  		 }

	  		 sort( $types );
	  		 return $types;
	  }

	  #@RemoteMethod
	  public function create( $tableName, $workspace, $projectName, $properties, $updatePersistenceDotXml, $createTable ) {


	  		 $workspace = preg_replace( '/\|/', DIRECTORY_SEPARATOR, $workspace );
	  		 $modelName = ucfirst( preg_replace( '/[_\-\+\!@#\$%\^&\*\(\)]/', '', $tableName ) );
	  		 $path = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'model';

	  		 $Table = new Table();
	  		 $Table->setName( $tableName );
	  		 $Table->setModel( $modelName );

	  		 $class = "class " . $modelName . ' {' . PHP_EOL;
	  		 $class .= PHP_EOL . "\tpublic function __construct() { }" . PHP_EOL . PHP_EOL;

	  	 	 for( $i=0; $i<count( $properties ); $i++ ) {

	  		 		  $property = preg_replace( '/[_\-\+\!@#\$%\^&\*\(\)]/', '', $properties[$i][0] );
      	    		  $default = $properties[$i][5];
      	    		  $class .= (isset( $default) && $default != '(null)') ? "\tprivate \$$property = \"$default\";" . PHP_EOL : "\tprivate \$$property;" . PHP_EOL;
	  		 }

	  		 $class .= PHP_EOL;

             foreach( $properties as $key => $value ) {

             		  $property = preg_replace( '/[_\-\+\!@#\$%\^&\*\(\)]/', '', $value[0] );

             		  // Add built-in agilephp persistence interceptors
             		  switch( $property ) {

             		  		case 'id':
             		  			$class .= "\t#@Id" . PHP_EOL;
             		  			break;

             		  		case 'password':
             		  			$class .= "\t#@Password" . PHP_EOL;
             		  			break;
             		  }

             		  $class .= "\tpublic function set" . ucfirst( $property ) . "( \$value ) {" . PHP_EOL . PHP_EOL . "\t\t \$this->$property = \$value;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
             		  $class .= "\tpublic function get" . ucfirst( $property ) . "() {" . PHP_EOL . PHP_EOL . "\t\t return \$this->$property;" . PHP_EOL . "\t}" . PHP_EOL . PHP_EOL;
             }

      	     $class .= '}';

      	     $file = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'model' .
      	     			 DIRECTORY_SEPARATOR . ucfirst( $modelName ) . '.php';
      	     $h = fopen( $file, 'w' );
      	     fwrite( $h, '<?php' . PHP_EOL . PHP_EOL . '/** AgilePHP generated domain model */' . PHP_EOL . PHP_EOL . $class . PHP_EOL . '?>' );
      	     fclose( $h );

      	     // Update persistence.xml
      	     if( $updatePersistenceDotXml ) {

      	     	 $persistence_xml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'persistence.xml';
		  		 if( !file_exists( $persistence_xml ) )
		  		 	 throw new AgilePHP_Exception( 'Could not update persistence.xml. File does not exist at \'' . $persistence_xml . '\'' );

		  	     $xml = simplexml_load_file( $persistence_xml );

	      	     foreach( $xml->database->table as $tableXml ) {

	      		  		  if( (string)$tableXml->attributes()->model == $modelName )
	      		  		   	   throw new AgilePHP_Exception( 'Failed to update persistence.xml. Table element already exists.' );
	      		 }

	      		 $xml = "\t<table name=\"" . $tableName . "\" model=\"" . $modelName . "\">" . PHP_EOL;

      		  	 foreach( $properties as $value ) {

      		  	 		  $Column = new Column( null, $tableName );
      		  	 		  $Column->setProperty( $value[0] );
      		  	 		  $Column->setName( $value[1] );
      		  	 		  $Column->setDisplay( $value[2] );
      		  	 		  $Column->setType( $value[3] );
      	    		  	  $Column->setLength( $value[4] );
      	    		   	  $Column->setDefault( $value[5] );
      	    		   	  $Column->setVisible( $value[6] );
      	    		   	  $Column->setRequired( $value[7] );
      	    		   	  $Column->setIndex( $value[8] );
      	    		   	  $Column->setPrimaryKey( $value[9] );
      	    		   	  $Column->setAutoIncrement( $value[10] );
      	    		   	  $Column->setSortable( $value[11] );
      	    		   	  $Column->setSelectable( $value[12] );
      	    		   	  $Column->setSanitize( $value[13] );

      	    		   	  $Table->addColumn( $Column );

      	    		   	  $xml .= "\t\t\t<column name=\"" . $Column->getName() . "\" type=\"" . $Column->getType() . "\" length=\"" . $Column->getLength() . "\"";

      		  		   	  $xml .= ($Column->getDefault() && $Column->getDefault() != '(null)') ? " default=\"" . $Column->getDefault() . "\"" : '';
      		  		   	  $xml .= $Column->isRequired() ? " required=\"true\"" : '';
      		  		   	  $xml .= (!$Column->isVisible()) ? " visible=\"false\"" : '';
      		  		  	  $xml .= $Column->isIndex() ? " index=\"true\"" : '';
      		  		   	  $xml .= $Column->isPrimaryKey() ? " primaryKey=\"true\"" : '';
      		  		   	  $xml .= $Column->isAutoIncrement() ? " autoIncrement=\"true\"" : '';
      		  		   	  $xml .= (!$Column->isSortable()) ? " sortable=\"false\"" : '';
      		  		   	  $xml .= $Column->isSelectable() ? " selectable=\"true\"" : '';
      		  		   	  $xml .= (!$Column->getSanitize()) ? " sanitize=\"false\"" : '';

      		  		   	  $xml .= '/>' . PHP_EOL;
      		  	 }

      		     $xml .= "\t\t</table>" . PHP_EOL . "\t</database>" . PHP_EOL;

      		     $h = fopen( $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'persistence.xml', 'r' );
      		     $data = '';
      		     while( !feof( $h ) )
      		  		 $data .= fgets( $h, 4096 );
      		     fclose( $h );

      		     $h = fopen( $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR  . 'persistence.xml', 'w' );
      		     fwrite( $h, str_replace( '</database>' . PHP_EOL, $xml, $data ) );
      		     fclose( $h );
      	     }

      	     if( $createTable ) {

      	     	 $pm = new PersistenceManager();
      	     	 $pm->createTable( $Table );
      	     }

      	     return true;
	  }
}
?>