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
 * Remoting class responsible for getting configuration values from the database.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.ide.classes
 */
class ConfigsRemote {

	  public function __construct() { }

	  #@RemoteMethod
 	  public function get( $name ) {

 	  		 $config = new Config();
 	  		 $config->setName( $name );

 	  		 $o = new stdClass;
 	  		 $o->value = $config->getValue();

 	  		 return $o;
	  }

	  #@RemoteMethod
 	  public function getConfigs() {

 	  		 $results = array();

 	  		 $pm = new PersistenceManager();
 	  		 $pm->setMaxResults( 50 );

 	  		 $configs = $pm->find( new Config() );

 	  		 foreach( $configs as $config ) {
 	  		 	
 	  		 	$o = new stdClass;
 	  		 	$o->name = $config->getName();
 	  		 	$o->value = $config->getValue();

 	  		 	array_push( $results, $o );
 	  		 }

 	  		 return $results;
	  }
}
?>