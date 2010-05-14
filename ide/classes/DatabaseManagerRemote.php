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
 * @package com.makeabyte.agilephp.ide.classes
 */

/**
 * Remoting class responsible for server side processing on behalf of Database Manager.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.ide.classes
 */
class DatabaseManagerRemote {

	  public function __construct() { }

	  #@RemoteMethod
	  public function getServers() {

	  		 $boxes = array();

	  		 try {
		  		   $pm = new PersistenceManager();
		  		   $servers = $pm->find( new Server() );

		  		   foreach( $servers as $server ) {

		  		   		$box = array();
		  		 		$box[0] = $server->getId();
		  		 		$box[1] = $server->getIp() . ' (' . $server->getProfile() . ')';

			  		 	array_push( $boxes, $box );
		  		 }

		  		 $o = new stdClass;
		  		 $o->servers = $boxes;

		  		 return $o;
	  		 }
	  		 catch( Exception $e ) {

	  		 	 throw new AgilePHP_RemotingException( $e );
	  		 }
	  }

	  /**
	   * Performs connection test to the database server configured on the "Database" wizard step.
	   * 
	   * @type
	   */
	  #@RemoteMethod
	  public function testDatabaseConnection( $database ) {

	  		 $Database = new Database();
	  		 $Database->setType( $database->type );
	  		 $Database->setDriver( $database->type );
	  		 $Database->setHostname( $database->hostname );
	  		 $Database->setName( $database->name );
	  		 $Database->setUsername( $database->username );
	  		 $Database->setPassword( $database->password );

	  		 $pm = new PersistenceManager();
	  		 $pm->connect( $Database );

	  		 return $pm->isConnected();
	  }
}
?>