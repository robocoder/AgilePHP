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
 * @package com.makeabyte.agilephp.orm
 */

require_once 'orm/dialect/SQLDialect.php';

/**
 * Factory responsible for returning a SQLDialect implementation
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.orm
 */
abstract class ORMFactory {

		 private static $dialect;

		 /**
		  * Returns a SQLDialect singleton instance
		  * 
		  * @param string $ormXml Optional file path to an ORM XML configuration file. Defaults to approot/orm.xml
		  * @return SQLDialect
		  */
	     public static function getDialect( $ormXml = null ) {

	     		 if( self::$dialect == null ) {

		    		 $xml = self::getXml( $ormXml );

		     		 $db = new Database( $xml->database );
	     		 
		     		 self::$dialect = self::connect( $db );
	     		 }

	     		 return self::$dialect;
	     }

	     /**
	      * Replaces the singleton instance with a new SQLDialect according to the specified orm.xml configuration
	      * and returns the instance.
	      * 
	      * @param string $dialect The name of the SQLDialect implementation to create
	      * @param string $ormXml Optional file path to an ORM XML configuration file. Defaults to approot/orm.xml
	      * @return SQLDialect
	      */
	     public static function loadDialect( $ormXml = null ) {

	    		 $xml = self::getXml( $ormXml );

                 $db = new Database( $xml->database );

                 self::$dialect = self::connect( $db );

                 return self::$dialect;
	     }

	     /**
	      * Replaces the singleton instance with a new SQLDialect according to the specified orm.xml configuration
	      * and returns the instance.
	      * 
	      * @param string $dialect The name of the SQLDialect implementation to create
	      * @param string $ormXml Optional file path to an ORM XML configuration file. Defaults to approot/orm.xml
	      * @return SQLDialect
	      */
	     public static function createDialect( $ormXml = null ) {

	    		 $xml = self::getXml( $ormXml );

                 $db = new Database( $xml->database );

                 return self::connect( $db );
	     }

		 /**
	      * Connects to the specified Database.
	      * 
	      * @param Database $db The database object to establish a connection with
	      * @return SQLDialect A dialect instance responsible for the specified database
	      */
	     public static function connect( Database $db ) {

	     		$root = AgilePHP::getFramework()->getFrameworkRoot() . DIRECTORY_SEPARATOR . 'orm' .
	     		 				 DIRECTORY_SEPARATOR . 'dialect' . DIRECTORY_SEPARATOR;

	     		     switch( $db->getType() ) {
	
		  	 			 case 'sqlite':
	  	     		 	  	  require_once $root . 'SQLiteDialect.php';
	  	     		 	  	  return new SQLiteDialect( $db );
		  	     		 break;
	
		  	     	     case 'mysql':
		  	     		 	  require_once $root . 'MySQLDialect.php';
		  	     		 	  return new MySQLDialect( $db );
	  	     		 	 break;
	
	  	     		 	 case 'pgsql':
	  	     		 		  require_once $root . 'PGSQLDialect.php';
	  	     		 		  return new PGSQLDialect( $db );
	  	     		 	 break;
	
	  	     		 	 case 'mssql':
		  	     	       	  require_once $root . 'MSSQLDialect.php';
		  	     	     	  return new MSSQLDialect( $db );
	  	     	     	 break;

	  	     		 	 default:
	  	     		 	 	throw new ORMException( 'Invalid database type' );
	                 }
	     }

	     /**
	      * Destroys the singleton dialect instance.
	      */
	     public static function destroy() {

	     		self::$dialect = null;
	     }
	     
	     /**
	      * Returns orm.xml configuration
	      * 
	      * @param string $ormXml File path to the orm.xml file to load
	      * @return SimpleXMLElement
	      */
		 private static function getXml( $ormXml ) {

		 		 $orm_xml = ($ormXml) ? $ormXml : AgilePHP::getFramework()->getWebRoot() . '/orm.xml';

                 if( !file_exists( $orm_xml ) )
                     throw new ORMException( 'Failed to load orm.xml at \'' . $orm_xml . '\'.' );

                 $xml = simplexml_load_file( $orm_xml );

                 $dom = new DOMDocument();
                 $dom->Load( $orm_xml );
                 if( !$dom->validate() )
                     throw new ORMException( $ormXml . ' Document Object Model validation failed.' );

                 return $xml;
		 }
}
?>