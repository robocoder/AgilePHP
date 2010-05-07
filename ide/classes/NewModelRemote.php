<?php

/**
 * Responsible for processing all requests in regards to "New Model" actions.
 * 
 */
class NewModelRemote {

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
	  public function getDatabaseColumns( $tableName ) {

	  		 $columns = array();

	  		 $pm = new PersistenceManager();
	  		 $Database = $pm->reverseEngineer();

	  		 foreach( $Database->getTables() as $table ) {

	  		 	if( $table->getName() == $tableName ) {

		  		 	foreach( $table->getColumns() as $column ) {

				  		 	//$c = array();
				  		 	//$c['name'] = $column->getName();

				  		 	array_push( $columns, $column );
		  		 	}
	  		 	}
	  		 }

	  		 $o = new stdClass;
	  		 
	  		 $renderer = new AJAXRenderer();
	  		 $o->xml = $renderer->toXML( $columns );
	  		 
	  		 return $columns;
	  }
}
?>